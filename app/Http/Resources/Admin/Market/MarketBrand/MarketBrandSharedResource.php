<?php

namespace App\Http\Resources\Admin\Market\MarketBrand;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class MarketBrandSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentLocale = app()->getLocale();

        $translation = $this->whenLoaded('translations', function () use ($currentLocale) {
            return $this->translations->firstWhere('locale', $currentLocale)
                ?: $this->translations->firstWhere('locale', config('app.fallback_locale', 'ru'))
                    ?: $this->translations->first();
        });

        $firstImage = $this->whenLoaded('images', fn () => $this->images->first());

        $thumbnailUrl = !($firstImage instanceof MissingValue) && $firstImage
            ? $firstImage->thumb_url
            : null;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /** Основные данные */
            'url' => $this->url,
            'website' => $this->website,
            'logo' => $this->logo,
            'icon' => $this->icon,
            'social_links' => $this->social_links,

            /** Отображение */
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,
            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            /** Публикация / модерация */
            'status' => $this->status,
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,

            /** Перевод */
            'locale' => $translation?->locale,
            'title' => $translation?->title,
            'subtitle' => $translation?->subtitle,
            'short' => $translation?->short,

            /** Изображение */
            'thumbnail_url' => $thumbnailUrl,

            /** Counts */
            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            /** Timestamps */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
