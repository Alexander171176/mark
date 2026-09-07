<?php

namespace App\Http\Controllers\Admin\System\Parameter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\Parameter\ParameterRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\System\Setting\SettingResource;
use App\Models\Admin\System\Setting\Setting;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Параметрами системы в административной панели.
 *
 * Предоставляет CRUD операции, а также дополнительные действия:
 * - Обновление активности и сортировки (одиночное и массовое)
 *
 * @version 1.1
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 * @see Setting
 * @see ParameterRequest
 */
class ParameterController extends Controller
{

    /**
     * Отображение списка параметров.
     */
    public function index(Request $request): Response
    {
        $currentLocale = app()->getLocale();
        $settingsService = app(AdminSettingsService::class);

        $perPage = $settingsService->int(
            'adminSystemSettingsPerPage',
            6
        );

        $defaultSort = $settingsService->string(
            'adminSystemSettingsDefaultSort',
            'idDesc'
        );

        $processingMode = $settingsService->string(
            'adminSystemSettingsProcessingMode',
            'frontend'
        );

        $sortParam = (string) $request->query(
            'sort',
            $defaultSort
        );

        $search = trim(
            (string) $request->query('search', '')
        );

        $settingsCount = $this->indexQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $settingsCount,
                300
            );

        try {
            $settings = $this->getIndexSettings(
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render(
                'Admin/System/Parameters/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSystemSettingsPerPage' =>
                        $perPage,

                    'adminSystemSettingsDefaultSort' =>
                        $defaultSort,

                    'adminSystemSettingsProcessingMode' =>
                        $processingMode,

                    'settings' =>
                        SettingResource::collection($settings),

                    'settingsCount' =>
                        $settingsCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки параметров для Index: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/System/Parameters/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSystemSettingsPerPage' =>
                        $perPage,

                    'adminSystemSettingsDefaultSort' =>
                        $defaultSort,

                    'adminSystemSettingsProcessingMode' =>
                        $processingMode,

                    'settings' => [],

                    'settingsCount' => 0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'error' =>
                        'Ошибка загрузки параметров.',
                ]
            );
        }
    }

    /**
     * Отображение формы создания параметра.
     */
    public function create(): Response
    {
        return Inertia::render(
            'Admin/System/Parameters/Create'
        );
    }

    /**
     * Сохранение нового параметра.
     */
    public function store(
        ParameterRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        try {
            $setting = Setting::create($data);

            Log::info(
                'Параметр системы успешно создан.',
                [
                    'id' =>
                        $setting->id,

                    'option' =>
                        $setting->option,
                ]
            );

            return redirect()
                ->route('admin.parameters.index')
                ->with(
                    'success',
                    __('admin/controllers.created_success')
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании параметра: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    __('admin/controllers.created_error')
                );
        }
    }

    /**
     * Отображение формы редактирования параметра.
     */
    public function edit(
        Setting $parameter
    ): Response {
        return Inertia::render(
            'Admin/System/Parameters/Edit',
            [
                'setting' =>
                    new SettingResource($parameter),
            ]
        );
    }

    /**
     * Обновление параметра.
     */
    public function update(
        ParameterRequest $request,
        Setting $parameter
    ): RedirectResponse {
        try {
            $parameter->update(
                $request->validated()
            );

            Log::info(
                'Параметр системы обновлён.',
                [
                    'id' =>
                        $parameter->id,

                    'option' =>
                        $parameter->option,
                ]
            );

            return redirect()
                ->route('admin.parameters.index')
                ->with(
                    'success',
                    __('admin/controllers.updated_success')
                );
        } catch (Throwable $e) {
            Log::error(
                "Ошибка при обновлении параметра ID {$parameter->id}: "
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    __('admin/controllers.updated_error')
                );
        }
    }

    /**
     * Удаление параметра.
     */
    public function destroy(
        Setting $parameter
    ): RedirectResponse {
        try {
            $parameterId = $parameter->id;

            $parameter->delete();

            Log::info(
                "Параметр системы удалён: ID {$parameterId}"
            );

            return redirect()
                ->route('admin.parameters.index')
                ->with(
                    'success',
                    __('admin/controllers.deleted_success')
                );
        } catch (Throwable $e) {
            Log::error(
                "Ошибка при удалении параметра ID {$parameter->id}: "
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->with(
                    'error',
                    __('admin/controllers.deleted_error')
                );
        }
    }

    /**
     * Обновление активности одного параметра.
     */
    public function updateActivity(
        UpdateActivityRequest $request,
        Setting $setting
    ): RedirectResponse {
        try {
            $setting->activity =
                $request->validated('activity');

            $setting->save();

            $actionText = $setting->activity
                ? 'активирован'
                : 'деактивирован';

            Log::info(
                "Параметр ID {$setting->id} успешно {$actionText}."
            );

            return back()->with(
                'success',
                __(
                    'admin/controllers.activity_updated_success',
                    [
                        'option' =>
                            $setting->option,

                        'action' =>
                            $actionText,
                    ]
                )
            );
        } catch (Throwable $e) {
            Log::error(
                "Ошибка обновления активности параметра ID {$setting->id}: "
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                __('admin/controllers.activity_updated_error')
            );
        }
    }

    /**
     * Массовое обновление активности параметров.
     */
    public function bulkUpdateActivity(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
            ],

            'ids.*' => [
                'required',
                'integer',
                'exists:settings,id',
            ],

            'activity' => [
                'required',
                'boolean',
            ],
        ]);

        try {
            Setting::query()
                ->whereIn(
                    'settings.id',
                    $validated['ids']
                )
                ->update([
                    'activity' =>
                        $validated['activity'],
                ]);

            Log::info(
                'Массово обновлена активность параметров.',
                [
                    'count' =>
                        count($validated['ids']),

                    'activity' =>
                        $validated['activity'],
                ]
            );

            return back()->with(
                'success',
                __(
                    'admin/controllers.bulk_activity_updated_success'
                )
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового обновления активности параметров: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                __(
                    'admin/controllers.bulk_activity_updated_error'
                )
            );
        }
    }

    /**
     * Обновление сортировки одного параметра.
     */
    public function updateSort(
        UpdateSortEntityRequest $request,
        Setting $setting
    ): RedirectResponse {
        try {
            $setting->sort =
                $request->validated('sort');

            $setting->save();

            Log::info(
                "Обновлено sort параметра ID {$setting->id} "
                . "на {$setting->sort}."
            );

            return back()->with(
                'success',
                __(
                    'admin/controllers.sort_updated_success'
                )
            );
        } catch (Throwable $e) {
            Log::error(
                "Ошибка обновления сортировки параметра ID {$setting->id}: "
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                __(
                    'admin/controllers.sort_updated_error'
                )
            );
        }
    }

    /**
     * Массовое обновление сортировки параметров.
     */
    public function updateSortBulk(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'settings' => [
                'required',
                'array',
            ],

            'settings.*.id' => [
                'required',
                'integer',
                'exists:settings,id',
            ],

            'settings.*.sort' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        try {
            DB::transaction(
                function () use ($validated): void {
                    foreach (
                        $validated['settings']
                        as $settingData
                    ) {
                        Setting::query()
                            ->where(
                                'settings.id',
                                $settingData['id']
                            )
                            ->update([
                                'sort' =>
                                    $settingData['sort'],
                            ]);
                    }
                }
            );

            Log::info(
                'Массово обновлена сортировка параметров.',
                [
                    'count' =>
                        count($validated['settings']),
                ]
            );

            return back()->with(
                'success',
                __(
                    'admin/controllers.bulk_sort_updated_success'
                )
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового обновления сортировки параметров: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                __(
                    'admin/controllers.bulk_sort_updated_error'
                )
            );
        }
    }

    /**
     * Базовый запрос для списка параметров.
     */
    private function indexQuery(): Builder
    {
        return Setting::query();
    }

    /**
     * Получение параметров по активному режиму обработки.
     */
    private function getIndexSettings(
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
    ): LengthAwarePaginator|EloquentCollection {
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
