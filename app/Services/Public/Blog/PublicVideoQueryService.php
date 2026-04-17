<?php

namespace App\Services\Public\Blog;

use App\Models\Admin\Blog\Video\Video;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PublicVideoQueryService
{
    /**
     * Базовый публичный query видео.
     *
     * @param string $locale
     * @return Builder
     */
    public function baseQuery(string $locale): Builder
    {
        return Video::query()
            ->forPublic($locale)
            ->withCount('likes')
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
            ]);
    }

    /**
     * Поиск по заголовку видео.
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function applySearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where('title', 'like', "%{$search}%");
    }

    /**
     * Применить сортировку.
     *
     * @param Builder $query
     * @param array{column:string,direction:string,value:string} $sort
     * @return Builder
     */
    public function applySort(Builder $query, array $sort): Builder
    {
        return $query
            ->reorder()
            ->orderBy($sort['column'], $sort['direction'])
            ->orderBy('id', 'desc');
    }

    /**
     * Пагинация.
     *
     * @param Builder $query
     * @param int $perPage
     * @param string $pageName
     * @return LengthAwarePaginator
     */
    public function paginate(Builder $query, int $perPage = 12, string $pageName = 'page'): LengthAwarePaginator
    {
        return $query
            ->paginate($perPage, ['*'], $pageName)
            ->withQueryString();
    }

    /**
     * Разрешённые сортировки для видео.
     *
     * @param string|null $sort
     * @return array{column:string,direction:string,value:string}
     */
    public function resolveSort(?string $sort): array
    {
        return match ($sort) {
            'date_asc'   => ['column' => 'created_at', 'direction' => 'asc',  'value' => 'date_asc'],
            'date_desc'  => ['column' => 'created_at', 'direction' => 'desc', 'value' => 'date_desc'],
            'views_asc'  => ['column' => 'views',      'direction' => 'asc',  'value' => 'views_asc'],
            'views_desc' => ['column' => 'views',      'direction' => 'desc', 'value' => 'views_desc'],
            default      => ['column' => 'id',         'direction' => 'desc', 'value' => 'date_desc'],
        };
    }
}
