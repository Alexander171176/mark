<?php

namespace App\Http\Resources\Admin\Slider\Slider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Компактное представление слайдера для Index и связанных списков. */
class SliderSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            'id' => (int) $this->id,
            'user_id' => $this->user_id !== null ? (int) $this->user_id : null,

            /** Основные поля для списка */
            'code' => $this->code,
            'type' => $this->type,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,
            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,
            'moderated_by' => $this->moderated_by !== null ? (int) $this->moderated_by : null,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Публикация */
            'published_at' => $this->published_at?->format('Y-m-d\TH:i'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Краткие настройки */
            'effect' => $this->effect,
            'autoplay_delay' => $this->autoplay_delay !== null ? (int) $this->autoplay_delay : null,
            'loop' => (bool) $this->loop,

            /** Счётчик связей */
            'slides_count' => $this->whenCounted('slides'),

            /** Только текущий перевод; translations[] для Index не возвращаем */
            'translation' => $translation ? new SliderTranslationResource($translation) : null,

            /** Миниатюры только из eager-loaded slides.images.media */
            'preview_images' => $this->whenLoaded('slides', function () {
                return $this->slides
                    ->sortBy(fn ($slide) => [
                        (int) ($slide->relationLoaded('pivot') ? ($slide->pivot?->sort ?? 0) : 0),
                        (int) $slide->id,
                    ])
                    ->flatMap(function ($slide) {
                        if (! $slide->relationLoaded('images')) {
                            return [];
                        }

                        return $slide->images->map(function ($image) use ($slide) {
                            // Важно: getFirstMedia() здесь не вызываем,
                            // так как MediaLibrary может загрузить связь лениво.
                            if (! $image->relationLoaded('media')) {
                                return null;
                            }

                            $media = $image->getRelation('media')
                                ->firstWhere('collection_name', 'images');

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

            /** Модератор, если загружен */
            'moderator' => $this->whenLoaded('moderator', function () {
                if (! $this->moderator) {
                    return null;
                }

                return [
                    'id' => (int) $this->moderator->id,
                    'name' => $this->moderator->name,
                    'email' => $this->moderator->email,
                ];
            }),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /** Читаем только переводы, уже загруженные контроллером. */
    private function currentTranslation(): ?object
    {
        if (! $this->resource->relationLoaded('translations')) {
            return null;
        }

        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'ru');
        $translations = $this->resource->getRelation('translations');

        return $translations->firstWhere('locale', $locale)
            ?: $translations->firstWhere('locale', $fallback)
                ?: $translations->first();
    }
}
