<?php

namespace App\Http\Controllers\Public\Default\School\SchoolLesson;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\School\SchoolLesson\SchoolLessonResource;
use App\Http\Resources\Public\School\SchoolLesson\SchoolLessonSharedResource;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\School\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolLessonController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Страница списка публичных уроков.
     *
     * Поддерживает:
     * server / frontend / auto.
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        /* ======================== SEO ======================== */

        $cmsSeoPage = app(CmsPageResolverService::class)
            ->resolveSeo($request->path());

        $cmsSeoTranslation = $cmsSeoPage?->translationOrFallback();

        $seo = $cmsSeoTranslation
            ? [
                'title' =>
                    $cmsSeoTranslation->meta_title
                        ?: $cmsSeoTranslation->title,

                'keywords' =>
                    $cmsSeoTranslation->meta_keywords,

                'description' =>
                    $cmsSeoTranslation->meta_desc
                        ?: $cmsSeoTranslation->short,
            ]
            : [
                'title' => __('Уроки'),
                'keywords' => '',
                'description' => '',
            ];

        /* ======================== Settings ======================== */

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int(
                'publicSchoolLessonsPerPage',
                12
            )
        );

        $search = $this->resolveSearch(
            $request
        );

        $sort = $this->resolveSort(
            $request,
            $settings->string(
                'publicSchoolLessonsDefaultSort',
                'idDesc'
            )
        );

        $view = $this->resolveView(
            $request,
            $settings->string(
                'publicSchoolLessonsDefaultView',
                'grid'
            )
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string(
                'publicSchoolLessonsProcessingMode',
                'server'
            )
        );

        /* ======================== Processing mode ======================== */

        $lessonsCount = null;

        /**
         * Предварительный COUNT нужен только auto,
         * потому что режим должен принять решение
         * исходя из общего количества публичных уроков.
         */
        if ($processingMode === 'auto') {
            $lessonsCount = SchoolLesson::query()
                ->forPublic($locale)
                ->count();

            $useServerProcessing = app(ProcessingModeService::class)
                ->shouldUseServer(
                    $processingMode,
                    $lessonsCount,
                    300
                );
        } else {
            $useServerProcessing =
                $processingMode === 'server';
        }

        /* ======================== Lessons ======================== */

        $lessons = $this->getIndexLessons(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        /**
         * lessonsFound:
         * количество элементов после поиска.
         *
         * lessonsCount:
         * общее количество публичных уроков
         * независимо от поискового запроса.
         */
        if ($useServerProcessing) {
            $lessonsFound = $lessons->total();

            /**
             * Если поиска нет, paginator total
             * одновременно является общим количеством.
             */
            if ($lessonsCount === null && $search === '') {
                $lessonsCount = $lessonsFound;
            }

            /**
             * При server + search нужен отдельный COUNT,
             * чтобы lessonsCount оставался общим количеством,
             * а lessonsFound — количеством найденных.
             */
            if ($lessonsCount === null) {
                $lessonsCount = SchoolLesson::query()
                    ->forPublic($locale)
                    ->count();
            }
        } else {
            $lessonsFound = $lessons->count();
            $lessonsCount ??= $lessonsFound;
        }

        $lessons =
            SchoolLessonSharedResource::collection(
                $lessons
            );

        /* ======================== Sidebars ======================== */

        $trackTree =
            $this->buildTrackTree($locale);

        $sidebarData =
            $this->getSidebarData($locale);

        /* ======================== Response ======================== */

        return Inertia::render(
            'Public/Default/School/SchoolLessons/Index',
            [
                'seo' => $seo,

                'publicSchoolLessonsProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

                'lessons' =>
                    $lessons,

                'lessonsCount' =>
                    $lessonsCount,

                'lessonsFound' =>
                    $lessonsFound,

                'filters' =>
                    $this->buildIndexFilters(
                        $search,
                        $perPage,
                        $sort,
                        $view,
                        $processingMode
                    ),

                'trackTree' =>
                    $trackTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /**
     * Страница конкретного публичного урока.
     */
    public function show(string $slug): Response
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

        /* ======================== Lesson ======================== */

        $query = SchoolLesson::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->with([
                /**
                 * Изображения урока.
                 */
                'images.media',

                /**
                 * Родительский модуль.
                 *
                 * Пока не вводим дополнительное
                 * ограничение через whereHas().
                 * Публичность родительской цепочки
                 * отдельно проверим по маршрутам
                 * и существующему контракту Show.
                 */
                'module' => function ($query) use ($locale, $locales) {
                    $query
                        ->forPublic($locale)
                        ->with([
                            'images.media',

                            /**
                             * Родительский курс.
                             */
                            'course' => function ($query) use ($locale, $locales) {
                                $query
                                    ->forPublic($locale)
                                    ->with([
                                        'images.media',

                                        'instructorProfile' => function ($query) use ($locales) {
                                            $query->with([
                                                'translations' =>
                                                    fn ($translationQuery) =>
                                                    $translationQuery->whereIn(
                                                        'locale',
                                                        $locales
                                                    ),

                                                'images.media',
                                            ]);
                                        },
                                    ])
                                    ->withCount([
                                        'modules as modules_count' =>
                                            fn (Builder $query) =>
                                            $query->forPublic($locale),

                                        /**
                                         * SchoolLesson теперь имеет
                                         * полноценный forPublic().
                                         */
                                        'lessons as lessons_count' =>
                                            fn (Builder $query) =>
                                            $query->forPublic($locale),

                                        'tracks as tracks_count' =>
                                            fn (Builder $query) =>
                                            $query->forPublic($locale),

                                        'hashtags as hashtags_count' =>
                                            fn (Builder $query) =>
                                            $query->forPublic($locale),

                                        'reviews',
                                        'likes',
                                    ]);
                            },
                        ])
                        ->withCount([
                            /**
                             * Только публичные уроки модуля.
                             */
                            'lessons as lessons_count' =>
                                fn (Builder $query) =>
                                $query->forPublic($locale),

                            'likes',
                        ]);

                    $this->withUserLike($query);
                },

                /**
                 * Только публичные хештеги урока.
                 */
                'hashtags' => function ($query) use ($locale) {
                    $query->forPublic($locale);
                },
            ])
            ->withCount([
                'likes',
                'images',

                /**
                 * Только публичные хештеги.
                 */
                'hashtags as hashtags_count' =>
                    fn (Builder $query) =>
                    $query->forPublic($locale),
            ]);

        /**
         * already_liked одним EXISTS.
         */
        $lesson = $this->withUserLike($query)
            ->firstOrFail();

        /* ======================== Views ======================== */

        $lesson->increment('views');

        /* ======================== Sidebars ======================== */

        $trackTree =
            $this->buildTrackTree($locale);

        $sidebarData =
            $this->getSidebarData($locale);

        /* ======================== Response ======================== */

        return Inertia::render(
            'Public/Default/School/SchoolLessons/Show',
            [
                'lesson' =>
                    new SchoolLessonResource(
                        $lesson
                    ),

                'trackTree' =>
                    $trackTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /**
     * Лайк публичного урока.
     */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $lesson = SchoolLesson::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = auth()->id();

        if (
            $lesson
                ->likes()
                ->where('user_id', $userId)
                ->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Вы уже поставили лайк.',

                'likes' =>
                    $lesson->likes()->count(),
            ]);
        }

        $lesson->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,

            'likes' =>
                $lesson->likes()->count(),
        ]);
    }

    /**
     * Базовый запрос списка
     * публичных уроков.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        $query = SchoolLesson::query()
            ->forPublic($locale)
            ->with([
                /**
                 * Карточке урока нужны
                 * только изображения.
                 *
                 * translations уже загружает
                 * SchoolLesson::forPublic().
                 */
                'images.media',
            ])
            ->withCount([
                'likes',
                'images',

                /**
                 * Только публичные хештеги.
                 */
                'hashtags as hashtags_count' =>
                    fn (Builder $query) =>
                    $query->forPublic($locale),
            ]);

        /**
         * already_liked одним EXISTS
         * для авторизованного пользователя.
         */
        return $this->withUserLike($query);
    }

    /**
     * Получение списка публичных уроков
     * по активному режиму обработки.
     */
    private function getIndexLessons(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query =
            $this->indexQuery($locale);

        /**
         * Server:
         * поиск, сортировка и пагинация
         * выполняются backend.
         */
        if ($useServerProcessing) {
            return $query
                ->publicSearch(
                    $search,
                    $locale
                )
                ->publicSortByParam(
                    $sort,
                    $locale
                )
                ->paginate($perPage)
                ->withQueryString();
        }

        /**
         * Frontend:
         * backend отдаёт полный публичный набор.
         *
         * Поиск и пагинация выполняются Vue.
         * Сортировка остаётся идентичной
         * server-режиму.
         */
        return $query
            ->publicSortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
