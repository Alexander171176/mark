<?php

namespace App\Traits\Settings;

use App\Http\Requests\Admin\System\Setting\UpdateWidgetPanelRequest;
use App\Models\Admin\System\Setting\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

trait WidgetPanelSettingsTrait
{
    /**
     * Ключ кэша настроек панели виджетов.
     */
    private string $widgetPanelCacheKey = 'widget_panel_settings';

    /**
     * Дефолтный цвет панели виджетов (hex без #).
     */
    private string $widgetPanelDefaultColor = '155E75';

    /**
     * Дефолтная прозрачность панели виджетов (0..1).
     */
    private float $widgetPanelDefaultOpacity = 0.95;

    /**
     * Категория настроек панели виджетов.
     */
    private string $widgetPanelCategory = 'widget_panel';

    /**
     * Получает настройку цвета панели виджетов в админке
     *
     * @return JsonResponse
     */
    public function getWidgetPanelSettings(): JsonResponse
    {
        try {
            $settings = Cache::remember(
                $this->widgetPanelCacheKey,
                self::SETTINGS_CACHE_TTL,
                function () {
                    $color = Setting::where('option', 'widgetHexColor')->value('value') ?? $this->widgetPanelDefaultColor;
                    $opacity = Setting::where('option', 'widgetOpacity')->value('value') ?? $this->widgetPanelDefaultOpacity;

                    $color = $this->normalizeWidgetColor($color);
                    $opacity = $this->normalizeWidgetOpacity($opacity);

                    return ['color' => $color, 'opacity' => $opacity];
                }
            );

            return response()->json($settings);

        } catch (Throwable $e) {
            Log::error('Ошибка получения настроек панели виджетов: ' . $e->getMessage());
            return response()->json([
                'color' => $this->widgetPanelDefaultColor,
                'opacity' => $this->widgetPanelDefaultOpacity,
            ], 500);
        }
    }

    /**
     * Обновляет настройку цвета панели виджетов в админке
     *
     * @param UpdateWidgetPanelRequest $request
     * @return JsonResponse
     */
    public function updateWidgetPanelSettings(UpdateWidgetPanelRequest $request): JsonResponse
    {
        $validated = $request->validated(); // 'color' (hex без #), 'opacity' (float 0-1)

        try {
            DB::beginTransaction();

            // Важно: сохраняем оба значения консистентно
            $this->updateSettingPair(
                'widgetHexColor',
                $validated['color'],
                'string',
                'WIDGET_HEX_COLOR'
            );

            $this->updateSettingPair(
                'widgetOpacity',
                (string) $validated['opacity'],
                'float',
                'WIDGET_OPACITY'
            );

            DB::commit();

            $this->clearSettingsCache($this->widgetPanelCacheKey);
            $this->clearSettingsCache();

            return response()->json([
                'success' => true,
                'message' => 'Настройки панели виджетов обновлены.',
            ]);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Ошибка обновления настроек панели виджетов: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка сохранения настроек панели виджетов.',
            ], 500);
        }
    }

    /**
     * Нормализует цвет (hex без #). Если невалидный — возвращает дефолт.
     *
     * @param mixed $color
     * @return string
     */
    private function normalizeWidgetColor($color): string
    {
        $color = (string) $color;
        return preg_match('/^[0-9A-Fa-f]{6}$/i', $color)
            ? strtoupper($color)
            : $this->widgetPanelDefaultColor;
    }

    /**
     * Нормализует прозрачность (0..1). Если невалидная — возвращает дефолт.
     *
     * @param mixed $opacity
     * @return float
     */
    private function normalizeWidgetOpacity($opacity): float
    {
        if (!is_numeric($opacity)) {
            return $this->widgetPanelDefaultOpacity;
        }

        $opacity = (float) $opacity;

        return ($opacity >= 0 && $opacity <= 1)
            ? $opacity
            : $this->widgetPanelDefaultOpacity;
    }

    /**
     * Универсальный хелпер для сохранения одной настройки в settings.
     * Используем updateOrCreate и общие поля (category/activity).
     *
     * @param string $option
     * @param string $value
     * @param string $type
     * @param string $constant
     * @return void
     */
    private function updateSettingPair(string $option, string $value, string $type, string $constant): void
    {
        Setting::updateOrCreate(
            ['option' => $option],
            [
                'value' => $value,
                'type' => $type,
                'constant' => $constant,
                'category' => $this->widgetPanelCategory,
                'activity' => true,
            ]
        );
    }
}
