<?php

namespace App\Http\Controllers\Admin\System\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\Permission\PermissionRequest;
use App\Http\Resources\Admin\System\Permission\PermissionResource;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Admin\System\Permission\AdminPermission as Permission;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

// Используем
// Модели не нужны напрямую, используем RMB
// Для транзакций (опционально)
// Импортируем модель

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
     * Загружает пагинированный список с сортировкой по настройкам.
     * Передает данные для отображения и настройки пагинации/сортировки.
     * Пагинация и сортировка выполняются на фронтенде.
     *
     * @return Response
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

        $permissionsCount = $this->indexQuery()->count();

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

                'permissions' => PermissionResource::collection($permissions),
                'permissionsCount' => $permissionsCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки разрешений для Index: ' . $e->getMessage(), [
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
     *
     * @return Response
     */
    public function create(): Response
    {
        // TODO: Проверка прав $this->authorize('create-permissions', Permission::class);

        return Inertia::render('Admin/System/Permissions/Create');
    }

    /**
     * Сохранение нового разрешения в базе данных.
     * Использует PermissionRequest для валидации и авторизации.
     *
     * @param PermissionRequest $request
     * @return RedirectResponse Редирект на список статей с сообщением.
     */
    public function store(PermissionRequest $request): RedirectResponse
    {
        // authorize() в PermissionRequest
        $data = $request->validated();

        try {
            DB::beginTransaction();
            Permission::create([
                'name' => $data['name'],
                'guard_name' => 'sanctum',
            ]);
            DB::commit();

            Log::info('Разрешение создано:', ['name' => $data['name']]);
            app()[PermissionRegistrar::class]->forgetCachedPermissions(); // Очистка кэша Spatie
            return redirect()->route('admin.permissions.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании разрешения: " . $e->getMessage());
            return back()->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Отображение формы редактирования существующего разрешения.
     * Использует Route Model Binding для получения модели.
     *
     * @param Permission $permission Модель разрешения, найденная по ID из маршрута.
     * @return Response
     */
    public function edit(Permission $permission): Response // Используем RMB
    {
        // TODO: Проверка прав $this->authorize('edit-permissions', $permission);

        return Inertia::render('Admin/System/Permissions/Edit', [
            'permission' => new PermissionResource($permission),
        ]);
    }

    /**
     * Обновление существующего разрешения в базе данных.
     * Использует PermissionRequest и Route Model Binding.
     *
     * @param PermissionRequest $request Валидированный запрос.
     * @param Permission $permission Модель разрешения для обновления.
     * @return RedirectResponse Редирект на список разрешений с сообщением.
     */
    public function update(PermissionRequest $request, Permission $permission): RedirectResponse // Используем RMB
    {
        // authorize() в PermissionRequest
        $data = $request->validated();

        try {
            DB::beginTransaction();
            // Обновляем только имя, guard обычно не меняют
            $permission->update(['name' => $data['name']]);
            DB::commit();

            Log::info('Разрешение обновлено:', ['id' => $permission->id, 'name' => $permission->name]);
            app()[PermissionRegistrar::class]->forgetCachedPermissions(); // Очистка кэша Spatie
            return redirect()->route('admin.permissions.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении разрешения ID {$permission->id}: " . $e->getMessage());
            return back()
                ->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удаление указанного разрешения.
     * Использует Route Model Binding.
     *
     * @param Permission $permission Модель разрешения для удаления.
     * @return RedirectResponse Редирект на список разрешений с сообщением.
     */
    public function destroy(Permission $permission): RedirectResponse // Используем RMB
    {
        // TODO: Проверка прав $this->authorize('delete-permissions', $permission);
        // TODO: Добавить проверку, не является ли разрешение базовым/системным?

        try {
            DB::beginTransaction();
            $permission->delete(); // Spatie удалит связи из role_has_permissions и model_has_permissions
            DB::commit();

            Log::info('Разрешение удалено:', ['id' => $permission->id, 'name' => $permission->name]);
            app()[PermissionRegistrar::class]->forgetCachedPermissions(); // Очистка кэша Spatie
            return redirect()->route('admin.permissions.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении разрешения ID {$permission->id}: " . $e->getMessage());
            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    private function indexQuery(): Builder
    {
        return Permission::query()
            ->with('roles')
            ->withCount('roles');
    }

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
