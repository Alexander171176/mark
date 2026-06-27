<?php

namespace App\Http\Resources\Admin\Market\MarketCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class MarketCategorySharedResource extends JsonResource
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

        $firstImage = $this->whenLoaded('images', fn () => $this->images->first());

        $thumbnailUrl = !($firstImage instanceof MissingValue) && $firstImage
            ? $firstImage->thumb_url
            : null;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            /** Дерево */
            'parent_id' => $this->parent_id,
            'level' => (int) $this->level,
            'is_root' => $this->parent_id === null,

            /** Основные данные */
            'url' => $this->url,
            'icon' => $this->icon,

            /** Отображение */
            'in_menu' => (bool) $this->in_menu,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /** Публикация / модерация */
            'status' => $this->status,
            'moderation_status' => (int) $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,

            /** Перевод для дерева */
            'locale' => $translation?->locale,
            'title' => $translation?->title,
            'subtitle' => $translation?->subtitle,
            'short' => $translation?->short,

            /** Изображение для дерева */
            'thumbnail_url' => $thumbnailUrl,

            /** Дети */
            'children' => self::collection(
                $this->whenLoaded('children')
            ),

            /** Counts */
            'children_count' => $this->when(
                isset($this->children_count),
                fn () => (int) $this->children_count
            ),

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
