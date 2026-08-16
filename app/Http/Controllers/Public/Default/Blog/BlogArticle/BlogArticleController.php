<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogArticle;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoResource;
use App\Http\Resources\Public\Blog\BlogArticle\BlogArticleResource;
use App\Http\Resources\Public\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Public\Blog\BlogRubric\BlogRubricSharedResource;
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

        /**
         * Public Index всегда использует
         * краткий публичный Resource.
         */
        $articles = BlogArticleSharedResource::collection(
            $articles
        );

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

        $articleQuery = BlogArticle::query()
            ->forPublic()
            ->where(
                'url',
                $url
            )
            ->with([
                /**
                 * Текущая locale + fallback ru.
                 */
                'translations' => fn ($query) =>
                $query->whereIn(
                    'locale',
                    $locales
                ),

                'owner',

                /**
                 * Изображения + Spatie Media.
                 */
                'images.media',

                /**
                 * Публичные теги статьи.
                 *
                 * Загружаем только текущую локаль
                 * + fallback ru.
                 */
                'tags' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->whereIn(
                            'locale',
                            $locales
                        ),
                    ])
                    ->ordered($locale),

                /**
                 * Рубрики статьи.
                 */
                'rubrics' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->whereIn(
                            'locale',
                            $locales
                        ),
                    ]),

                /**
                 * Видео статьи.
                 *
                 * Public Video Resource сделаем,
                 * когда дойдём до BlogVideo.
                 */
                'videos' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->whereIn(
                            'locale',
                            $locales
                        ),

                        'images.media',
                    ])
                    ->sortByParam(
                        'sortAsc',
                        $locale
                    ),

                /**
                 * Рекомендованные статьи.
                 */
                'relatedArticles' => function ($query) use (
                    $locale,
                    $locales
                ) {
                    $query
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
                            'likes',
                        ]);

                    /**
                     * already_liked одним EXISTS
                     * для всей выборки relatedArticles.
                     */
                    $this->withUserLike(
                        $query
                    );

                    $query->sortByParam(
                        'sortAsc',
                        $locale
                    );
                },
            ])
            ->withCount([
                'likes',
            ]);

        $articleQuery = $this->withUserLike(
            $articleQuery
        );

        $article = $articleQuery
            ->firstOrFail();

        $article->increment('views');

        $breadcrumbRubric = $article->rubrics
            ->unique('id')
            ->sortBy('sort')
            ->first();

        $recommendedArticles =
            BlogArticleSharedResource::collection(
                $article->relatedArticles
            );

        $rubricTree = $this->getRubricTree(
            $locale
        );

        $sidebarData = $this->getSidebarData(
            $locale
        );

        return Inertia::render(
            'Public/Default/Blog/BlogArticles/Show',
            [
                'article' =>
                    new BlogArticleResource(
                        $article
                    ),

                'breadcrumbRubric' =>
                    $breadcrumbRubric
                        ? new BlogRubricSharedResource(
                        $breadcrumbRubric
                    )
                        : null,

                'recommendedArticles' =>
                    $recommendedArticles,

                /**
                 * Временно Admin Resource.
                 * Заменим после BlogVideo refactor.
                 */
                'articleVideos' =>
                    BlogVideoResource::collection(
                        $article->videos
                    ),

                'rubricTree' => $rubricTree,
                'locale' => $locale,

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

    /** Базовый запрос для списка публичных статей. */
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

        $query = BlogArticle::query()
            ->forPublic()
            ->with([
                /**
                 * Текущая locale + fallback ru.
                 */
                'translations' => fn ($query) =>
                $query->whereIn(
                    'locale',
                    $locales
                ),

                /**
                 * Автор нужен карточкам
                 * и frontend-поиску.
                 */
                'owner',

                /**
                 * Изображения + Spatie Media
                 * одним пакетным eager loading.
                 */
                'images.media',
            ])
            ->withCount([
                'likes',
            ]);

        /**
         * Для авторизованного пользователя
         * добавляем already_liked через EXISTS.
         *
         * Для гостя дополнительного SQL нет.
         */
        return $this->withUserLike(
            $query
        );
    }

    /** Получение списка публичных статей по активному режиму обработки. */
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
         * SQL выполняет поиск,
         * сортировку и пагинацию.
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
         * отдаём всю публичную коллекцию.
         *
         * Поиск, сортировку и пагинацию
         * выполняет Vue.
         */
        return $query
            ->ordered()
            ->get();
    }
}
