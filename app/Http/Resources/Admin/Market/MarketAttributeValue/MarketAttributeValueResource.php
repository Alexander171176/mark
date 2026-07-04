<?php

namespace App\Http\Resources\Admin\Market\MarketAttributeValue;

use App\Http\Resources\Admin\Market\MarketAttribute\MarketAttributeSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAttributeValueResource extends JsonResource
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
            'market_attribute_id' => $this->market_attribute_id,

            /** Основные данные значения */
            'code' => $this->code,
            'icon' => $this->icon,
            'color' => $this->color,

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
                ? new MarketAttributeValueTranslationResource($currentTranslation)
                : null,

            /** Все переводы */
            'translations' => MarketAttributeValueTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            /** Relations */
            'attribute' => new MarketAttributeSharedResource(
                $this->whenLoaded('attribute')
            ),

            'moderator' => $this->whenLoaded('moderator', function () {
                return [
                    'id' => $this->moderator?->id,
                    'name' => $this->moderator?->name,
                ];
            }),
        ];
    }
}
