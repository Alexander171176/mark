<?php

namespace App\Http\Resources\Admin\Slider\SliderSlide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideSharedResource extends JsonResource
{
    /**
     * Компактный ресурс слайда
     * для Index и связанных списков.
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            'id' => $this->id,
            'slider_id' => $this->slider_id,

            /** Отображение */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'is_main' => (bool) $this->is_main,

            /** Публикация */
            'status' => $this->status,

            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Основные настройки оформления */
            'background_color' => $this->background_color,
            'image_position' => $this->image_position,
            'content_position' => $this->content_position,

            /** Анимация */
            'animation_type' => $this->animation_type,

            /** Счётчики */
            'images_count' => $this->whenCounted('images'),
            'actions_count' => $this->whenCounted('actions'),
            'advantages_count' => $this->whenCounted('advantages'),

            /** Текущий перевод */
            'translation' => $translation
                ? new SliderSlideTranslationResource($translation)
                : null,

            /** Изображения, если загружены */
            'images' => SliderSlideImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Системные даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Текущий перевод из загруженных данных.
     *
     * Resource не выполняет SQL-запросы.
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
