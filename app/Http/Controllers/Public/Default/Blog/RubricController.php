<?php

namespace App\Http\Controllers\Public\Default\Blog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\Article\ArticleResource;
use App\Http\Resources\Admin\Blog\Rubric\RubricResource;
use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Rubric\Rubric;
use App\Traits\Public\BuildsRubricTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для показа Рубрик (Blog)
 * страницы списка, одиночки в публичной части.
 *
 * Паттерн:
 * - дерево (root + children)
 * - поиск
 * - сортировка
 * - пагинация
 * - images (Spatie)
 * - Показ в левой и правой колонке: Статьи Баннеры, Видео (left,right)
 * - Показ внизу страницы: Статьи Баннеры, Видео (main)
 *
 * @version 1.1
 */
class RubricController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use HasSidebarDataTrait;
    use BuildsRubricTreeTrait;

    /**
     * Страница всех рубрик.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $perPage = $this->resolvePerPage($request);
        $search = $this->resolveSearch($request);
        $sort = $this->resolveSort($request, 'date_desc');

        $rubrics = Rubric::query()
            ->forPublic($locale)
            ->search($search)
            ->with([
                'owner',
                'images' => fn ($q) => $q->orderBy('order'),
            ])
            ->withCount('articles')
            ->sortByParam($sort)
            ->paginate($perPage)
            ->withQueryString();

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/Rubrics/Index', [
            'rubrics' => RubricResource::collection($rubrics),
            'rubricsCount' => Rubric::query()
                ->forPublic($locale)
                ->count(),
            'rubricsFound' => $rubrics->total(),
            'filters' => $this->buildIndexFilters($search, $perPage, $sort),
            'rubricTree' => $rubricTree,
            'locale' => $locale,
            ...$sidebarData,
        ]);
    }

    /**
     * Страница конкретной рубрики.
     *
     * @param Request $request
     * @param string $url
     * @return Response
     */
    public function show(Request $request, string $url): Response
    {
        $locale = app()->getLocale();

        $rubric = Rubric::query()
            ->forPublic($locale)
            ->where('url', $url)
            ->with([
                'owner',
                'images',
                'children' => fn ($q) => $q
                    ->forPublic($locale)
                    ->with([
                        'owner',
                        'images' => fn ($imgQ) => $imgQ->orderBy('order'),
                    ])
                    ->withCount('articles')
                    ->ordered(),
            ])
            ->withCount('articles')
            ->firstOrFail();

        $rubric->increment('views');

        $articlesSearch = $this->resolveSearch($request, 'q_articles');

        $perPageArticles = (int) $request->integer('per_page_articles', 6);
        $perPageArticles = max(3, min($perPageArticles, 60));

        $articlesSort = (string) $request->query('sort_articles', 'date_desc');

        $articles = Article::query()
            ->forPublic($locale)
            ->whereHas('rubrics', function ($q) use ($rubric) {
                $q->where('rubrics.id', $rubric->id);
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

        return Inertia::render('Public/Default/Blog/Rubrics/Show', [
            'rubric' => new RubricResource($rubric),
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
