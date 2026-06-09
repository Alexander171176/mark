<?php

namespace App\Traits\Public\School;

use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackResource;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;

trait BuildsTrackTreeTrait
{
    /** Строит дерево направлений обучения. */
    protected function buildTrackTree(string $locale): array
    {
        $tracks = SchoolTrack::query()
            ->forPublic($locale)
            ->with([
                'translation',
                'translations',
                'images',
            ])
            ->withCount([
                'children',
                'courses',
                'likes',
                'images',
            ])
            ->ordered()
            ->get();

        $items = SchoolTrackResource::collection($tracks)->resolve();

        $indexed = [];

        foreach ($items as $item) {
            $item['children'] = [];
            $indexed[$item['id']] = $item;
        }

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
