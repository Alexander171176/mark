<?php

namespace App\Http\Resources\Admin\Slider\SliderSlideAction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideActionResource extends JsonResource
{
    /**
     * Полный ресурс кнопки или действия слайда.
     *
     * Используется для Show, Edit
     * и административного конструктора.
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            'id' => $this->id,
            'slider_slide_id' => $this->slider_slide_id,

            /** Отображение и сортировка */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'is_primary' => (bool) $this->is_primary,

            /** Тип и назначение действия */
            'action_type' => $this->action_type,
            'action_value' => $this->action_value,
            'route_params' => $this->route_params,
            'target' => $this->target,

            /** Внешний вид кнопки */
            'style' => $this->style,
            'icon' => $this->icon,
            'icon_position' => $this->icon_position,
            'css_class' => $this->css_class,

            /** Дополнительные настройки */
            'settings' => $this->settings,

            /** Текущий перевод */
            'translation' => $translation
                ? new SliderSlideActionTranslationResource($translation)
                : null,

            /** Все переводы */
            'translations' => SliderSlideActionTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Системные даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Получение текущего перевода.
     *
     * Приоритет:
     * 1. Текущий язык приложения.
     * 2. Резервный язык приложения.
     * 3. Первый доступный перевод.
     *
     * Использует только заранее загруженные
     * отношения и не выполняет SQL-запросов.
     */
    private function currentTranslation(): ?object
    {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        $locale = app()->getLocale();

        $fallback = config('app.fallback_locale', 'ru');

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }
}
