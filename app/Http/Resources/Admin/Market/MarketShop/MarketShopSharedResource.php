<?php

namespace App\Http\Resources\Admin\Market\MarketShop;

use App\Http\Resources\Admin\Market\MarketCompany\MarketCompanySharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketShopSharedResource extends JsonResource
{
    /**
     * Компактный ресурс магазина для Index и связанных списков.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'market_company_id' => $this->market_company_id,
            'user_id' => $this->user_id,

            /** Основные данные магазина */
            'url' => $this->url,
            'email' => $this->email,
            'phone' => $this->phone,
            'logo' => $this->logo,

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

            /** Дата публикации / окно показа */
            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Счётчики */
            'views' => (int) $this->views,
            'images_count' => $this->whenCounted('images'),

            /** Текущий перевод */
            'translation' => $this->currentTranslation(),

            /** Relations */
            'company' => $this->whenLoaded('company', function () {
                return new MarketCompanySharedResource($this->company);
            }),

            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url,
                ];
            }),

            'images' => MarketShopImageResource::collection(
                $this->whenLoaded('images')
            ),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Текущий перевод из уже загруженной коллекции translations.
     *
     * На Index Controller должен загружать только текущую локаль,
     * поэтому обычно это первый элемент коллекции.
     */
    private function currentTranslation(): ?MarketShopTranslationResource
    {
        if (!$this->relationLoaded('translations')) {
            return null;
        }

        $translation = $this->translations->first();

        return $translation
            ? new MarketShopTranslationResource($translation)
            : null;
    }
}
