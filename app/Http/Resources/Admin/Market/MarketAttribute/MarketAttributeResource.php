<?php

namespace App\Http\Resources\Admin\Market\MarketAttribute;

use App\Http\Resources\Admin\Market\MarketAttributeGroup\MarketAttributeGroupSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $currentTranslation = $this->whenLoaded('translations', function () use ($currentLocale) {
            return $this->translations->firstWhere('locale', $currentLocale)
                ?: $this->translations->firstWhere(
                    'locale',
                    config('app.fallback_locale', 'ru')
                )
                    ?: $this->translations->first();
        });

        return [
            'id' => $this->id,
            'market_attribute_group_id' => $this->market_attribute_group_id,
            'user_id' => $this->user_id,

            /** Основные данные характеристики */
            'code' => $this->code,
            'icon' => $this->icon,
            'color' => $this->color,
            'type' => $this->type,
            'unit' => $this->unit,

            /** Настройки характеристики */
            'required' => (bool) $this->required,
            'filterable' => (bool) $this->filterable,
            'visible' => (bool) $this->visible,

            /** Отображение / сортировка / активность */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Статус публикации */
            'status' => $this->status,

            /** Модерация */
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,
            'moderated_by' => $this->moderated_by,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            /** Дата публикации / окно показа */
            'published_at' => $this->published_at?->format('Y-m-d'),
            'show_from_at' => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at' => $this->show_to_at?->format('Y-m-d\TH:i'),

            /** Текущий перевод */
            'translation' => $currentTranslation
                ? new MarketAttributeTranslationResource($currentTranslation)
                : null,

            /** Все переводы */
            'translations' => MarketAttributeTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Counts */
            'values_count' => $this->whenCounted('values'),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            /** Relations */
            'group' => new MarketAttributeGroupSharedResource(
                $this->whenLoaded('group')
            ),

            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner?->id,
                    'name' => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url,
                ];
            }),

            'moderator' => $this->whenLoaded('moderator', function () {
                return [
                    'id' => $this->moderator?->id,
                    'name' => $this->moderator?->name,
                ];
            }),
        ];
    }
}
