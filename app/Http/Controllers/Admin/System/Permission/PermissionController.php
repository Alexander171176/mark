<?php

namespace App\Http\Controllers\Admin\System\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\Permission\PermissionRequest;
use App\Http\Resources\Admin\System\Permission\PermissionResource;
use App\Http\Resources\Admin\System\Permission\PermissionSharedResource;
use App\Models\Admin\System\Permission\AdminPermission as Permission;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

/**
 * Контроллер для управления Разрешениями в административной панели.
 *
 * Предоставляет CRUD операции.
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * @see \Spatie\Permission\Models\Permission Модель Разрешения
 * @see PermissionRequest Запрос для создания/обновления
 */
class PermissionController extends Controller
{
    /**
     * Отображение списка всех Разрешений.
     */
    public function index(Request $request): Response
    {
        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminSystemPermissionsPerPage',
            $settings->int('site_settings.AdminCountPermissions', 6)
        );

        $defaultSort = $settings->string(
            'adminSystemPermissionsDefaultSort',
            $settings->string('site_settings.AdminSortPermissions', 'nameAsc')
        );

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSystemPermissionsProcessingMode',
            'frontend'
        );

        $permissionsCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $permissionsCount,
                300
            );

        try {
            $permissions = $this->getIndexPermissions(
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/System/Permissions/Index', [
                'useServerProcessing' => $useServerProcessing,

                'adminSystemPermissionsProcessingMode' => $processingMode,
                'adminSystemPermissionsPerPage' => $perPage,
                'adminSystemPermissionsDefaultSort' => $defaultSort,

                'adminCountPermissions' => $perPage,
                'adminSortPermissions' => $sortParam,

                'permissions' => PermissionSharedResource::collection($permissions),
                'permissionsCount' => $permissionsCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки разрешений для Index.', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return Inertia::render('Admin/System/Permissions/Index', [
                'useServerProcessing' => $useServerProcessing,

                'adminSystemPermissionsProcessingMode' => $processingMode,
                'adminSystemPermissionsPerPage' => $perPage,
                'adminSystemPermissionsDefaultSort' => $defaultSort,

                'adminCountPermissions' => $perPage,
                'adminSortPermissions' => $sortParam,

                'permissions' => [],
                'permissionsCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки разрешений.',
            ]);
        }
    }

    /**
     * Отображение формы создания нового разрешения.
     */
    public function create(): Response
    {
        // TODO: Проверка прав $this->authorize('create-permissions', Permission::class);

        return Inertia::render('Admin/System/Permissions/Create');
    }

    /**
     * Сохранение нового разрешения в базе данных.
     */
    public function store(PermissionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $permission = DB::transaction(function () use ($data): Permission {
                return Permission::create([
                    'name' => $data['name'],
                    'guard_name' => 'sanctum',
                ]);
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            Log::info('Разрешение успешно создано.', [
                'id' => $permission->id,
                'name' => $permission->name,
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', __('admin/controllers.created_success'));
        } catch (Throwable $e) {
            Log::error('Ошибка при создании разрешения.', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return back()->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Отображение формы редактирования существующего разрешения.
     */
    public function edit(Permission $permission): Response
    {
        // TODO: Проверка прав $this->authorize('edit-permissions', $permission);

        return Inertia::render('Admin/System/Permissions/Edit', [
            'permission' => new PermissionResource($permission),
        ]);
    }

    /**
     * Обновление существующего разрешения в базе данных.
     */
    public function update(PermissionRequest $request, Permission $permission): RedirectResponse
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($permission, $data): void {
                $permission->update([
                    'name' => $data['name'],
                ]);
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            Log::info('Разрешение успешно обновлено.', [
                'id' => $permission->id,
                'name' => $permission->name,
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', __('admin/controllers.updated_success'));
        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении разрешения ID {$permission->id}.", [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return back()->withInput()
                ->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удаление указанного разрешения.
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        // TODO: Проверка прав $this->authorize('delete-permissions', $permission);
        // TODO: Добавить проверку, не является ли разрешение базовым/системным?

        $permissionId = $permission->id;
        $permissionName = $permission->name;

        try {
            DB::transaction(function () use ($permission): void {
                $permission->delete();
            });

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            Log::info('Разрешение удалено.', [
                'id' => $permissionId,
                'name' => $permissionName,
            ]);

            return redirect()->route('admin.permissions.index')
                ->with('success', __('admin/controllers.deleted_success'));
        } catch (Throwable $e) {
            Log::error("Ошибка при удалении разрешения ID {$permissionId}.", [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Базовый запрос разрешений без relations/counts.
     *
     * Используется для определения общего количества записей
     * и выбора режима обработки server/frontend/auto.
     */
    private function baseQuery(): Builder
    {
        return Permission::query();
    }

    /**
     * Запрос для Index.
     *
     * Загружает только данные, необходимые PermissionSharedResource
     * и frontend/server сортировке/поиску.
     */
    private function indexQuery(): Builder
    {
        return $this->baseQuery()
            ->with('roles')
            ->withCount('roles');
    }

    /**
     * Получение списка разрешений для server/frontend режима.
     */
    private function getIndexPermissions(
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
