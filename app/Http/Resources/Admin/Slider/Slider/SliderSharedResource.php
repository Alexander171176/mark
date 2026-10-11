<?php

namespace App\Http\Resources\Admin\Slider\Slider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSharedResource extends JsonResource
{
    /**
     * Компактный ресурс слайдера
     * для Index и связанных списков.
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

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

            /** Владелец */
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url,
                ];
            }),

            /** Системные даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Перевод для административного списка.
     *
     * Контроллер может предварительно загрузить
     * только необходимый перевод.
     *
     * SQL-запросы здесь не выполняются.
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
