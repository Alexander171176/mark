<?php

namespace App\Services\Public\Market;

use App\Models\Admin\Market\MarketCategory\MarketCategory;
use Illuminate\Support\Collection;

class MarketCategoryTreeService
{
    /** Получить публичное дерево категорий маркетплейса. */
    public function getTree(string $locale): array
    {
        $categories = MarketCategory::query()
            ->forMenu()
            ->root()
            ->with([
                'translations',
                'images',

                'publicCatalogChildren' => fn ($query) => $query
                    ->with([
                        'translations',
                        'images',
                        'publicCatalogChildren',
                    ]),
            ])
            ->get();

        return $categories
            ->map(fn (MarketCategory $category) => $this->mapCategory($category, $locale))
            ->values()
            ->all();
    }

    /** Преобразовать категорию в элемент дерева. */
    private function mapCategory(MarketCategory $category, string $locale): array
    {
        $translation = $category->translationOrFallback($locale);
        $image = $category->images->first();

        /** @var Collection $children */
        $children = $category->publicCatalogChildren;

        return [
            'id' => $category->id,
            'parent_id' => $category->parent_id,
            'level' => (int) $category->level,

            'url' => $category->url,
            'icon' => $category->icon,

            'title' => $translation?->title,
            'subtitle' => $translation?->subtitle,
            'short' => $translation?->short,

            'thumbnail_url' => $image?->thumb_url,

            'children_count' => $children->count(),

            'children' => $children
                ->map(fn (MarketCategory $child) => $this->mapCategory($child, $locale))
                ->values()
                ->all(),
        ];
    }
}
