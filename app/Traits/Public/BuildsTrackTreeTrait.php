<?php

namespace App\Traits\Public;

use App\Http\Resources\Admin\School\LearningCategory\LearningCategoryResource;
use App\Http\Resources\Admin\School\Track\SchoolTrackResource;
use App\Models\Admin\School\LearningCategory\LearningCategory;
use App\Models\Admin\School\Track\SchoolTrack;

trait BuildsTrackTreeTrait
{
    /**
     * Строит дерево категорий (trackTree).
     */
    protected function buildTrackTree(string $locale): array
    {
        $tracks = SchoolTrack::query()
            ->active()
            ->byLocale($locale)
            ->with([
                'images' => fn ($q) =>
                $q->orderBy('school_track_has_images.order', 'asc'),
            ])
            ->withCount([
                'children' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale),

                'courses' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published(),
            ])
            ->ordered()
            ->get();

        $items = SchoolTrackResource::collection($tracks)->resolve();

        // Индексация
        $indexed = [];

        foreach ($items as $item) {
            $item['children'] = [];
            $indexed[$item['id']] = $item;
        }

        // Построение дерева
        $tree = [];

        foreach ($indexed as $id => &$item) {
            if (!empty($item['parent_id']) && isset($indexed[$item['parent_id']])) {
                $indexed[$item['parent_id']]['children'][] = &$item;
            } else {
                $tree[] = &$item;
            }
        }

        unset($item);

        return array_values($tree);
    }
}
