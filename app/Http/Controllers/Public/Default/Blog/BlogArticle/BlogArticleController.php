<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogArticle;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Blog\BlogArticle\BlogArticleResource;
use App\Http\Resources\Public\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Public\Blog\BlogRubric\BlogRubricSharedResource;
use App\Http\Resources\Public\Blog\BlogVideo\BlogVideoSharedResource;
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

        /* ======================== SEO ======================== */

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

        /* ======================== Filters ======================== */

        /**
         * Единственный источник истины.
         *
         * Public-пользователь количество
         * статей на странице не регулирует.
         */
        $perPage = $settings->int(
            'publicBlogArticlesPerPage',
            12
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string(
                'publicBlogArticlesDefaultSort',
                'sortAsc'
            )
        );

        /**
         * Backend default сохраняем,
         * фактический grid/rows хранится
         * frontend в localStorage.
         */
        $view = $this->resolveView(
            $request,
            $settings->string(
                'publicBlogArticlesDefaultView',
                'grid'
            )
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string(
                'publicBlogArticlesProcessingMode',
                'server'
            )
        );

        /* ======================== Processing mode ======================== */

        $articlesCount = null;

        /**
         * Decision COUNT нужен
         * только режиму auto.
         */
        if ($processingMode === 'auto') {
            $articlesCount = BlogArticle::query()
                ->forPublic()
                ->count();

            $useServerProcessing = app(ProcessingModeService::class)
                ->shouldUseServer(
                    $processingMode,
                    $articlesCount,
                    300
                );
        } else {
            $useServerProcessing = $processingMode === 'server';
        }

        /* ======================== Articles ======================== */

        $articles = $this->getIndexArticles(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        if ($useServerProcessing) {
            $articlesFound = $articles->total();

            /**
             * Без поиска paginator total
             * одновременно является общим
             * количеством Public-статей.
             */
            if ($articlesCount === null && $search === '') {
                $articlesCount = $articlesFound;
            }

            /**
             * При поиске paginator total
             * содержит только найденные статьи.
             */
            if ($articlesCount === null) {
                $articlesCount = BlogArticle::query()
                    ->forPublic()
                    ->count();
            }
        } else {
            $articlesFound = $articles->count();
            $articlesCount ??= $articlesFound;
        }

        $articles = BlogArticleSharedResource::collection(
            $articles
        );

        /* ======================== Sidebars ======================== */

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        /* ======================== Response ======================== */

        return Inertia::render(
            'Public/Default/Blog/BlogArticles/Index',
            [
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
            ]
        );
    }

    /** Страница конкретной статьи блога. */
    public function show(string $url): Response
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(array_unique([
            $locale,
            $fallbackLocale,
        ]));

        /* ======================== Article ======================== */

        $articleQuery = BlogArticle::query()
            ->forPublic()
            ->where('url', $url)
            ->with([
                /**
                 * Current locale + fallback.
                 */
                'translations' => fn ($query) =>
                $query->whereIn('locale', $locales),

                /**
                 * Автор.
                 */
                'owner',

                /**
                 * Изображения + Spatie Media.
                 */
                'images.media',

                /**
                 * Только публичные теги.
                 */
                'tags' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->whereIn('locale', $locales),
                    ])
                    ->ordered($locale),

                /**
                 * Только публичные рубрики.
                 */
                'rubrics' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->whereIn('locale', $locales),
                    ]),

                /**
                 * Публичные видео статьи.
                 *
                 * Для блока RecommendedVideos
                 * достаточно SharedResource.
                 */
                'videos' => function ($query) use ($locale, $locales) {
                    $query
                        ->forPublic()
                        ->with([
                            'translations' => fn ($translationQuery) =>
                            $translationQuery->whereIn('locale', $locales),

                            'owner',

                            'images.media',
                        ])
                        ->withCount([
                            'likes',
                            'comments',
                        ])
                        ->sortByParam(
                            'sortAsc',
                            $locale
                        );

                    $this->withUserLike($query);
                },

                /**
                 * Рекомендованные статьи.
                 */
                'relatedArticles' => function ($query) use ($locale, $locales) {
                    $query
                        ->forPublic()
                        ->with([
                            'translations' => fn ($translationQuery) =>
                            $translationQuery->whereIn('locale', $locales),

                            'owner',

                            'images.media',

                            'rubrics' => fn ($rubricQuery) => $rubricQuery
                                ->forPublic()
                                ->with([
                                    'translations' => fn ($translationQuery) =>
                                    $translationQuery->whereIn('locale', $locales),
                                ]),
                        ])
                        ->withCount([
                            'likes',
                        ]);

                    $this->withUserLike($query);

                    $query->sortByParam(
                        'sortAsc',
                        $locale
                    );
                },
            ])
            ->withCount([
                'likes',
            ]);

        /**
         * already_liked основной статьи
         * одним EXISTS.
         */
        $articleQuery = $this->withUserLike(
            $articleQuery
        );

        $article = $articleQuery
            ->firstOrFail();

        $article->increment('views');

        /* ======================== Breadcrumb ======================== */

        /**
         * Используем первую публичную
         * рубрику по sort.
         */
        $breadcrumbRubric = $article->rubrics
            ->unique('id')
            ->sortBy('sort')
            ->first();

        /* ======================== Related data ======================== */

        $recommendedArticles =
            BlogArticleSharedResource::collection(
                $article->relatedArticles
            );

        /**
         * Видео теперь используют
         * только Public Shared Resource.
         */
        $articleVideos =
            BlogVideoSharedResource::collection(
                $article->videos
            );

        /* ======================== Sidebars ======================== */

        $rubricTree = $this->getRubricTree(
            $locale
        );

        $sidebarData = $this->getSidebarData(
            $locale
        );

        /* ======================== Response ======================== */

        return Inertia::render(
            'Public/Default/Blog/BlogArticles/Show',
            [
                'article' => new BlogArticleResource(
                    $article
                ),

                'breadcrumbRubric' => $breadcrumbRubric
                    ? new BlogRubricSharedResource(
                        $breadcrumbRubric
                    )
                    : null,

                'recommendedArticles' =>
                    $recommendedArticles,

                'articleVideos' =>
                    $articleVideos,

                'rubricTree' =>
                    $rubricTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
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

    /** Базовый запрос Public Index статей. */
    private function indexQuery(string $locale): Builder
    {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(array_unique([
            $locale,
            $fallbackLocale,
        ]));

        $query = BlogArticle::query()
            ->forPublic()
            ->with([
                'translations' => fn ($query) =>
                $query->whereIn('locale', $locales),

                'owner',

                'images.media',
            ])
            ->withCount([
                'likes',
            ]);

        return $this->withUserLike(
            $query
        );
    }

    /** Получение Public Index статей по активному режиму. */
    private function getIndexArticles(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery(
            $locale
        );

        /**
         * Server:
         * SQL search / sort / pagination.
         */
        if ($useServerProcessing) {
            return $query
                ->search(
                    $search,
                    $locale
                )
                ->sortByParam(
                    $sort,
                    $locale
                )
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        /**
         * Frontend:
         * весь Public-набор.
         *
         * Начальную сортировку также
         * выполняем согласно настройке.
         */
        return $query
            ->sortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
