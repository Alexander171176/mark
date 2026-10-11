<?php

namespace App\Http\Resources\Admin\Slider\SliderSlideAdvantage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideAdvantageResource extends JsonResource
{
    /**
     * Полный ресурс преимущества слайда.
     *
     * Используется для Show, Edit
     * и административного конструктора слайдеров.
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

            /** Настройки иконки */
            'icon_type' => $this->icon_type,
            'icon' => $this->icon,
            'icon_color' => $this->icon_color,

            /** Внешний вид преимущества */
            'style' => $this->style,

            /** Дополнительные настройки */
            'settings' => $this->settings,

            /** Текущий перевод */
            'translation' => $translation
                ? new SliderSlideAdvantageTranslationResource($translation)
                : null,

            /** Все переводы */
            'translations' => SliderSlideAdvantageTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Системные даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Получение перевода из заранее загруженной коллекции.
     *
     * Приоритет:
     * 1. Текущий язык приложения.
     * 2. Резервный язык приложения.
     * 3. Первый доступный перевод.
     *
     * Дополнительные SQL-запросы не выполняются.
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
