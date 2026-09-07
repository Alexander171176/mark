<?php

namespace App\Http\Controllers\Admin\System\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\Setting\UpdateSettingValueRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Resources\Admin\System\Setting\SettingResource;
use App\Models\Admin\System\Setting\Setting;
use App\Traits\Admin\Settings\CountSettingsTrait;
use App\Traits\Admin\Settings\SortSettingsTrait;
use App\Traits\Admin\Settings\UpdatesSettingsTrait;
use App\Traits\Admin\Settings\WidgetPanelSettingsTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

class SettingController extends Controller
{
    use UpdatesSettingsTrait;
    use CountSettingsTrait;
    use SortSettingsTrait;
    use WidgetPanelSettingsTrait;

    /**
     * Админские настройки,
     * разрешённые для интерфейса Settings.
     */
    private const ADMIN_EDITABLE_OPTIONS = [
        'locale',
        'widgetHexColor',
        'widgetOpacity',
    ];

    /**
     * Отображение настроек.
     */
    public function index(): InertiaResponse
    {
        // TODO: Проверка прав
        // $this->authorize('view-settings', Setting::class);

        try {
            $settings = Setting::query()
                ->where(function ($query) {
                    $query
                        ->where(
                            'settings.category',
                            'public'
                        )
                        ->orWhere(function ($query) {
                            $query
                                ->where(
                                    'settings.category',
                                    'admin'
                                )
                                ->whereIn(
                                    'settings.option',
                                    self::ADMIN_EDITABLE_OPTIONS
                                );
                        });
                })
                ->ordered()
                ->get();

            return Inertia::render(
                'Admin/System/Settings/Index',
                [
                    'settings' =>
                        SettingResource::collection($settings),
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки настроек для Settings Index: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/System/Settings/Index',
                [
                    'settings' => [],

                    'error' =>
                        'Не удалось загрузить список настроек.',
                ]
            );
        }
    }

    /**
     * Обновление значения конкретной настройки.
     */
    public function updateValue(
        UpdateSettingValueRequest $request,
        Setting $setting
    ): RedirectResponse {
        try {
            DB::beginTransaction();

            $setting->update([
                'value' =>
                    $request->validated()['value'],
            ]);

            DB::commit();

            Log::info(
                'Значение настройки обновлено.',
                [
                    'id' =>
                        $setting->id,

                    'option' =>
                        $setting->option,

                    'new_value' =>
                        $setting->value,
                ]
            );

            return back()
                ->with(
                    'success',
                    __('admin/controllers.value_updated_success')
                );
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error(
                "Ошибка при обновлении значения настройки ID {$setting->id}: "
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->with(
                    'error',
                    __('admin/controllers.value_updated_error')
                );
        }
    }

    /**
     * Универсальное обновление настройки по option.
     */
    public function updateSettingValue(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'key' => [
                'required',
                'string',
            ],

            'value' => [
                'nullable',
            ],
        ]);

        try {
            DB::beginTransaction();

            $setting = Setting::query()
                ->where(
                    'option',
                    $validated['key']
                )
                ->firstOrFail();

            $setting->update([
                'value' =>
                    $validated['value'],
            ]);

            DB::commit();

            Log::info(
                'Значение настройки обновлено по option.',
                [
                    'id' =>
                        $setting->id,

                    'option' =>
                        $setting->option,

                    'new_value' =>
                        $setting->value,
                ]
            );

            return back()
                ->with(
                    'success',
                    __('admin/controllers.value_updated_success')
                );
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error(
                'Ошибка обновления настройки по option: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->with(
                    'error',
                    __('admin/controllers.value_updated_error')
                );
        }
    }

    /**
     * Обновление статуса активности настройки.
     */
    public function updateActivity(
        UpdateActivityRequest $request,
        Setting $setting
    ): RedirectResponse {
        $validated = $request->validated();

        try {
            $setting->activity =
                $validated['activity'];

            $setting->save();

            $actionText = $setting->activity
                ? 'активирован'
                : 'деактивирован';

            Log::info(
                "Параметр ID {$setting->id} успешно {$actionText}.",
                [
                    'id' =>
                        $setting->id,

                    'option' =>
                        $setting->option,

                    'activity' =>
                        $setting->activity,
                ]
            );

            return back()
                ->with(
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

            return back()
                ->withErrors([
                    'general' =>
                        __('admin/controllers.activity_updated_error'),
                ]);
        }
    }

    /**
     * Массовое обновление активности настроек.
     */
    public function bulkUpdateActivity(
        Request $request
    ): JsonResponse {
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
                'Массово обновлена активность настроек.',
                [
                    'count' =>
                        count($validated['ids']),

                    'activity' =>
                        $validated['activity'],
                ]
            );

            return response()->json([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового обновления активности настроек: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return response()->json(
                [
                    'success' => false,

                    'message' =>
                        __('admin/controllers.bulk_activity_updated_error'),
                ],
                500
            );
        }
    }
}
