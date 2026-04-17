<?php

namespace App\Traits\Public;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait WithUserLikesTrait
{
    protected function appendUserLikes(LengthAwarePaginator $paginator, $resourceClass): LengthAwarePaginator
    {
        $items = $paginator->getCollection()->map(function ($item) use ($resourceClass) {
            $resolved = (new $resourceClass($item))->resolve();

            $resolved['already_liked'] = auth()->check()
                ? $item->likes()->where('user_id', auth()->id())->exists()
                : false;

            return $resolved;
        });

        $paginator->setCollection(collect($items));

        return $paginator;
    }
}
