<?php

namespace App\Http\Controllers\Public\Default\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\Article\ArticleResource;
use App\Http\Resources\Admin\Blog\Tag\TagResource;
use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Tag\Tag;
use App\Traits\Public\BuildsRubricTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для показа Тега (Blog) в публичной части.
 *
 * Паттерн:
 * - показ конкретного тега
 * - список статей, привязанных к тегу
 * - поиск
 * - сортировка
 * - пагинация
 * - rubricTree для левого меню
 * - left/right/main sidebar blocks
 *
 * @version 1.1
 */
class TagController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use HasSidebarDataTrait;
    use BuildsRubricTreeTrait;

    /**
     * Страница конкретного тега.
     *
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function show(Request $request, string $slug): Response
    {
        $locale = app()->getLocale();

        $articlesSearch = $this->resolveSearch($request, 'q_articles');

        $perPageArticles = (int) $request->integer('per_page_articles', 6);
        $perPageArticles = max(3, min($perPageArticles, 60));

        $articlesSort = (string) $request->query('sort_articles', 'date_desc');

        $tag = Tag::query()
            ->forPublic($locale)
            ->whereSlug($slug)
            ->with(['owner'])
            ->withCount('articles')
            ->firstOrFail();

        $tag->increment('views');

        $articles = Article::query()
            ->forPublic($locale)
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('tags.id', $tag->id);
            })
            ->search($articlesSearch)
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
            ])
            ->withCount('likes')
            ->sortByParam($articlesSort)
            ->paginate($perPageArticles, ['*'], 'page_articles')
            ->withQueryString();

        $articles = $this->appendUserLikes($articles, ArticleResource::class);

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/Tags/Show', [
            'tag' => new TagResource($tag),
            'articles' => $articles,
            'articlesFound' => $articles->total(),
            'filters' => [
                'q_articles' => $articlesSearch,
                'per_page_articles' => $perPageArticles,
                'sort_articles' => $articlesSort,
            ],
            'rubricTree' => $rubricTree,
            'locale' => $locale,
            ...$sidebarData,
        ]);
    }
}
