<?php

namespace App\Http\Controllers\Public\Default\School\SchoolCourse;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\School\SchoolModule\SchoolModuleSharedResource;
use App\Http\Resources\Public\School\SchoolCourse\SchoolCourseResource;
use App\Http\Resources\Public\School\SchoolCourse\SchoolCourseSharedResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
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

class SchoolCourseController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Страница списка курсов.
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        /**
         * SEO страницы.
         */
        $cmsSeoPage = app(CmsPageResolverService::class)->resolveSeo($request->path());
        $cmsSeoTranslation = $cmsSeoPage?->translationOrFallback();

        $seo = $cmsSeoTranslation
            ? [
                'title' => $cmsSeoTranslation->meta_title ?: $cmsSeoTranslation->title,
                'keywords' => $cmsSeoTranslation->meta_keywords,
                'description' => $cmsSeoTranslation->meta_desc ?: $cmsSeoTranslation->short,
            ]
            : [
                'title' => __('Курсы'),
                'keywords' => '',
                'description' => '',
            ];

        /**
         * Public-настройки.
         */
        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicSchoolCoursesPerPage', 12)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicSchoolCoursesDefaultSort', 'idDesc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicSchoolCoursesDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicSchoolCoursesProcessingMode', 'server')
        );

        /**
         * Предварительный COUNT нужен только auto.
         *
         * server:
         * paginator сам выполнит COUNT.
         *
         * frontend:
         * количество получим из Collection.
         */
        $processingModeService = app(ProcessingModeService::class);
        $coursesCount = null;

        if ($processingMode === 'auto') {
            $coursesCount = SchoolCourse::query()
                ->forPublic($locale)
                ->count();

            $useServerProcessing = $processingModeService->shouldUseServer(
                $processingMode,
                $coursesCount,
                300
            );
        } else {
            $useServerProcessing = $processingMode === 'server';
        }

        /**
         * Получаем курсы.
         */
        $courses = $this->getIndexCourses(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        if ($useServerProcessing) {
            $coursesFound = $courses->total();

            /**
             * Без поиска paginator total одновременно
             * является общим Public count.
             */
            if ($coursesCount === null && $search === '') {
                $coursesCount = $coursesFound;
            }

            /**
             * При поиске paginator total содержит
             * только количество найденных записей,
             * поэтому общий Public count считаем отдельно.
             */
            if ($coursesCount === null) {
                $coursesCount = SchoolCourse::query()
                    ->forPublic($locale)
                    ->count();
            }
        } else {
            /**
             * Frontend получил весь Public-набор.
             */
            $coursesFound = $courses->count();
            $coursesCount ??= $coursesFound;
        }

        /**
         * Public Index использует краткий Resource.
         */
        $courses = SchoolCourseSharedResource::collection($courses);

        /**
         * Данные школы.
         */
        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render(
            'Public/Default/School/SchoolCourses/Index',
            [
                'seo' => $seo,

                'publicSchoolCoursesProcessingMode' => $processingMode,
                'useServerProcessing' => $useServerProcessing,

                'courses' => $courses,
                'coursesCount' => $coursesCount,
                'coursesFound' => $coursesFound,

                'filters' => $this->buildIndexFilters(
                    $search,
                    $perPage,
                    $sort,
                    $view,
                    $processingMode
                ),

                'trackTree' => $trackTree,
                'locale' => $locale,

                ...$sidebarData,
            ]
        );
    }

    /**
     * Страница конкретного курса.
     */
    public function show(
        string $slug
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
         * Основной Public-запрос курса.
         */
        $courseQuery =
            SchoolCourse::query()
                ->forPublic(
                    $locale
                )
                ->where(
                    'slug',
                    $slug
                )
                ->with([
                    /**
                     * translations курса
                     * уже загружает forPublic().
                     */

                    /**
                     * Изображения курса.
                     */
                    'images.media',

                    /**
                     * Инструктор:
                     *
                     * current locale
                     * → fallback locale.
                     */
                    'instructorProfile.translations' =>
                        fn ($query) =>
                        $query->whereIn(
                            'locale',
                            $locales
                        ),

                    'instructorProfile.images.media',

                    /**
                     * Треки:
                     *
                     * current locale
                     * → fallback locale.
                     */
                    'tracks.translations' =>
                        fn ($query) =>
                        $query->whereIn(
                            'locale',
                            $locales
                        ),

                    'tracks.images.media',

                    /**
                     * Хештеги:
                     *
                     * current locale
                     * → fallback locale.
                     */
                    'hashtags.translations' =>
                        fn ($query) =>
                        $query->whereIn(
                            'locale',
                            $locales
                        ),

                    /**
                     * Цены.
                     */
                    'prices',

                    /**
                     * Отзывы.
                     */
                    'reviews' =>
                        fn ($query) =>
                        $query
                            ->with(
                                'user:id,name'
                            )
                            ->latest(),

                    /**
                     * Рекомендованные курсы.
                     */
                    'relatedCourses' =>
                        function ($query) use (
                            $locale,
                            $locales
                        ) {
                            $query
                                ->forPublic(
                                    $locale
                                )
                                ->with([
                                    'images.media',

                                    'instructorProfile.translations' =>
                                        fn ($translationQuery) =>
                                        $translationQuery
                                            ->whereIn(
                                                'locale',
                                                $locales
                                            ),

                                    'instructorProfile.images.media',
                                ])
                                ->withCount([
                                    'likes',
                                ])
                                ->ordered();

                            /**
                             * already_liked
                             * для всех related courses
                             * одним EXISTS.
                             */
                            $this->withUserLike(
                                $query
                            );
                        },

                    /**
                     * Модули.
                     */
                    'modules' => function ($query) use ($locale) {
                        $query
                            ->forPublic($locale)
                            ->with([
                                'images.media',
                            ])
                            ->withCount([
                                'lessons',
                                'likes',
                            ])
                            ->ordered();

                        $this->withUserLike($query);
                    },
                ])
                ->withCount([
                    'modules as modules_count' => fn ($query) =>
                    $query->forPublic($locale),

                    /**
                     * TODO SchoolLesson:
                     * после Public-рефакторинга сделать public-only.
                     */
                    'lessons',

                    /**
                     * Пока оставляем до проверки/refactoring SchoolTrack.
                     */
                    'tracks',

                    /**
                     * Hashtag уже имеет Public-контракт.
                     */
                    'hashtags as hashtags_count' => fn ($query) =>
                    $query->forPublic($locale),

                    'images',
                    'prices',

                    /**
                     * Reviews отдельно проверим по их Public-контракту.
                     */
                    'reviews',

                    'likes',
                ]);

        /**
         * already_liked курса
         * добавляется в основной SQL.
         */
        $courseQuery =
            $this->withUserLike(
                $courseQuery
            );

        $course =
            $courseQuery->firstOrFail();

        /**
         * Увеличиваем просмотры.
         */
        $course->increment(
            'views'
        );

        /**
         * Полный Public Resource курса.
         */
        $courseData =
            new SchoolCourseResource(
                $course
            );

        /**
         * Дочерние Public-модули курса.
         *
         * Для списка используем
         * краткий Public SharedResource.
         */
        $modules =
            SchoolModuleSharedResource::collection(
                $course->modules
            );

        /**
         * Данные школы.
         */
        $trackTree =
            $this->buildTrackTree(
                $locale
            );

        $sidebarData =
            $this->getSidebarData(
                $locale
            );

        return Inertia::render(
            'Public/Default/School/SchoolCourses/Show',
            [
                'course' =>
                    $courseData,

                'modules' =>
                    $modules,

                'trackTree' =>
                    $trackTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /**
     * Лайк курса.
     */
    public function like(
        string $id
    ): JsonResponse {
        if (!auth()->check()) {
            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $course =
            SchoolCourse::query()
                ->forPublic(
                    app()->getLocale()
                )
                ->findOrFail(
                    $id
                );

        $userId =
            auth()->id();

        if (
            $course
                ->likes()
                ->where(
                    'user_id',
                    $userId
                )
                ->exists()
        ) {
            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'Вы уже поставили лайк.',

                'likes' =>
                    $course
                        ->likes()
                        ->count(),
            ]);
        }

        $course
            ->likes()
            ->create([
                'user_id' =>
                    $userId,
            ]);

        return response()->json([
            'success' =>
                true,

            'likes' =>
                $course
                    ->likes()
                    ->count(),
        ]);
    }

    /**
     * Базовый запрос Public Index курсов.
     */
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

        $query =
            SchoolCourse::query()
                ->forPublic(
                    $locale
                )
                ->with([
                    /**
                     * translations курса
                     * уже загружает forPublic().
                     */

                    /**
                     * Изображения курса
                     * + Spatie Media Library.
                     */
                    'images.media',

                    /**
                     * Инструктор:
                     *
                     * current locale
                     * → fallback locale.
                     */
                    'instructorProfile.translations' =>
                        fn ($query) =>
                        $query->whereIn(
                            'locale',
                            $locales
                        ),

                    /**
                     * Изображения инструктора.
                     */
                    'instructorProfile.images.media',
                ])
                ->withCount([
                    'modules as modules_count' => fn ($query) =>
                    $query->forPublic($locale),

                    /**
                     * TODO SchoolLesson.
                     */
                    'lessons',

                    /**
                     * Пока не меняем без проверки Public SchoolTrack.
                     */
                    'tracks',

                    'hashtags as hashtags_count' => fn ($query) =>
                    $query->forPublic($locale),

                    /**
                     * Reviews пока не трогаем без проверки
                     * их публичного контракта.
                     */
                    'reviews',

                    'likes',
                ]);

        /**
         * already_liked одним EXISTS.
         *
         * Для гостя дополнительный
         * EXISTS не добавляется.
         */
        return $this->withUserLike(
            $query
        );
    }

    /**
     * Получение списка публичных курсов
     * согласно активному режиму обработки.
     */
    private function getIndexCourses(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query =
            $this->indexQuery(
                $locale
            );

        /**
         * Server:
         *
         * поиск и сортировка выполняются
         * на стороне Laravel/MySQL.
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
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        /**
         * Frontend:
         *
         * возвращаем весь публичный набор.
         *
         * Начальная сортировка должна
         * совпадать с server-режимом.
         */
        return $query
            ->publicSortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
