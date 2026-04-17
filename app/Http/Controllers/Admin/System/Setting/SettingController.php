<?php

namespace App\Http\Controllers\Admin\System\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\System\Setting\UpdateSettingValueRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Resources\Admin\System\Setting\SettingResource;
use App\Models\Admin\System\Setting\Setting;
use App\Traits\Settings\ClearsSettingsCacheTrait;
use App\Traits\Settings\CountSettingsTrait;
use App\Traits\Settings\SortSettingsTrait;
use App\Traits\Settings\UpdatesSettingsTrait;
use App\Traits\Settings\WidgetPanelSettingsTrait;
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
    // Время кэширования специфичных настроек
    private const SETTINGS_CACHE_TTL = 3600; // 1 час

    use UpdatesSettingsTrait;
    use CountSettingsTrait;
    use SortSettingsTrait;
    use WidgetPanelSettingsTrait;
    use ClearsSettingsCacheTrait;

    // --- Стандартные CRUD методы ---

    /**
     * Отображение списка всех настроек.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        // TODO: Проверка прав $this->authorize('view-settings', Setting::class);

        // Получаем настройки для фронтенда (дефолтные значения)
        $adminCountSettings = config('site_settings.AdminCountSettings', 15); // Для ItemsPerPageSelect
        $adminSortSettings  = config('site_settings.AdminSortSettings', 'idDesc'); // Для SortSelect

        try {
            // Загружаем ВСЕ рубрики с количеством секций (или без, если не нужно в таблице)
            $settings = Setting::all(); // Загружаем ВСЕ
            $settingsCount = $settings->count(); // Считаем из загруженной коллекции

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки рубрик для Index: " . $e->getMessage());
            $settings = collect();
            $settingsCount = 0;
            session()->flash('error', 'Не удалось загрузить список параметров.');
        }

        return Inertia::render('Admin/System/Settings/Index', [
            // Передаем ПОЛНУЮ коллекцию ресурсов
            'settings' => SettingResource::collection($settings),
            'settingsCount' => $settingsCount,
            // Передаем дефолтные/текущие настройки для инициализации фронтенда
            'adminCountSettings' => (int)$adminCountSettings,
            'adminSortSettings' => $adminSortSettings, // Это значение прочитает SortSelect при загрузке
        ]);
    }

    /**
     * Обновление значения конкретной настройки.
     *
     * @param UpdateSettingValueRequest $request
     * @param Setting $setting
     * @return RedirectResponse
     */
    public function updateValue(UpdateSettingValueRequest $request, Setting $setting): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $setting->update([
                'value' => $request->validated()['value'],
            ]);

            DB::commit();

            Log::info('Значение настройки обновлено', [
                'id' => $setting->id,
                'option' => $setting->option,
                'new_value' => $setting->value,
            ]);

            return back()
                ->with('success', __('admin/controllers.value_updated_success'));

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при обновлении значения настройки ID
            {$setting->id}: {$e->getMessage()}");

            return back()
                ->with('error', __('admin/controllers.value_updated_error'));
        }
    }

    /**
     * Обновление статуса активности параметра.
     *
     * @param UpdateActivityRequest $request
     * @param Setting $setting
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, Setting $setting): RedirectResponse
    {
        $validated = $request->validated();

        if (in_array($setting->category, ['system', 'admin', 'public'], true)) {
            Log::info("Попытка изменения активности параметра ID {$setting->id} с категорией '{$setting->category}'.");

            return back()
                ->with('warning', __('admin/controllers.activity_update_forbidden_error', [
                    'category' => $setting->category,
                ]));
        }

        try {
            $setting->activity = $validated['activity'];
            $setting->save();

            $actionText = $setting->activity ? 'активирован' : 'деактивирован';
            Log::info("Параметр ID {$setting->id} успешно {$actionText}");

            return back()
                ->with('success', __('admin/controllers.activity_updated_success', [
                    'option' => $setting->option,
                    'action' => $actionText,
                ]));
        } catch (Throwable $e) {
            Log::error("Ошибка обновления активности параметра ID {$setting->id}: "
                . $e->getMessage());

            return back()->withErrors([
                'general' => __('admin/controllers.activity_updated_error'),
            ]);
        }
    }

    /**
     * Обновление статуса активности массово
     *
     * @param Request $request
     * @return JsonResponse Json ответ
     */
    public function bulkUpdateActivity(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'required|integer|exists:settings,id',
            'activity' => 'required|boolean',
        ]);

        Setting::whereIn('id', $data['ids'])->update(['activity' => $data['activity']]);

        return response()->json(['success' => true]);
    }
}
