<?php

namespace App\Traits\Admin\Settings;

use App\Http\Requests\Admin\System\Setting\UpdateWidgetPanelRequest;
use App\Models\Admin\System\Setting\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

trait WidgetPanelSettingsTrait
{
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
     * Получает настройки панели виджетов в админке.
     *
     * @return JsonResponse
     */
    public function getWidgetPanelSettings(): JsonResponse
    {
        try {
            $color = Setting::query()
                ->where('option', 'widgetHexColor')
                ->value('value')
                ?? $this->widgetPanelDefaultColor;

            $opacity = Setting::query()
                ->where('option', 'widgetOpacity')
                ->value('value')
                ?? $this->widgetPanelDefaultOpacity;

            return response()->json([
                'color' => $this->normalizeWidgetColor($color),
                'opacity' => $this->normalizeWidgetOpacity($opacity),
            ]);

        } catch (Throwable $e) {
            Log::error(
                'Ошибка получения настроек панели виджетов: '
                . $e->getMessage()
            );

            return response()->json([
                'color' => $this->widgetPanelDefaultColor,
                'opacity' => $this->widgetPanelDefaultOpacity,
            ], 500);
        }
    }

    /**
     * Обновляет настройки панели виджетов в админке.
     *
     * @param UpdateWidgetPanelRequest $request
     * @return JsonResponse
     */
    public function updateWidgetPanelSettings(
        UpdateWidgetPanelRequest $request
    ): JsonResponse {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

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

            return response()->json([
                'success' => true,
                'message' => 'Настройки панели виджетов обновлены.',
            ]);

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error(
                'Ошибка обновления настроек панели виджетов: '
                . $e->getMessage()
            );

            return response()->json([
                'success' => false,
                'message' => 'Ошибка сохранения настроек панели виджетов.',
            ], 500);
        }
    }

    /**
     * Нормализует цвет (hex без #).
     * Если значение невалидное — возвращает дефолт.
     *
     * @param mixed $color
     * @return string
     */
    private function normalizeWidgetColor($color): string
    {
        $color = (string) $color;

        return preg_match('/^[0-9A-Fa-f]{6}$/', $color)
            ? strtoupper($color)
            : $this->widgetPanelDefaultColor;
    }

    /**
     * Нормализует прозрачность (0..1).
     * Если значение невалидное — возвращает дефолт.
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
     *
     * @param string $option
     * @param string $value
     * @param string $type
     * @param string $constant
     * @return void
     */
    private function updateSettingPair(
        string $option,
        string $value,
        string $type,
        string $constant
    ): void {
        Setting::query()->updateOrCreate(
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
