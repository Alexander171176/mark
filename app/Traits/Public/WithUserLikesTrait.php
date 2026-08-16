<?php

namespace App\Traits\Public;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;

trait WithUserLikesTrait
{
    /**
     * Добавить к запросу признак лайка
     * текущего авторизованного пользователя.
     *
     * Поддерживает:
     * - обычный Eloquent Builder;
     * - relation-запросы внутри eager loading.
     *
     * Для гостя дополнительный SQL EXISTS
     * не добавляется.
     */
    protected function withUserLike(
        Builder|Relation $query,
        string $relation = 'likes'
    ): Builder|Relation {
        $userId = Auth::id();

        if (!$userId) {
            return $query;
        }

        $query->withExists([
            "{$relation} as already_liked" => fn ($likesQuery) =>
            $likesQuery->where(
                'user_id',
                $userId
            ),
        ]);

        return $query;
    }
}
