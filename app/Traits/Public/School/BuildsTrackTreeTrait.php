<?php

namespace App\Traits\Public\School;

use App\Http\Resources\Public\School\SchoolTrack\SchoolTrackTreeResource;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;

trait BuildsTrackTreeTrait
{
    /**
     * Строит публичное дерево направлений обучения.
     *
     * Максимальная глубина дерева — 3 уровня.
     */
    protected function buildTrackTree(string $locale): array
    {
        $tracks = SchoolTrack::query()
            ->forPublic($locale)
            ->root()
            ->with([
                'children' => function ($query) use ($locale) {
                    $query
                        ->forPublic($locale)
                        ->ordered()
                        ->with([
                            'children' => function ($query) use ($locale) {
                                $query
                                    ->forPublic($locale)
                                    ->ordered();
                            },
                        ]);
                },
            ])
            ->ordered()
            ->get();

        return SchoolTrackTreeResource::collection($tracks)
            ->resolve();
    }
}
