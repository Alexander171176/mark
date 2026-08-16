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

        $rubrics = BlogRubricSharedResource::collection($rubrics);

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
    /** Страница конкретной рубрики блога. */
    public function show(
        Request $request,
        string $url
    ): Response {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        /**
         * Публичные настройки сайта.
         *
         * Единственный источник настроек
         * количества и сортировки статей.
         */
        $settings = app(
            PublicSettingsService::class
        );

        /**
         * Основная рубрика.
         */
        $rubric = BlogRubric::query()
            ->forPublic()
            ->where(
                'url',
                $url
            )
            ->with([
                /**
                 * Текущая локаль
                 * + fallback ru.
                 */
                'translations' => fn ($query) =>
                $query->whereIn(
                    'locale',
                    $locales
                ),

                /**
                 * Автор основной рубрики.
                 */
                'owner',

                /**
                 * Изображения + Spatie Media.
                 */
                'images.media',

                /**
                 * Дочерние публичные рубрики.
                 */
                'children' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->whereIn(
                            'locale',
                            $locales
                        ),

                        'owner',

                        'images.media',
                    ])
                    ->withCount([
                        'articles',
                    ]),
            ])
            ->withCount([
                'articles',
            ])
            ->firstOrFail();

        /**
         * Увеличиваем просмотры рубрики.
         */
        $rubric->increment('views');

        /**
         * Поиск по статьям рубрики.
         */
        $articlesSearch = $this->resolveSearch(
            $request,
            'q_articles'
        );

        /**
         * Количество статей на странице
         * берётся только из публичных настроек.
         */
        $perPageArticles = $this->resolvePerPage(
            $request,
            $settings->int(
                'publicBlogArticlesPerPage',
                12
            ),
            1,
            60
        );

        /**
         * Сортировка по умолчанию
         * также берётся из публичных настроек.
         */
        $articlesSort = (string) $request->query(
            'sort_articles',
            $settings->string(
                'publicBlogArticlesDefaultSort',
                'sortAsc'
            )
        );

        /**
         * Статьи текущей рубрики.
         */
        $articlesQuery = BlogArticle::query()
            ->forPublic()
            ->whereHas(
                'rubrics',
                function ($query) use ($rubric) {
                    $query->where(
                        'blog_rubrics.id',
                        $rubric->id
                    );
                }
            )
            ->search(
                $articlesSearch,
                $locale
            )
            ->with([
                /**
                 * Текущая локаль
                 * + fallback ru.
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
            ])
            ->withCount([
                'likes',
            ]);

        /**
         * already_liked одним EXISTS.
         *
         * Для гостя дополнительного
         * SQL-подзапроса нет.
         */
        $articlesQuery = $this->withUserLike(
            $articlesQuery
        );

        /**
         * Серверная сортировка
         * и пагинация.
         */
        $articles = $articlesQuery
            ->sortByParam(
                $articlesSort,
                $locale
            )
            ->paginate(
                $perPageArticles,
                ['*'],
                'page_articles'
            )
            ->withQueryString();

        /**
         * total() сохраняем до ResourceCollection.
         */
        $articlesFound = $articles->total();

        /**
         * Карточки статей используют
         * краткий Public Resource.
         */
        $articles = BlogArticleSharedResource::collection(
            $articles
        );

        /**
         * Дерево рубрик.
         */
        $rubricTree = $this->getRubricTree(
            $locale
        );

        /**
         * Данные сайдбаров.
         */
        $sidebarData = $this->getSidebarData(
            $locale
        );

        return Inertia::render(
            'Public/Default/Blog/BlogRubrics/Show',
            [
                /**
                 * Полный Public Resource рубрики.
                 */
                'rubric' => new BlogRubricResource(
                    $rubric
                ),

                /**
                 * Статьи рубрики.
                 */
                'articles' => $articles,
                'articlesFound' => $articlesFound,

                /**
                 * Только интерактивные фильтры.
                 *
                 * Размер страницы Vue не управляет.
                 */
                'filters' => [
                    'q_articles' =>
                        $articlesSearch,

                    'sort_articles' =>
                        $articlesSort,
                ],

                'rubricTree' => $rubricTree,
                'locale' => $locale,

                ...$sidebarData,
            ]
        );
    }

    /** Базовый запрос для списка публичных рубрик. */
    private function indexQuery(
        string $locale
    ): Builder {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        return BlogRubric::query()
            ->forPublic()
            ->with([
                /**
                 * Только текущая локаль
                 * и fallback-язык.
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
                 * Изображения + Spatie Media
                 * загружаются пакетно.
                 */
                'images.media',
            ])
            ->withCount([
                'articles',
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
