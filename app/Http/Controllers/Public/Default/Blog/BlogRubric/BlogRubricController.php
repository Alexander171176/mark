<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogRubric;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleResource;
use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\Blog\BuildsRubricTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
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

        $cmsSeoPage = app(CmsPageResolverService::class)
            ->resolveSeo($request->path());

        $cmsSeoTranslation = $cmsSeoPage?->translationOrFallback();

        $seo = $cmsSeoTranslation
            ? [
                'title' => $cmsSeoTranslation->meta_title ?: $cmsSeoTranslation->title,
                'keywords' => $cmsSeoTranslation->meta_keywords,
                'description' => $cmsSeoTranslation->meta_desc ?: $cmsSeoTranslation->short,
            ]
            : [
                'title' => __('Рубрики'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicBlogRubricsPerPage', 12)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicBlogRubricsDefaultSort', 'sortAsc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicBlogRubricsDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicBlogRubricsProcessingMode', 'server')
        );

        $rubricsCount = BlogRubric::query()
            ->forPublic()
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $rubricsCount,
                300
            );

        $rubrics = $this->getIndexRubrics(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        $rubricsFound = $useServerProcessing
            ? $rubrics->total()
            : $rubrics->count();

        $rubrics = BlogRubricResource::collection($rubrics);

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/BlogRubrics/Index', [

            'seo' => $seo,

            'publicBlogRubricsProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'rubrics' => $rubrics,

            'rubricsCount' => $rubricsCount,
            'rubricsFound' => $rubricsFound,

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
            config('site_settings.publicBlogArticlesDefaultSort', 'sortAsc')
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

    /** Базовый запрос для списка публичных рубрик. */
    private function indexQuery(): Builder
    {
        return BlogRubric::query()
            ->forPublic()
            ->with([
                'translations',
                'owner',
                'images',
            ])
            ->withCount('articles');
    }

    /** Получение списка публичных рубрик по активному режиму обработки. */
    private function getIndexRubrics(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery();

        if ($useServerProcessing) {
            return $query
                ->search($search, $locale)
                ->sortByParam($sort, $locale)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->sortByParam($sort, $locale)
            ->get();
    }
}
