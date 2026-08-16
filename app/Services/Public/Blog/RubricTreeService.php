<?php

namespace App\Services\Public\Blog;

use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RubricTreeService
{
    /**
     * Получить дерево рубрик с кэшированием.
     */
    public function getTree(
        string $locale,
        int $ttl = 1800
    ): array {
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

        $cacheKey = "blog:rubric_tree:{$locale}";

        return Cache::remember(
            $cacheKey,
            $ttl,
            function () use ($locale, $fallbackLocale, $locales) {
                $rubrics = BlogRubric::query()
                    ->forPublic()
                    ->with([
                        'translations' => fn ($query) =>
                        $query->whereIn(
                            'locale',
                            $locales
                        ),
                    ])
                    ->sortByParam(
                        'sort_asc',
                        $locale
                    )
                    ->get();

                return $this->buildTree(
                    $rubrics,
                    $locale,
                    $fallbackLocale
                );
            }
        );
    }

    /**
     * Построение дерева рубрик.
     */
    public function buildTree(
        ?Collection $rubrics,
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): array {
        if (!$rubrics || $rubrics->isEmpty()) {
            return [];
        }

        $locale = $locale ?: app()->getLocale();

        $fallbackLocale = $fallbackLocale
            ?: config('app.fallback_locale', 'ru');

        $items = $rubrics
            ->map(function (BlogRubric $rubric) use (
                $locale,
                $fallbackLocale
            ) {
                $translation = null;

                if ($rubric->relationLoaded('translations')) {
                    $translation = $rubric->translations
                        ->firstWhere(
                            'locale',
                            $locale
                        )
                        ?: $rubric->translations
                            ->firstWhere(
                                'locale',
                                $fallbackLocale
                            )
                            ?: $rubric->translations->first();
                }

                return [
                    'id' => $rubric->id,
                    'parent_id' => $rubric->parent_id,

                    'title' => $translation?->title,
                    'subtitle' => $translation?->subtitle,
                    'short' => $translation?->short,

                    'url' => $rubric->url,
                    'icon' => $rubric->icon,

                    'sort' => (int) $rubric->sort,
                    'level' => (int) $rubric->level,

                    'children' => [],
                ];
            })
            ->keyBy('id')
            ->toArray();

        $tree = [];

        foreach ($items as $id => &$item) {
            $parentId = $item['parent_id'] ?? null;

            if (
                $parentId
                && isset($items[$parentId])
            ) {
                $items[$parentId]['children'][] = &$item;
            } else {
                $tree[] = &$item;
            }
        }

        unset($item);

        return array_values($tree);
    }

    /**
     * Очистка кэша дерева рубрик.
     */
    public function forget(
        ?string $locale = null
    ): void {
        if ($locale) {
            Cache::forget(
                "blog:rubric_tree:{$locale}"
            );

            return;
        }

        foreach (
            config('app.available_locales', [])
            as $loc
        ) {
            Cache::forget(
                "blog:rubric_tree:{$loc}"
            );
        }
    }
}
