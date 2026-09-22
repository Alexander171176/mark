<?php

namespace App\Http\Resources\Public\Market\MarketCategory;

use App\Http\Resources\Admin\Market\MarketCategory\MarketCategoryImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MarketCategorySharedResource extends JsonResource
{
    /**
     * Компактное представление категории
     * для Public списков, карточек и дерева.
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

            'sort' =>
                (int) $this->sort,

            'in_menu' =>
                (bool) $this->in_menu,

            'views' =>
                (int) $this->views,

            'published_at' =>
                $this->published_at?->toISOString(),

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
                ]
                : null,

            /**
             * Родительская категория.
             */
            'parent' =>
                self::make(
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
                self::collection(
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
