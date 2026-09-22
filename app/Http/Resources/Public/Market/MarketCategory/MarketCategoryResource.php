<?php

namespace App\Http\Resources\Public\Market\MarketCategory;

use App\Http\Resources\Admin\Market\MarketCategory\MarketCategoryImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketCategoryResource extends JsonResource
{
    /**
     * Полное представление категории
     * для Public Show.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $this->relationLoaded(
            'translations'
        )
            ? $this->translationOrFallback(
                $locale,
                $fallbackLocale
            )
            : null;

        return [
            'id' =>
                (int) $this->id,

            'parent_id' =>
                $this->parent_id !== null
                    ? (int) $this->parent_id
                    : null,

            'level' =>
                (int) $this->level,

            'url' =>
                $this->url,

            'icon' =>
                $this->icon,

            'views' =>
                (int) $this->views,

            /**
             * Resolved translation.
             *
             * Строгий порядок:
             * current → fallback → null.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'title' =>
                        $translation->title,

                    'subtitle' =>
                        $translation->subtitle,

                    'short' =>
                        $translation->short,

                    'description' =>
                        $translation->description,

                    'meta_title' =>
                        $translation->meta_title,

                    'meta_keywords' =>
                        $translation->meta_keywords,

                    'meta_description' =>
                        $translation->meta_description,
                ]
                : null,

            /**
             * Родительская категория.
             */
            'parent' =>
                MarketCategorySharedResource::make(
                    $this->whenLoaded('parent')
                ),

            /**
             * Публичные дочерние категории.
             *
             * Laravel relation:
             * publicCatalogChildren.
             *
             * Public API:
             * children.
             */
            'children' =>
                MarketCategorySharedResource::collection(
                    $this->whenLoaded(
                        'publicCatalogChildren'
                    )
                ),

            /**
             * Изображения категории.
             */
            'images' =>
                MarketCategoryImageResource::collection(
                    $this->whenLoaded('images')
                ),

            /**
             * Public counts.
             */
            'products_count' =>
                $this->whenCounted(
                    'products'
                ),

            'children_count' =>
                $this->whenCounted(
                    'children'
                ),

            'images_count' =>
                $this->whenCounted(
                    'images'
                ),
        ];
    }
}
