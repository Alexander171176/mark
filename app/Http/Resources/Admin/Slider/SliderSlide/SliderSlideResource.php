<?php

namespace App\Http\Resources\Admin\Slider\SliderSlide;

use App\Http\Resources\Admin\Slider\Slider\SliderSharedResource;
use App\Http\Resources\Admin\Slider\SliderSlideAction\SliderSlideActionResource;
use App\Http\Resources\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantageResource;
use App\Models\Admin\Slider\SliderSlide\SliderSlideTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideResource extends JsonResource
{
    /** Полное представление самостоятельного слайда для Show, Edit и конструктора. */
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

            // Глобальная активность самого слайда, независимо от его размещений.
            'activity' => (bool) $this->activity,

            // Данные конкретного размещения в слайдере (если слайд получен через slides()).
            // У самостоятельного слайда pivot отсутствует.
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

            // Публикация и период показа.
            'status' => $this->status,
            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            // Фон и оформление.
            'background_color' => $this->background_color,
            'text_color' => $this->text_color,
            'accent_color' => $this->accent_color,
            'overlay_color' => $this->overlay_color,
            'overlay_opacity' => $this->overlay_opacity !== null ? (int) $this->overlay_opacity : null,
            'image_position' => $this->image_position,
            'content_position' => $this->content_position,

            // Анимация содержимого.
            'animation_type' => $this->animation_type,
            'animation_duration' => (int) $this->animation_duration,
            'animation_delay' => (int) $this->animation_delay,
            'animation_stagger' => (int) $this->animation_stagger,
            'animation_once' => (bool) $this->animation_once,
            'animation_settings' => $this->animation_settings,
            'settings' => $this->settings,

            // Счётчики связанных сущностей.
            'sliders_count' => $this->whenCounted('sliders'),
            'images_count' => $this->whenCounted('images'),
            'actions_count' => $this->whenCounted('actions'),
            'advantages_count' => $this->whenCounted('advantages'),

            // Переводы без дополнительных запросов к базе.
            'translation' => $translation ? new SliderSlideTranslationResource($translation) : null,
            'translations' => SliderSlideTranslationResource::collection($this->whenLoaded('translations')),

            // Слайд может принадлежать нескольким слайдерам.
            'sliders' => SliderSharedResource::collection($this->whenLoaded('sliders')),

            // Изображения и их назначения находятся в slider_slide_has_images.
            'images' => SliderSlideImageResource::collection($this->whenLoaded('images')),
            'desktop_images' => SliderSlideImageResource::collection($this->whenLoaded('desktopImages')),
            'mobile_images' => SliderSlideImageResource::collection($this->whenLoaded('mobileImages')),

            // Подключённые кнопки и преимущества (их pivot отдают собственные ресурсы).
            'actions' => SliderSlideActionResource::collection($this->whenLoaded('actions')),
            'advantages' => SliderSlideAdvantageResource::collection($this->whenLoaded('advantages')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /** Текущая локаль → fallback → первый доступный перевод; без SQL-запросов. */
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
