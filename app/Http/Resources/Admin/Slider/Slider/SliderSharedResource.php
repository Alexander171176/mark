<?php

namespace App\Http\Resources\Admin\Slider\Slider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSharedResource extends JsonResource
{
    /** Компактное представление слайдера для Index и связанных списков. */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            'id' => (int) $this->id,
            'user_id' => $this->user_id !== null ? (int) $this->user_id : null,

            /** Основные данные */
            'code' => $this->code,
            'type' => $this->type,

            /** Отображение и сортировка */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Публикация */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,
            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,
            'moderated_by' => $this->moderated_by !== null ? (int) $this->moderated_by : null,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Даты публикации */
            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Краткие настройки Swiper */
            'effect' => $this->effect,
            'autoplay_delay' => $this->autoplay_delay !== null
                ? (int) $this->autoplay_delay
                : null,
            'loop' => (bool) $this->loop,

            /** Счётчик слайдов */
            'slides_count' => $this->whenCounted('slides'),

            /** Текущий перевод */
            'translation' => $translation
                ? new SliderTranslationResource($translation)
                : null,

            /** Изображения слайдов для мини-галереи в Index. */
            'preview_images' => $this->whenLoaded('slides', function () {
                return $this->slides
                    ->sortBy(fn ($slide) => [(int) $slide->sort, (int) $slide->id])
                    ->flatMap(function ($slide) {
                        if (! $slide->relationLoaded('images')) {
                            return [];
                        }

                        return $slide->images->map(function ($image) use ($slide) {
                            $media = $image->getFirstMedia('images');
                            if (! $media) {
                                return null;
                            }

                            return [
                                'id' => (int) $image->id,
                                'slide_id' => (int) $slide->id,
                                'url' => $media->getUrl(),
                                'alt' => $image->alt ?? '',
                                'caption' => $image->caption ?? '',
                            ];
                        })->filter()->values();
                    })
                    ->values()
                    ->all();
            }),

            /** Владелец */
            'owner' => $this->whenLoaded('owner', function () {
                if (! $this->owner) {
                    return null;
                }

                return [
                    'id' => (int) $this->owner->id,
                    'name' => $this->owner->name,
                    'email' => $this->owner->email,
                    'profile_photo_url' => $this->owner->profile_photo_url,
                ];
            }),

            /** Системные даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Текущий перевод из заранее загруженных translations.
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
