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
    public function getTree(string $locale, int $ttl = 1800): array
    {
        $cacheKey = "blog:rubric_tree:{$locale}";

        return Cache::remember($cacheKey, $ttl, function () use ($locale) {
            $rubrics = BlogRubric::query()
                ->forPublic()
                ->with('translations')
                ->sortByParam('sort_asc', $locale)
                ->get();

            return $this->buildTree($rubrics, $locale);
        });
    }

    /**
     * Построение дерева рубрик.
     */
    public function buildTree(?Collection $rubrics, ?string $locale = null): array
    {
        if (!$rubrics || $rubrics->isEmpty()) {
            return [];
        }

        $locale = $locale ?: app()->getLocale();

        $items = $rubrics->map(function (BlogRubric $rubric) use ($locale) {
            $translation = $rubric->translationOrFallback(
                $locale,
                config('app.fallback_locale', 'ru')
            );

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
        })->keyBy('id')->toArray();

        $tree = [];

        foreach ($items as $id => &$item) {
            $parentId = $item['parent_id'] ?? null;

            if ($parentId && isset($items[$parentId])) {
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
    public function forget(?string $locale = null): void
    {
        if ($locale) {
            Cache::forget("blog:rubric_tree:{$locale}");
            return;
        }

        foreach (config('app.available_locales', []) as $loc) {
            Cache::forget("blog:rubric_tree:{$loc}");
        }
    }
}
