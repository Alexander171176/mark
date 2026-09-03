<?php

namespace App\Http\Resources\Admin\Market\MarketBrand;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketBrandSharedResource extends JsonResource
{
    /**
     * Компактный ресурс бренда для Index и связанных списков
     */
    public function toArray(Request $request): array
    {
        $translation = $this->currentTranslation();

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /** Основные данные */
            'url' => $this->url,
            'website' => $this->website,
            'logo' => $this->logo,
            'icon' => $this->icon,

            /** Отображение / сортировка / активность */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            /** Статус публикации */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,

            'is_pending' => (int) $this->moderation_status === 0,
            'is_approved' => (int) $this->moderation_status === 1,
            'is_rejected' => (int) $this->moderation_status === 2,

            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Публикация / окно показа */
            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Статистика */
            'views' => (int) $this->views,

            /** Счётчики */
            'images_count' => $this->whenCounted('images'),

            /** Текущий перевод */
            'translation' => $translation
                ? new MarketBrandTranslationResource($translation)
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

            /** Изображения */
            'images' => MarketBrandImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Перевод текущей локали.
     *
     * Controller для Index загружает translations
     * уже с фильтром по locale.
     */
    private function currentTranslation()
    {
        if (!$this->relationLoaded('translations')) {
            return null;
        }

        return $this->translations->first();
    }
}
