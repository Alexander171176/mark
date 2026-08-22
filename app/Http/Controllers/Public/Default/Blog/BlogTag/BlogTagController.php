<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogTag;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Public\Blog\BlogTag\BlogTagResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\Blog\BuildsRubricTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogTagController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use HasSidebarDataTrait;
    use BuildsRubricTreeTrait;

    /** Страница конкретного тега блога. */
    public function show(Request $request, string $slug): Response
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

        /* ======================== Tag ======================== */

        $tag = BlogTag::query()
            ->forPublic()
            ->whereSlug($slug)
            ->with([
                'translations' => fn ($query) =>
                $query->whereIn('locale', $locales),
            ])
            ->withCount([
                'articles as articles_count' => fn ($query) =>
                $query->forPublic(),
            ])
            ->firstOrFail();

        $tag->increment('views');

        /* ======================== Article settings ======================== */

        /**
         * Единственный источник истины.
         *
         * Public-пользователь количество
         * статей не регулирует.
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

        $processingModeService = app(ProcessingModeService::class);

        $articlesCount = null;

        /**
         * Предварительный COUNT нужен
         * только режиму auto.
         */
        if ($processingMode === 'auto') {
            $articlesCount = BlogArticle::query()
                ->forPublic()
                ->whereHas(
                    'tags',
                    fn ($query) =>
                    $query->where(
                        'blog_tags.id',
                        $tag->id
                    )
                )
                ->count();

            $useServerProcessing = $processingModeService->shouldUseServer(
                $processingMode,
                $articlesCount,
                300
            );
        } else {
            $useServerProcessing = $processingMode === 'server';
        }

        /* ======================== Articles ======================== */

        $articles = $this->getTagArticles(
            tag: $tag,
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
             * одновременно является общим
             * количеством Public-статей тега.
             */
            if (
                $articlesCount === null
                && $articlesSearch === ''
            ) {
                $articlesCount = $articlesFound;
            }

            /**
             * При поиске paginator total
             * содержит только найденные записи.
             *
             * Общий count нужен нижней
             * административной панели.
             */
            if ($articlesCount === null) {
                $articlesCount = BlogArticle::query()
                    ->forPublic()
                    ->whereHas(
                        'tags',
                        fn ($query) =>
                        $query->where(
                            'blog_tags.id',
                            $tag->id
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

        /**
         * Для карточек достаточно
         * краткого Public Resource.
         */
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
            'Public/Default/Blog/BlogTags/Show',
            [
                'tag' => new BlogTagResource(
                    $tag
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
     * конкретного тега.
     */
    private function tagArticlesQuery(
        BlogTag $tag,
        string $locale,
        array $locales
    ): Builder {
        $query = BlogArticle::query()
            ->forPublic()
            ->whereHas(
                'tags',
                fn ($query) =>
                $query->where(
                    'blog_tags.id',
                    $tag->id
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
                 * Рубрики нужны карточкам,
                 * поиску и отображению статьи.
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
         * already_liked одним EXISTS
         * для авторизованного пользователя.
         */
        return $this->withUserLike(
            $query
        );
    }

    /**
     * Получение статей тега
     * по активному режиму обработки.
     */
    private function getTagArticles(
        BlogTag $tag,
        string $locale,
        array $locales,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->tagArticlesQuery(
            tag: $tag,
            locale: $locale,
            locales: $locales,
        );

        /**
         * Server:
         * поиск, сортировка и пагинация
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
         * backend отдаёт весь Public-набор.
         *
         * Поиск и пагинация уже выполняются
         * внутри Show.vue.
         */
        return $query
            ->sortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
