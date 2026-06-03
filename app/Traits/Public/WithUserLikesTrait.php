<?php

namespace App\Traits\Public;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

trait WithUserLikesTrait
{
    /** Добавляет признак лайка текущего пользователя к каждому элементу пагинации. */
    protected function appendUserLikes(
        LengthAwarePaginator $paginator,
        string $resourceClass
    ): LengthAwarePaginator {
        $userId = Auth::id();

        $items = $paginator->getCollection()->map(function ($item) use ($resourceClass, $userId) {
            $resolved = (new $resourceClass($item))->resolve();

            $resolved['already_liked'] = $userId
                ? $item->likes()->where('user_id', $userId)->exists()
                : false;

            return $resolved;
        });

        $paginator->setCollection(collect($items));

        return $paginator;
    }
}
