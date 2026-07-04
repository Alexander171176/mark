<?php

namespace App\Http\Resources\Admin\Market\MarketAttributeValue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketAttributeValueSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $translation = $this->whenLoaded('translations', function () use ($currentLocale) {
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

            /** Основные данные */
            'code' => $this->code,
            'icon' => $this->icon,
            'color' => $this->color,

            /** Отображение */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Публикация / модерация */
            'status' => $this->status,
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,

            /** Перевод */
            'locale' => $translation?->locale,
            'title' => $translation?->title,
            'subtitle' => $translation?->subtitle,
            'short' => $translation?->short,
            'description' => $translation?->description,

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
