<?php

namespace App\Http\Controllers\Admin\System\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\Role\RoleRequest;
use App\Http\Resources\Admin\System\Role\RoleResource;
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
use App\Models\Admin\System\Role\AdminRole as Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

// Используем
// Для транзакций

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
     * Отображение списка всех Ролей.
     * Загружает пагинированный список с сортировкой по настройкам.
     * Передает данные для отображения и настройки пагинации/сортировки.
     * Пагинация и сортировка выполняются на фронтенде.
     *
     * @param Request $request
     * @return Response
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

        $rolesCount = $this->indexQuery()->count();

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

                // старые props оставляем для совместимости
                'adminCountRoles' => $perPage,
                'adminSortRoles' => $sortParam,

                'roles' => RoleResource::collection($roles),
                'rolesCount' => $rolesCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки ролей для Index: ' . $e->getMessage(), [
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
     *
     * @return Response
     */
    public function create(): Response
    {
        // TODO: Проверка прав $this->authorize('create-roles', Role::class);

        // Загружаем только ID и имя разрешений
        $permissions = Permission::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Admin/System/Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Сохранение новой роли в базе данных.
     * Использует PermissionRequest для валидации и авторизации.
     *
     * @param RoleRequest $request
     * @return RedirectResponse Редирект на список статей с сообщением.
     */
    public function store(RoleRequest $request): RedirectResponse
    {
        // authorize() в RoleRequest
        $data = $request->validated();

        // Получаем ID разрешений (реквест уже проверил их существование)
        $permissionIds = collect($data['permissions'] ?? [])->pluck('id')->toArray();

        try {
            DB::beginTransaction();
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'sanctum',
            ]);
            $role->syncPermissions($permissionIds); // Синхронизируем по ID
            DB::commit();

            Log::info('Роль успешно создана:', ['id' => $role->id, 'name' => $role->name]);
            return redirect()->route('admin.roles.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании роли: " . $e->getMessage());
            return back()->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Отображение формы редактирования существующей роли.
     * Использует Route Model Binding для получения модели.
     *
     * @param Role $role Модель роли, найденная по ID из маршрута.
     * @return Response
     */
    public function edit(Role $role): Response // Используем RMB
    {
        // TODO: Проверка прав $this->authorize('edit-roles', $role);

        // Загружаем разрешения, назначенные этой роли
        $role->load('permissions:id,name'); // Загружаем только id и name

        // Загружаем все разрешения для списка выбора
        $permissions = Permission::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('Admin/System/Roles/Edit', [
            'role' => new RoleResource($role),
            'permissions' => $permissions, // Передаем коллекцию для выбора
        ]);
    }

    /**
     * Обновление существующей роли в базе данных.
     * Использует RoleRequest и Route Model Binding.
     *
     * @param RoleRequest $request Валидированный запрос.
     * @param Role $role Модель разрешения для обновления.
     * @return RedirectResponse Редирект на список разрешений с сообщением.
     */
    public function update(RoleRequest $request, Role $role): RedirectResponse // Используем RMB
    {
        // authorize() в RoleRequest
        $data = $request->validated();
        $permissionIds = collect($data['permissions'] ?? null)->pluck('id')->toArray();

        try {
            $role->update(['name' => $data['name']]); // Обновляем только имя (guard обычно не меняют)

            // Синхронизируем разрешения, только если массив permissions передан
            if ($request->has('permissions')) {
                $role->syncPermissions($permissionIds);
            }

            Log::info('Роль обновлена:', ['id' => $role->id, 'name' => $role->name]);

            // Очищаем кэш разрешений Spatie
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            return redirect()->route('admin.roles.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении роли ID {$role->id}: " . $e->getMessage());
            return back()->withInput()
                ->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удаление указанной роли.
     * Использует Route Model Binding.
     *
     * @param Role $role Модель роли для удаления.
     * @return RedirectResponse Редирект на список ролей с сообщением.
     */
    public function destroy(Role $role): RedirectResponse // Используем RMB
    {
        // TODO: Проверка прав $this->authorize('delete-roles', $role);
        // TODO: Добавить проверку, нельзя ли удалить базовые роли (super-admin)

        if ($role->id === 1) {
            return redirect()->route('admin.roles.index')
                ->with('error', __('admin/controllers.delete_main_role_error'));
        }
        if (in_array($role->name, ['super-admin', 'owner'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', __('admin/controllers.delete_base_role_error'));
        }
        // TODO: Проверить, назначена ли роль пользователям? Запретить удаление или отсоединить?
        // if ($role->users()->count() > 0) { ... }

        try {
            DB::beginTransaction();
            $role->delete(); // Spatie удалит связи в role_has_permissions и model_has_roles
            DB::commit();

            Log::info('Роль удалена:', ['id' => $role->id, 'name' => $role->name]);
            // Очищаем кэш разрешений Spatie
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            return redirect()->route('admin.roles.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении роли ID {$role->id}: " . $e->getMessage());
            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /** Базовый запрос для списка ролей. */
    private function indexQuery(): Builder
    {
        return Role::query()
            ->with('permissions')
            ->withCount('permissions');
    }

    /** Получение ролей по активному режиму обработки. */
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
