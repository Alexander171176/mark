<?php

namespace App\Services\Public\Blog;

use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Rubric\Rubric;
use App\Models\Admin\Blog\Tag\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PublicArticleQueryService
{
    /**
     * Базовый публичный query статей.
     *
     * @param string $locale
     * @return Builder
     */
    public function baseQuery(string $locale): Builder
    {
        return Article::query()
            ->forPublic($locale)
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
            ]);
    }

    /**
     * Применить поиск по заголовку.
     *
     * @param Builder|BelongsToMany $query
     * @param string $search
     * @return Builder|BelongsToMany
     */
    public function applySearch(Builder|BelongsToMany $query, string $search): Builder|BelongsToMany
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
     * @param Builder|BelongsToMany $query
     * @param array{column:string,direction:string,value:string} $sort
     * @return Builder|BelongsToMany
     */
    public function applySort(Builder|BelongsToMany $query, array $sort): Builder|BelongsToMany
    {
        return $query
            ->reorder()
            ->orderBy($sort['column'], $sort['direction'])
            ->orderBy('id', 'desc');
    }

    /**
     * Пагинация.
     *
     * @param Builder|BelongsToMany $query
     * @param int $perPage
     * @param string $pageName
     * @return LengthAwarePaginator
     */
    public function paginate(
        Builder|BelongsToMany $query,
        int $perPage = 12,
        string $pageName = 'page'
    ): LengthAwarePaginator {
        return $query
            ->paginate($perPage, ['*'], $pageName)
            ->withQueryString();
    }

    /**
     * Статьи конкретной рубрики.
     *
     * @param Rubric $rubric
     * @param string $locale
     * @return BelongsToMany
     */
    public function forRubric(Rubric $rubric, string $locale): BelongsToMany
    {
        return $rubric->articles()
            ->forPublic($locale)
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
            ]);
    }

    /**
     * Статьи конкретного тега.
     *
     * @param Tag $tag
     * @param string $locale
     * @return BelongsToMany
     */
    public function forTag(Tag $tag, string $locale): BelongsToMany
    {
        return $tag->articles()
            ->forPublic($locale)
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
            ]);
    }

    /**
     * Разрешённые сортировки для статей.
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
