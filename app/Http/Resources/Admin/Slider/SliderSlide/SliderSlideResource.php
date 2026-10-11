<?php

namespace App\Http\Resources\Admin\Slider\SliderSlide;

use App\Http\Resources\Admin\Slider\Slider\SliderSharedResource;
use App\Http\Resources\Admin\Slider\SliderSlideAction\SliderSlideActionResource;
use App\Http\Resources\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideResource extends JsonResource
{
    /**
     * Полный ресурс слайда для Show, Edit
     * и административного конструктора.
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            'id' => $this->id,
            'slider_id' => $this->slider_id,

            /** Отображение и сортировка */
            'activity' => (bool) $this->activity,
            'sort' => (int) $this->sort,
            'is_main' => (bool) $this->is_main,

            /** Статус публикации */
            'status' => $this->status,

            /** Публикация и период показа */
            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Фон и оформление */
            'background_color' => $this->background_color,
            'text_color' => $this->text_color,
            'accent_color' => $this->accent_color,
            'overlay_color' => $this->overlay_color,

            'overlay_opacity' => $this->overlay_opacity !== null
                ? (int) $this->overlay_opacity
                : null,

            'image_position' => $this->image_position,
            'content_position' => $this->content_position,

            /** Анимация содержимого */
            'animation_type' => $this->animation_type,
            'animation_duration' => (int) $this->animation_duration,
            'animation_delay' => (int) $this->animation_delay,
            'animation_stagger' => (int) $this->animation_stagger,
            'animation_once' => (bool) $this->animation_once,

            /** Индивидуальные настройки анимации */
            'animation_settings' => $this->animation_settings,

            /** Дополнительные настройки */
            'settings' => $this->settings,

            /** Счётчики */
            'images_count' => $this->whenCounted('images'),
            'actions_count' => $this->whenCounted('actions'),
            'advantages_count' => $this->whenCounted('advantages'),

            /** Текущий перевод */
            'translation' => $translation
                ? new SliderSlideTranslationResource($translation)
                : null,

            /** Все переводы */
            'translations' => SliderSlideTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Родительский слайдер */
            'slider' => $this->whenLoaded('slider', function () {
                return new SliderSharedResource($this->slider);
            }),

            /** Изображения */
            'images' => SliderSlideImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Изображения для компьютеров */
            'desktop_images' => SliderSlideImageResource::collection(
                $this->whenLoaded('desktopImages')
            ),

            /** Изображения для мобильных устройств */
            'mobile_images' => SliderSlideImageResource::collection(
                $this->whenLoaded('mobileImages')
            ),

            /** Кнопки и действия */
            'actions' => SliderSlideActionResource::collection(
                $this->whenLoaded('actions')
            ),

            /** Преимущества */
            'advantages' => SliderSlideAdvantageResource::collection(
                $this->whenLoaded('advantages')
            ),

            /** Системные даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Текущий перевод из загруженной коллекции.
     *
     * Приоритет:
     * 1. Текущий язык.
     * 2. Резервный язык.
     * 3. Первый доступный перевод.
     *
     * SQL-запросы не выполняются.
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
