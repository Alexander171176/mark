<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogRubric;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleResource;
use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Traits\Public\BuildsRubricTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogRubricController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use HasSidebarDataTrait;
    use BuildsRubricTreeTrait;

    /** Страница всех рубрик блога. */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $perPage = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicBlogRubricsPerPage', 20)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            (string) config('site_settings.publicBlogRubricsDefaultSort', 'sort')
        );

        $view = $this->resolveView(
            $request,
            (string) config('site_settings.publicBlogRubricsDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            (string) config('site_settings.publicBlogRubricsProcessingMode', 'server')
        );

        $rubrics = BlogRubric::query()
            ->forPublic()
            ->search($search, $locale)
            ->with([
                'translations',
                'owner',
                'images',
            ])
            ->withCount('articles')
            ->sortByParam($sort, $locale)
            ->paginate($perPage)
            ->withQueryString();

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/BlogRubrics/Index', [
            'rubrics' => BlogRubricResource::collection($rubrics),

            'rubricsCount' => BlogRubric::query()
                ->forPublic()
                ->count(),

            'rubricsFound' => $rubrics->total(),

            'filters' => $this->buildIndexFilters(
                $search,
                $perPage,
                $sort,
                $view,
                $processingMode
            ),

            'rubricTree' => $rubricTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /** Страница конкретной рубрики блога. */
    public function show(Request $request, string $url): Response
    {
        $locale = app()->getLocale();

        $rubric = BlogRubric::query()
            ->forPublic()
            ->where('url', $url)
            ->with([
                'translations',
                'owner',
                'images',
                'children' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations',
                        'owner',
                        'images',
                    ])
                    ->withCount('articles')
                    ->ordered(),
            ])
            ->withCount('articles')
            ->firstOrFail();

        $rubric->increment('views');

        $articlesSearch = $this->resolveSearch($request, 'q_articles');

        $perPageArticles = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicBlogArticlesPerPage', 3),
            3,
            60
        );

        $articlesSort = (string) $request->query(
            'sort_articles',
            config('site_settings.publicBlogArticlesDefaultSort', 'sort')
        );

        $articles = BlogArticle::query()
            ->forPublic()
            ->whereHas('rubrics', function ($query) use ($rubric) {
                $query->where('blog_rubrics.id', $rubric->id);
            })
            ->search($articlesSearch, $locale)
            ->with([
                'translations',
                'owner',
                'images',
            ])
            ->withCount('likes')
            ->sortByParam($articlesSort, $locale)
            ->paginate($perPageArticles, ['*'], 'page_articles')
            ->withQueryString();

        $articles = $this->appendUserLikes($articles, BlogArticleResource::class);

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/BlogRubrics/Show', [
            'rubric' => new BlogRubricResource($rubric),

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
