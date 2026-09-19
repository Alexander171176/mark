<?php

namespace App\Services\Public\Market;

use App\Models\Admin\Market\MarketCategory\MarketCategory;

class MarketCategoryTreeService
{
    /**
     * Получить публичное дерево
     * категорий маркетплейса.
     */
    public function getTree(string $locale): array
    {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        $categories = MarketCategory::query()
            ->forMenu()
            ->root()
            ->with([
                /**
                 * Переводы корневых категорий.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Изображения корневых категорий.
                 */
                'images.media',

                /**
                 * Второй уровень.
                 */
                'publicCatalogChildren' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn ($query) =>
                            $query->whereIn(
                                'locale',
                                $locales
                            ),

                        'images.media',

                        /**
                         * Третий уровень.
                         */
                        'publicCatalogChildren' =>
                            fn ($query) =>
                            $query->with([
                                'translations' =>
                                    fn ($query) =>
                                    $query->whereIn(
                                        'locale',
                                        $locales
                                    ),

                                'images.media',
                            ]),
                    ]),
            ])
            ->get();

        return $categories
            ->map(
                fn (MarketCategory $category) =>
                $this->mapCategory(
                    $category,
                    $locale,
                    $fallbackLocale
                )
            )
            ->values()
            ->all();
    }

    /**
     * Преобразовать категорию
     * в элемент Public-дерева.
     */
    private function mapCategory(
        MarketCategory $category,
        string $locale,
        string $fallbackLocale
    ): array {
        $translation =
            $category->translationOrFallback(
                $locale,
                $fallbackLocale
            );

        $image = $category
            ->images
            ->first();

        /**
         * Relation уже содержит только
         * публичные категории меню.
         */
        $children = $category->relationLoaded(
            'publicCatalogChildren'
        )
            ? $category->publicCatalogChildren
            : collect();

        return [
            'id' =>
                $category->id,

            'parent_id' =>
                $category->parent_id,

            'level' =>
                (int) $category->level,

            'url' =>
                $category->url,

            'icon' =>
                $category->icon,

            'title' =>
                $translation?->title,

            'subtitle' =>
                $translation?->subtitle,

            'short' =>
                $translation?->short,

            'thumbnail_url' =>
                $image?->thumb_url,

            'children_count' =>
                $children->count(),

            'children' =>
                $children
                    ->map(
                        fn (
                            MarketCategory $child
                        ) =>
                        $this->mapCategory(
                            $child,
                            $locale,
                            $fallbackLocale
                        )
                    )
                    ->values()
                    ->all(),
        ];
    }
}
