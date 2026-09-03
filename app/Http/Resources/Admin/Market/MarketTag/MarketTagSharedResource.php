<?php

namespace App\Http\Resources\Admin\Market\MarketTag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketTagSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /** Основные данные */
            'url' => $this->url,
            'icon' => $this->icon,
            'color' => $this->color,

            /** Отображение / сортировка / активность */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Публикация */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,
            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,

            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Дата публикации / окно показа */
            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Счётчики */
            'views' => (int) $this->views,

            'products_count' => $this->when(
                isset($this->products_count),
                fn () => (int) $this->products_count
            ),

            /** Перевод текущей локали */
            'translation' => $translation
                ? new MarketTagTranslationResource(
                    $translation
                )
                : null,

            /** Владелец */
            'owner' => $this->whenLoaded(
                'owner',
                function () {
                    return [
                        'id' => $this->owner?->id,
                        'name' => $this->owner?->name,
                        'email' => $this->owner?->email,
                        'profile_photo_url' => $this->owner?->profile_photo_url,
                    ];
                }
            ),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Получение перевода только из уже
     * загруженной relation translations.
     *
     * Для Index Controller загружает только
     * translation текущей локали.
     */
    private function currentTranslation()
    {
        if (!$this->relationLoaded('translations')) {
            return null;
        }

        return $this->translations->first();
    }
}
