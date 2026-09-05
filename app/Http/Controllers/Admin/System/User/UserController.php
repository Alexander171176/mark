<?php

namespace App\Http\Controllers\Admin\System\User;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\User\StoreUserRequest;
use App\Http\Requests\Admin\System\User\UpdateUserRequest;
use App\Http\Resources\Admin\System\Permission\PermissionSharedResource;
use App\Http\Resources\Admin\System\Role\RoleSharedResource;
use App\Http\Resources\Admin\System\User\UserResource;
use App\Http\Resources\Admin\System\User\UserSharedResource;
use App\Models\User;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

class UserController extends Controller
{
    use PasswordValidationRules;

    public function index(Request $request): Response
    {
        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminSystemUsersPerPage',
            $settings->int('site_settings.AdminCountUsers', 6)
        );

        $defaultSort = $settings->string(
            'adminSystemUsersDefaultSort',
            $settings->string('site_settings.AdminSortUsers', 'idDesc')
        );

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSystemUsersProcessingMode',
            'frontend'
        );

        /** Лёгкий COUNT без eager loading и withCount. */
        $usersCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $usersCount,
                300
            );

        try {
            $users = $this->getIndexUsers(
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/System/Users/Index', [
                'useServerProcessing' => $useServerProcessing,

                'adminSystemUsersProcessingMode' => $processingMode,
                'adminSystemUsersPerPage' => $perPage,
                'adminSystemUsersDefaultSort' => $defaultSort,

                // Старые props оставляем для совместимости.
                'adminCountUsers' => $perPage,
                'adminSortUsers' => $sortParam,

                'users' => UserSharedResource::collection($users),
                'usersCount' => $usersCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки пользователей.', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return Inertia::render('Admin/System/Users/Index', [
                'useServerProcessing' => $useServerProcessing,

                'adminSystemUsersProcessingMode' => $processingMode,
                'adminSystemUsersPerPage' => $perPage,
                'adminSystemUsersDefaultSort' => $defaultSort,

                'adminCountUsers' => $perPage,
                'adminSortUsers' => $sortParam,

                'users' => [],
                'usersCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки пользователей.',
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        // TODO: Проверка прав доступа $this->authorize('create-users', User::class);

        $roles = $this->rolesForSelect();
        $permissions = $this->permissionsForSelect();

        return Inertia::render('Admin/System/Users/Create', [
            'roles' => RoleSharedResource::collection($roles),
            'permissions' => PermissionSharedResource::collection($permissions),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data): void {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                ]);

                $roleNames = collect($data['roles'] ?? [])->pluck('name')->toArray();
                $permissionNames = collect($data['permissions'] ?? [])->pluck('name')->toArray();

                $user->syncRoles($roleNames);
                $user->syncPermissions($permissionNames);
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return redirect()->route('admin.users.index')
                ->with('success', __('admin/controllers.created_success'));
        } catch (Throwable $e) {
            Log::error('Ошибка при создании пользователя.', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return back()->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        // TODO: Проверка прав доступа $this->authorize('update-users', $user);

        $user->load([
            'roles',
            'permissions',
        ]);

        $roles = $this->rolesForSelect();
        $permissions = $this->permissionsForSelect();

        return Inertia::render('Admin/System/Users/Edit', [
            'user' => new UserResource($user),
            'roles' => RoleSharedResource::collection($roles),
            'permissions' => PermissionSharedResource::collection($permissions),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($user, $data): void {
                $roleNames = collect($data['roles'] ?? [])->pluck('name')->toArray();
                $permissionNames = collect($data['permissions'] ?? [])->pluck('name')->toArray();

                $user->syncRoles($roleNames);
                $user->syncPermissions($permissionNames);

                $user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                ]);
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return redirect()->route('admin.users.index')
                ->with('success', __('admin/controllers.updated_success'));
        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении пользователя ID {$user->id}.", [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return back()->withInput()
                ->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // TODO: Проверка прав доступа $this->authorize('delete-users', $user);

        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', __('admin/controllers/users.cannot_delete_superadmin'));
        }

        if ($user->id === 1) {
            return redirect()->route('admin.users.index')
                ->with('error', __('admin/controllers/users.cannot_delete_main_admin'));
        }

        $userRoleNames = $user->roles->pluck('name');

        if ($userRoleNames->count() === 1 && $userRoleNames->first() === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', __('admin/controllers/users.cannot_delete_single_admin'));
        }

        $userId = $user->id;

        try {
            DB::transaction(function () use ($user): void {
                $user->syncRoles([]);
                $user->syncPermissions([]);
                $user->delete();
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            Log::info('Пользователь удалён.', [
                'id' => $userId,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', __('admin/controllers.deleted_success'));
        } catch (Throwable $e) {
            Log::error("Ошибка при удалении пользователя ID {$userId}.", [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /** Лёгкий базовый запрос без связей и счётчиков. */
    private function baseQuery(): Builder
    {
        return User::query();
    }

    /** Запрос для Admin Index. */
    private function indexQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'roles',
                'permissions',
            ])
            ->withCount([
                'roles',
                'permissions',
            ]);
    }

    /** Компактный список ролей для Create/Edit. */
    private function rolesForSelect()
    {
        return Role::query()
            ->select([
                'roles.id',
                'roles.name',
                'roles.guard_name',
            ])
            ->orderBy('roles.name')
            ->orderByDesc('roles.id')
            ->get();
    }

    /** Компактный список разрешений для Create/Edit. */
    private function permissionsForSelect()
    {
        return Permission::query()
            ->select([
                'permissions.id',
                'permissions.name',
                'permissions.guard_name',
            ])
            ->orderBy('permissions.name')
            ->orderByDesc('permissions.id')
            ->get();
    }

    /** Получение пользователей по активному режиму обработки. */
    private function getIndexUsers(
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
    ) {
        $query = $this->indexQuery();

        if ($useServerProcessing) {
            return $query
                ->search($search)
                ->sortByParam($sort)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->sortByParam($sort)
            ->get();
    }
}
