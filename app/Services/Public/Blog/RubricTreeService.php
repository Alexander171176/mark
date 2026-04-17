<?php

namespace App\Services\Public\Blog;

use App\Models\Admin\Blog\Rubric\Rubric;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RubricTreeService
{
    /**
     * Получить дерево рубрик с кэшированием.
     *
     * @param string $locale
     * @param int $ttl
     * @return array
     */
    public function getTree(string $locale, int $ttl = 1800): array
    {
        $cacheKey = "blog:rubric_tree:{$locale}";

        return Cache::remember($cacheKey, $ttl, function () use ($locale) {
            $rubrics = Rubric::query()
                ->forPublic($locale)
                ->orderBy('sort', 'asc')
                ->get();

            return $this->buildTree($rubrics);
        });
    }

    /**
     * Построение дерева рубрик.
     *
     * @param Collection|null $rubrics
     * @return array
     */
    public function buildTree(?Collection $rubrics): array
    {
        if (!$rubrics || $rubrics->isEmpty()) {
            return [];
        }

        $items = $rubrics->map(function ($rubric) {
            return [
                'id' => $rubric->id,
                'parent_id' => $rubric->parent_id,
                'title' => $rubric->title,
                'url' => $rubric->url,
                'icon' => $rubric->icon,
                'sort' => $rubric->sort,
                'children' => [],
            ];
        })->keyBy('id')->toArray();

        $tree = [];

        foreach ($items as $id => $item) {
            $parentId = $item['parent_id'] ?? null;

            if ($parentId && isset($items[$parentId])) {
                $items[$parentId]['children'][] = &$items[$id];
            } else {
                $tree[] = &$items[$id];
            }
        }

        return array_values($tree);
    }

    /**
     * Очистка кэша дерева рубрик по локали.
     *
     * @param string|null $locale
     * @return void
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
