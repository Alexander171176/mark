<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogArticle;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleResource;
use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricResource;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\Blog\BuildsRubricTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogArticleController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use HasSidebarDataTrait;
    use BuildsRubricTreeTrait;

    /** Страница всех статей блога. */
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
                'title' => __('Статьи'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage($request,
            $settings->int('publicBlogArticlesPerPage', 12));

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort($request,
            $settings->string('publicBlogArticlesDefaultSort', 'sortAsc'));

        $view = $this->resolveView($request,
            $settings->string('publicBlogArticlesDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicBlogArticlesProcessingMode', 'server')
        );

        $articlesCount = BlogArticle::query()
            ->forPublic()
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $articlesCount,
                300
            );

        $articles = $this->getIndexArticles(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        $articlesFound = $useServerProcessing
            ? $articles->total()
            : $articles->count();

        $articles = $useServerProcessing
            ? $this->appendUserLikes($articles, BlogArticleResource::class)
            : BlogArticleResource::collection($articles);

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/BlogArticles/Index', [

            'seo' => $seo,

            'publicBlogArticlesProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'articles' => $articles,

            'articlesCount' => $articlesCount,
            'articlesFound' => $articlesFound,

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

    /** Страница конкретной статьи блога. */
    public function show(string $url): Response
    {
        $locale = app()->getLocale();

        $article = BlogArticle::query()
            ->forPublic()
            ->where('url', $url)
            ->withCount('likes')
            ->with([
                'translations',
                'owner',
                'images',

                'tags' => fn ($query) => $query
                    ->forPublic()
                    ->with('translations')
                    ->ordered($locale),

                'rubrics' => fn ($query) => $query
                    ->forPublic()
                    ->with('translations')
                    ->ordered(),

                'videos' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations',
                        'images',
                    ])
                    ->sortByParam('sortAsc', $locale),

                'relatedArticles' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations',
                        'owner',
                        'images',
                    ])
                    ->withCount('likes')
                    ->sortByParam('sortAsc', $locale),
            ])
            ->firstOrFail();

        $article->increment('views');

        $breadcrumbRubric = $article->rubrics
            ->unique('id')
            ->sortBy('sort')
            ->first();

        $alreadyLiked = auth()->check()
            ? $article->likes()->where('user_id', auth()->id())->exists()
            : false;

        $recommendedArticles = $article->relatedArticles->map(function ($relatedArticle) {
            $resolved = (new BlogArticleResource($relatedArticle))->resolve();

            $resolved['already_liked'] = auth()->check()
                ? $relatedArticle->likes()->where('user_id', auth()->id())->exists()
                : false;

            return $resolved;
        });

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/BlogArticles/Show', [
            'article' => array_merge(
                (new BlogArticleResource($article))->resolve(),
                ['already_liked' => $alreadyLiked]
            ),

            'breadcrumbRubric' => $breadcrumbRubric
                ? (new BlogRubricResource($breadcrumbRubric))->resolve()
                : null,

            'recommendedArticles' => $recommendedArticles,

            'articleVideos' => BlogVideoResource::collection(
                $article->videos
            ),

            'rubricTree' => $rubricTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /** Лайк статьи. */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $article = BlogArticle::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = auth()->id();

        $alreadyLiked = $article->likes()
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyLiked) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $article->likes()->count(),
            ]);
        }

        $article->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $article->likes()->count(),
        ]);
    }

    /** Базовый запрос для списка публичных статей. */
    private function indexQuery(): Builder
    {
        return BlogArticle::query()
            ->forPublic()
            ->with([
                'translations',
                'owner',
                'images',
            ])
            ->withCount('likes');
    }

    /** Получение списка публичных статей по активному режиму обработки. */
    private function getIndexArticles(
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
