<?php

namespace App\Http\Controllers\Admin\System\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\Role\RoleRequest;
use App\Http\Resources\Admin\System\Permission\PermissionSharedResource;
use App\Http\Resources\Admin\System\Role\RoleResource;
use App\Http\Resources\Admin\System\Role\RoleSharedResource;
use App\Models\Admin\System\Role\AdminRole as Role;
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
use Spatie\Permission\PermissionRegistrar;
use Throwable;

/**
 * Контроллер для управления Ролями в административной панели.
 *
 * Предоставляет CRUD операции.
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * @see Role Модель Роли
 * @see RoleRequest Запрос для создания/обновления
 */
class RoleController extends Controller
{
    /**
     * Отображение списка ролей.
     */
    public function index(Request $request): Response
    {
        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminSystemRolesPerPage',
            $settings->int('site_settings.AdminCountRoles', 6)
        );

        $defaultSort = $settings->string(
            'adminSystemRolesDefaultSort',
            $settings->string('site_settings.AdminSortRoles', 'nameAsc')
        );

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSystemRolesProcessingMode',
            'frontend'
        );

        $rolesCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $rolesCount,
                300
            );

        try {
            $roles = $this->getIndexRoles(
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/System/Roles/Index', [
                'useServerProcessing' => $useServerProcessing,

                'adminSystemRolesProcessingMode' => $processingMode,
                'adminSystemRolesPerPage' => $perPage,
                'adminSystemRolesDefaultSort' => $defaultSort,

                // Старые props оставляем для совместимости.
                'adminCountRoles' => $perPage,
                'adminSortRoles' => $sortParam,

                'roles' => RoleSharedResource::collection($roles),
                'rolesCount' => $rolesCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки ролей для Index.', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return Inertia::render('Admin/System/Roles/Index', [
                'useServerProcessing' => $useServerProcessing,

                'adminSystemRolesProcessingMode' => $processingMode,
                'adminSystemRolesPerPage' => $perPage,
                'adminSystemRolesDefaultSort' => $defaultSort,

                'adminCountRoles' => $perPage,
                'adminSortRoles' => $sortParam,

                'roles' => [],
                'rolesCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки ролей.',
            ]);
        }
    }

    /**
     * Отображение формы создания новой роли.
     */
    public function create(): Response
    {
        // TODO: Проверка прав $this->authorize('create-roles', Role::class);

        $permissions = Permission::query()
            ->select(['id', 'name', 'guard_name'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/System/Roles/Create', [
            'permissions' => PermissionSharedResource::collection($permissions),
        ]);
    }

    /**
     * Сохранение новой роли.
     */
    public function store(RoleRequest $request): RedirectResponse
    {
        // authorize() выполняется в RoleRequest.
        $data = $request->validated();

        $permissionIds = collect($data['permissions'] ?? [])
            ->pluck('id')
            ->filter()
            ->values()
            ->all();

        try {
            DB::transaction(function () use ($data, $permissionIds): void {
                $role = Role::create([
                    'name' => $data['name'],
                    'guard_name' => 'sanctum',
                ]);

                $role->syncPermissions($permissionIds);

                Log::info('Роль успешно создана.', [
                    'id' => $role->id,
                    'name' => $role->name,
                ]);
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            return redirect()
                ->route('admin.roles.index')
                ->with('success', __('admin/controllers.created_success'));
        } catch (Throwable $e) {
            Log::error('Ошибка при создании роли.', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Отображение формы редактирования существующей роли.
     */
    public function edit(Role $role): Response
    {
        // TODO: Проверка прав $this->authorize('edit-roles', $role);

        $role->load('permissions:id,name');

        $permissions = Permission::query()
            ->select(['id', 'name', 'guard_name'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/System/Roles/Edit', [
            'role' => new RoleResource($role),
            'permissions' => PermissionSharedResource::collection($permissions),
        ]);
    }

    /**
     * Обновление существующей роли.
     */
    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        // authorize() выполняется в RoleRequest.
        $data = $request->validated();

        $permissionIds = collect($data['permissions'] ?? [])
            ->pluck('id')
            ->filter()
            ->values()
            ->all();

        try {
            DB::transaction(function () use ($request, $role, $data, $permissionIds): void {
                $role->update([
                    'name' => $data['name'],
                ]);

                if ($request->has('permissions')) {
                    $role->syncPermissions($permissionIds);
                }
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            Log::info('Роль успешно обновлена.', [
                'id' => $role->id,
                'name' => $role->name,
            ]);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', __('admin/controllers.updated_success'));
        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении роли ID {$role->id}.", [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удаление указанной роли.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // TODO: Проверка прав $this->authorize('delete-roles', $role);

        if ($role->id === 1) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', __('admin/controllers.delete_main_role_error'));
        }

        if (in_array($role->name, ['super-admin', 'owner'], true)) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', __('admin/controllers.delete_base_role_error'));
        }

        try {
            DB::transaction(function () use ($role): void {
                $role->delete();
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            Log::info('Роль удалена.', [
                'id' => $role->id,
                'name' => $role->name,
            ]);

            return redirect()
                ->route('admin.roles.index')
                ->with('success', __('admin/controllers.deleted_success'));
        } catch (Throwable $e) {
            Log::error("Ошибка при удалении роли ID {$role->id}.", [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Лёгкий базовый запрос без relations/counts.
     */
    private function baseQuery(): Builder
    {
        return Role::query();
    }

    /**
     * Запрос данных для Index.
     */
    private function indexQuery(): Builder
    {
        return $this->baseQuery()
            ->with('permissions')
            ->withCount('permissions');
    }

    /**
     * Получение ролей по активному режиму обработки.
     */
    private function getIndexRoles(
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
