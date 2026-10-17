<?php

namespace App\Http\Resources\Admin\Slider\SliderSlide;

use App\Models\Admin\Slider\SliderSlide\SliderSlideTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideSharedResource extends JsonResource
{
    /** Компактный ресурс самостоятельного слайда для Index и связанных списков. */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();
        $pivot = $this->resource->relationLoaded('pivot') ? $this->resource->pivot : null;

        return [
            'id' => (int) $this->id,
            'sort' => (int) $this->sort,
            'moderation_status' => (int) $this->moderation_status,
            'is_pending' => $this->isPendingModeration(),
            'is_approved' => $this->isApproved(),
            'is_rejected' => $this->isRejected(),
            'moderated_by' => $this->moderated_by !== null ? (int) $this->moderated_by : null,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,
            'moderator' => $this->whenLoaded('moderator', function () {
                return $this->moderator ? [
                    'id' => (int) $this->moderator->id,
                    'name' => $this->moderator->name,
                    'email' => $this->moderator->email,
                ] : null;
            }),

            // Глобальная активность слайда.
            'activity' => (bool) $this->activity,

            // Локальные параметры размещения; null для независимого каталога.
            'pivot' => $pivot ? [
                'id' => $pivot->id !== null ? (int) $pivot->id : null,
                'slider_id' => (int) $pivot->slider_id,
                'slider_slide_id' => (int) $pivot->slider_slide_id,
                'sort' => (int) $pivot->sort,
                'activity' => (bool) $pivot->activity,
                'is_main' => (bool) $pivot->is_main,
                'created_at' => $pivot->created_at?->toISOString(),
                'updated_at' => $pivot->updated_at?->toISOString(),
            ] : null,

            // Публикация.
            'status' => $this->status,
            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            // Краткие параметры оформления.
            'background_color' => $this->background_color,
            'image_position' => $this->image_position,
            'content_position' => $this->content_position,
            'animation_type' => $this->animation_type,

            // Счётчики связей.
            'sliders_count' => $this->whenCounted('sliders'),
            'images_count' => $this->whenCounted('images'),
            'actions_count' => $this->whenCounted('actions'),
            'advantages_count' => $this->whenCounted('advantages'),

            // Текущий перевод и изображения — только из загруженных отношений.
            'translation' => $translation ? new SliderSlideTranslationResource($translation) : null,
            // Главное изображение в порядке pivot.order; media должна быть загружена.
            'image' => $this->whenLoaded('images', function () {
                $image = $this->images->first();
                return $image ? new SliderSlideImageResource($image) : null;
            }),
            'images' => SliderSlideImageResource::collection($this->whenLoaded('images')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /** Выбор перевода из загруженной коллекции, без SQL-запросов. */
    private function currentTranslation(): ?SliderSlideTranslation
    {
        if (! $this->resource->relationLoaded('translations')) {
            return null;
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'ru');

        return $this->resource->translations->firstWhere('locale', $locale)
            ?: $this->resource->translations->firstWhere('locale', $fallback)
                ?: $this->resource->translations->first();
    }
}
