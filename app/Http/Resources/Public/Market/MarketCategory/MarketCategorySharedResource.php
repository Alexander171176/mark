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
            'id' => $this->id,

            'parent_id' =>
                $this->parent_id,

            'level' =>
                $this->level,

            'url' =>
                $this->url,

            'icon' =>
                $this->icon,

            'sort' =>
                $this->sort,

            'in_menu' =>
                (bool) $this->in_menu,

            'views' =>
                $this->views,

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

            'parent' =>
                self::make(
                    $this->whenLoaded('parent')
                ),

            'public_catalog_children' =>
                self::collection(
                    $this->whenLoaded(
                        'publicCatalogChildren'
                    )
                ),

            'images' =>
                MarketCategoryImageResource::collection(
                    $this->whenLoaded('images')
                ),

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
