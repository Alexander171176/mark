<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogRubric;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Public\Blog\BlogRubric\BlogRubricResource;
use App\Http\Resources\Public\Blog\BlogRubric\BlogRubricSharedResource;
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
                'title' => __('Рубрики'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(PublicSettingsService::class);

        /* ======================== Filters ======================== */

        /**
         * Единственный источник истины.
         *
         * Public-пользователь количество
         * рубрик на странице не регулирует.
         */
        $perPage = $settings->int(
            'publicBlogRubricsPerPage',
            12
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string(
                'publicBlogRubricsDefaultSort',
                'sortAsc'
            )
        );

        /**
         * Оставляем backend default,
         * но фактический grid/rows живёт
         * в localStorage frontend.
         */
        $view = $this->resolveView(
            $request,
            $settings->string(
                'publicBlogRubricsDefaultView',
                'grid'
            )
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string(
                'publicBlogRubricsProcessingMode',
                'server'
            )
        );

        /* ======================== Processing mode ======================== */

        $rubricsCount = null;

        /**
         * Decision COUNT нужен
         * только режиму auto.
         */
        if ($processingMode === 'auto') {
            $rubricsCount = BlogRubric::query()
                ->forPublic()
                ->count();

            $useServerProcessing = app(ProcessingModeService::class)
                ->shouldUseServer(
                    $processingMode,
                    $rubricsCount,
                    300
                );
        } else {
            $useServerProcessing =
                $processingMode === 'server';
        }

        /* ======================== Rubrics ======================== */

        $rubrics = $this->getIndexRubrics(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        if ($useServerProcessing) {
            /**
             * paginate() уже выполнил COUNT.
             */
            $rubricsFound = $rubrics->total();

            /**
             * Без поиска paginator total
             * одновременно является общим
             * количеством Public-рубрик.
             */
            if (
                $rubricsCount === null
                && $search === ''
            ) {
                $rubricsCount = $rubricsFound;
            }

            /**
             * При поиске paginator total —
             * только найденные рубрики.
             *
             * Общий total нужен нижней
             * административной панели.
             */
            if ($rubricsCount === null) {
                $rubricsCount = BlogRubric::query()
                    ->forPublic()
                    ->count();
            }
        } else {
            /**
             * Frontend уже получил
             * весь Public-набор.
             */
            $rubricsFound = $rubrics->count();
            $rubricsCount ??= $rubricsFound;
        }

        $rubrics = BlogRubricSharedResource::collection(
            $rubrics
        );

        /* ======================== Sidebars ======================== */

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        /* ======================== Response ======================== */

        return Inertia::render(
            'Public/Default/Blog/BlogRubrics/Index',
            [
                'seo' => $seo,

                'publicBlogRubricsProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

                'rubrics' =>
                    $rubrics,

                'rubricsCount' =>
                    $rubricsCount,

                'rubricsFound' =>
                    $rubricsFound,

                'filters' => $this->buildIndexFilters(
                    $search,
                    $perPage,
                    $sort,
                    $view,
                    $processingMode
                ),

                'rubricTree' =>
                    $rubricTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /** Страница конкретной рубрики блога. */
    public function show(Request $request, string $url): Response
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

        $settings = app(PublicSettingsService::class);

        /* ======================== Rubric ======================== */

        $rubric = BlogRubric::query()
            ->forPublic()
            ->where('url', $url)
            ->with([
                'translations' => fn ($query) =>
                $query->whereIn('locale', $locales),

                'owner',

                'images.media',

                /**
                 * Только публичные дочерние рубрики.
                 */
                'children' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->whereIn('locale', $locales),

                        'owner',

                        'images.media',
                    ])
                    ->withCount([
                        /**
                         * Только Public-статьи
                         * дочерней рубрики.
                         */
                        'articles as articles_count' => fn ($articleQuery) =>
                        $articleQuery->forPublic(),
                    ]),
            ])
            ->withCount([
                /**
                 * Только Public-статьи
                 * текущей рубрики.
                 */
                'articles as articles_count' => fn ($query) =>
                $query->forPublic(),
            ])
            ->firstOrFail();

        $rubric->increment('views');

        /* ======================== Article settings ======================== */

        /**
         * Единственный источник истины.
         *
         * Public-пользователь количество
         * статей на странице не регулирует.
         */
        $perPageArticles = $settings->int(
            'publicBlogArticlesPerPage',
            12
        );

        $articlesSearch = $this->resolveSearch(
            $request,
            'q_articles'
        );

        $articlesSort = (string) $request->query(
            'sort_articles',
            $settings->string(
                'publicBlogArticlesDefaultSort',
                'sortAsc'
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
         * Предварительный COUNT нужен
         * только режиму auto.
         */
        if ($processingMode === 'auto') {
            $articlesCount = BlogArticle::query()
                ->forPublic()
                ->whereHas(
                    'rubrics',
                    fn ($query) =>
                    $query->where(
                        'blog_rubrics.id',
                        $rubric->id
                    )
                )
                ->count();

            $useServerProcessing = app(ProcessingModeService::class)
                ->shouldUseServer(
                    $processingMode,
                    $articlesCount,
                    300
                );
        } else {
            $useServerProcessing =
                $processingMode === 'server';
        }

        /* ======================== Articles ======================== */

        $articles = $this->getRubricArticles(
            rubric: $rubric,
            locale: $locale,
            locales: $locales,
            useServerProcessing: $useServerProcessing,
            perPage: $perPageArticles,
            sort: $articlesSort,
            search: $articlesSearch,
        );

        if ($useServerProcessing) {
            /**
             * paginate() уже выполнил COUNT.
             */
            $articlesFound = $articles->total();

            /**
             * Без поиска paginator total
             * является одновременно общим
             * количеством Public-статей рубрики.
             */
            if (
                $articlesCount === null
                && $articlesSearch === ''
            ) {
                $articlesCount = $articlesFound;
            }

            /**
             * При поиске paginator total
             * содержит только найденные статьи.
             *
             * Общий Public count нужен
             * административной панели.
             */
            if ($articlesCount === null) {
                $articlesCount = BlogArticle::query()
                    ->forPublic()
                    ->whereHas(
                        'rubrics',
                        fn ($query) =>
                        $query->where(
                            'blog_rubrics.id',
                            $rubric->id
                        )
                    )
                    ->count();
            }
        } else {
            /**
             * Frontend уже получил
             * весь Public-набор.
             */
            $articlesFound = $articles->count();
            $articlesCount ??= $articlesFound;
        }

        $articles = BlogArticleSharedResource::collection(
            $articles
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
            'Public/Default/Blog/BlogRubrics/Show',
            [
                'rubric' => new BlogRubricResource(
                    $rubric
                ),

                'publicBlogArticlesProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

                'articles' =>
                    $articles,

                'articlesCount' =>
                    $articlesCount,

                'articlesFound' =>
                    $articlesFound,

                'filters' => [
                    'q_articles' =>
                        $articlesSearch,

                    'per_page_articles' =>
                        $perPageArticles,

                    'sort_articles' =>
                        $articlesSort,
                ],

                'rubricTree' =>
                    $rubricTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /**
     * Базовый запрос Public-статей
     * конкретной рубрики.
     */
    private function rubricArticlesQuery(
        BlogRubric $rubric,
        string $locale,
        array $locales
    ): Builder {
        $query = BlogArticle::query()
            ->forPublic()
            ->whereHas(
                'rubrics',
                fn ($query) =>
                $query->where(
                    'blog_rubrics.id',
                    $rubric->id
                )
            )
            ->with([
                /**
                 * Current locale + fallback.
                 */
                'translations' => fn ($query) =>
                $query->whereIn(
                    'locale',
                    $locales
                ),

                /**
                 * Автор нужен карточкам.
                 */
                'owner',

                /**
                 * Изображения + Spatie Media.
                 */
                'images.media',

                /**
                 * Рубрики нужны единому
                 * BlogArticleSharedResource
                 * и frontend-поиску.
                 */
                'rubrics.translations' => fn ($query) =>
                $query->whereIn(
                    'locale',
                    $locales
                ),
            ])
            ->withCount([
                'likes',
            ]);

        /**
         * already_liked одним EXISTS.
         */
        return $this->withUserLike(
            $query
        );
    }

    /**
     * Получение статей рубрики
     * по активному режиму обработки.
     */
    private function getRubricArticles(
        BlogRubric $rubric,
        string $locale,
        array $locales,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->rubricArticlesQuery(
            rubric: $rubric,
            locale: $locale,
            locales: $locales,
        );

        /**
         * Server:
         * поиск / сортировка / pagination
         * выполняются backend.
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
                    $perPage,
                    ['*'],
                    'page_articles'
                )
                ->withQueryString();
        }

        /**
         * Frontend:
         * отдаём весь Public-набор.
         */
        return $query
            ->sortByParam(
                $sort,
                $locale
            )
            ->get();
    }

    /** Базовый запрос для списка публичных рубрик. */
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

        return BlogRubric::query()
            ->forPublic()
            ->with([
                /**
                 * Current locale + fallback.
                 */
                'translations' => fn ($query) =>
                $query->whereIn(
                    'locale',
                    $locales
                ),

                /**
                 * Автор нужен карточке
                 * и frontend-поиску.
                 */
                'owner',

                /**
                 * Изображения + Spatie Media.
                 */
                'images.media',
            ])
            ->withCount([
                /**
                 * Только Public-статьи.
                 */
                'articles as articles_count' => fn ($query) =>
                $query->forPublic(),
            ]);
    }

    /** Получение списка публичных рубрик по активному режиму обработки. */
    private function getIndexRubrics(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery(
            $locale
        );

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
