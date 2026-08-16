<?php

namespace App\Http\Controllers\Public\Default\Blog\BlogVideo;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Blog\BlogVideo\BlogVideoResource;
use App\Http\Resources\Public\Blog\BlogVideo\BlogVideoSharedResource;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
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

class BlogVideoController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use HasSidebarDataTrait;
    use BuildsRubricTreeTrait;

    /** Страница всех видео блога. */
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
                'title' => __('Видео'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicBlogVideosPerPage', 12)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicBlogVideosDefaultSort', 'sortAsc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicBlogVideosDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicBlogVideosProcessingMode', 'server')
        );

        $videosCount = BlogVideo::query()
            ->forPublic()
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $videosCount,
                300
            );

        $videos = $this->getIndexVideos(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        $videosFound = $useServerProcessing
            ? $videos->total()
            : $videos->count();

        /**
         * Public Index всегда использует
         * краткий Public Resource.
         */
        $videos = BlogVideoSharedResource::collection(
            $videos
        );

        $rubricTree = $this->getRubricTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/Blog/BlogVideos/Index', [

            'seo' => $seo,

            'publicBlogVideosProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'videos' => $videos,

            'videosCount' => $videosCount,
            'videosFound' => $videosFound,

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

    /** Страница конкретного видео блога. */
    public function show(
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
         * Единственный источник количества
         * показываемых карточек видео.
         */
        $settings = app(
            PublicSettingsService::class
        );

        $videosLimit = $settings->int(
            'publicBlogVideosPerPage',
            12
        );

        /**
         * Основное публичное видео.
         */
        $videoQuery = BlogVideo::query()
            ->forPublic()
            ->where(
                'url',
                $url
            )
            ->with([
                /**
                 * Current locale
                 * + fallback ru.
                 */
                'translations' => fn ($query) =>
                $query->whereIn(
                    'locale',
                    $locales
                ),

                /**
                 * Автор основного видео.
                 */
                'owner',

                /**
                 * Изображения + Spatie Media.
                 */
                'images.media',

                /**
                 * Собственный Media relation
                 * нужен для source_type = local.
                 */
                'media',

                /**
                 * Связанные публичные видео.
                 */
                'relatedVideos' => function ($query) use (
                    $locale,
                    $locales,
                    $videosLimit
                ) {
                    $query
                        ->forPublic()
                        ->with([
                            /**
                             * Current locale
                             * + fallback ru.
                             */
                            'translations' => fn ($translationQuery) =>
                            $translationQuery->whereIn(
                                'locale',
                                $locales
                            ),

                            /**
                             * Автор нужен карточке.
                             */
                            'owner',

                            /**
                             * Изображения + Spatie Media.
                             */
                            'images.media',

                            /**
                             * Собственный Media relation
                             * для local video.
                             */
                            'media',
                        ])
                        ->withCount([
                            'likes',
                        ])
                        ->sortByParam(
                            'sortAsc',
                            $locale
                        )
                        ->limit(
                            $videosLimit
                        );

                    /**
                     * already_liked одним EXISTS
                     * для связанных видео.
                     */
                    $this->withUserLike(
                        $query
                    );
                },
            ])
            ->withCount([
                'likes',
            ]);

        /**
         * already_liked одним EXISTS
         * для основного видео.
         *
         * Для гостя дополнительного
         * SQL-подзапроса нет.
         */
        $videoQuery = $this->withUserLike(
            $videoQuery
        );

        $video = $videoQuery
            ->firstOrFail();

        /**
         * Увеличиваем просмотры.
         */
        $video->increment('views');

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
            'Public/Default/Blog/BlogVideos/Show',
            [
                /**
                 * Основная сущность страницы
                 * использует полный Public Resource.
                 */
                'video' =>
                    new BlogVideoResource(
                        $video
                    ),

                /**
                 * Рекомендованные карточки
                 * используют SharedResource.
                 */
                'recommendedVideos' =>
                    BlogVideoSharedResource::collection(
                        $video->relatedVideos
                    ),

                'rubricTree' =>
                    $rubricTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /** Лайк видео. */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $video = BlogVideo::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = auth()->id();

        $alreadyLiked = $video->likes()
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyLiked) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $video->likes()->count(),
            ]);
        }

        $video->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $video->likes()->count(),
        ]);
    }

    /** Базовый запрос для списка публичных видео. */
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

        return BlogVideo::query()
            ->forPublic()
            ->with([
                /**
                 * Только current locale
                 * + fallback ru.
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
                 * Изображения + Spatie Media.
                 */
                'images.media',

                /**
                 * Собственный Media relation
                 * для local video.
                 */
                'media',
            ])
            ->withCount([
                'likes',
                'comments',
            ]);
    }

    /** Получение списка публичных видео по активному режиму обработки. */
    private function getIndexVideos(
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
         * already_liked одним EXISTS.
         *
         * Для гостя дополнительного
         * SQL-подзапроса нет.
         */
        $query = $this->withUserLike(
            $query
        );

        /**
         * Server mode:
         * поиск, сортировка и пагинация
         * выполняются SQL.
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
         * Frontend mode:
         *
         * отдаём полную публичную коллекцию.
         * Поиск/сортировку/пагинацию
         * выполняет Vue.
         */
        return $query
            ->orderBy(
                'sort',
                'asc'
            )
            ->orderByDesc(
                'id'
            )
            ->get();
    }
}
