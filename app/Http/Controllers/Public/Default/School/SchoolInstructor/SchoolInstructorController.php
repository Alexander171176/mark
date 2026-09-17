<?php

namespace App\Http\Controllers\Public\Default\School\SchoolInstructor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Public\School\SchoolInstructorProfile\SchoolInstructorProfileResource;
use App\Http\Resources\Public\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\School\HasSidebarDataTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolInstructorController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Страница списка инструкторов.
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        /* ======================== SEO ======================== */

        $cmsSeoPage = app(CmsPageResolverService::class)
            ->resolveSeo($request->path());

        $cmsSeoTranslation =
            $cmsSeoPage?->translationOrFallback();

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
                'title' =>
                    __('Инструкторы'),

                'keywords' =>
                    '',

                'description' =>
                    '',
            ];

        $settings =
            app(PublicSettingsService::class);

        /* ======================== Filters ======================== */

        /**
         * Единственный источник истины.
         *
         * Public-пользователь количество
         * инструкторов не регулирует.
         */
        $perPage = $settings->int(
            'publicSchoolInstructorsPerPage',
            12
        );

        $search =
            $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string(
                'publicSchoolInstructorsDefaultSort',
                'idDesc'
            )
        );

        /**
         * Пока сохраняем view в filters,
         * чтобы Index.vue получил backend default.
         *
         * Само переключение grid/rows
         * хранится frontend в localStorage.
         */
        $view = $this->resolveView(
            $request,
            $settings->string(
                'publicSchoolInstructorsDefaultView',
                'grid'
            )
        );

        $processingMode =
            $this->resolveProcessingMode(
                $settings->string(
                    'publicSchoolInstructorsProcessingMode',
                    'server'
                )
            );

        /* ======================== Processing mode ======================== */

        $processingModeService =
            app(ProcessingModeService::class);

        $instructorProfilesCount = null;

        /**
         * Предварительный COUNT нужен
         * только режиму auto.
         */
        if ($processingMode === 'auto') {
            $instructorProfilesCount =
                SchoolInstructorProfile::query()
                    ->forPublic($locale)
                    ->count();

            $useServerProcessing =
                $processingModeService->shouldUseServer(
                    $processingMode,
                    $instructorProfilesCount,
                    300
                );
        } else {
            $useServerProcessing =
                $processingMode === 'server';
        }

        /* ======================== Instructors ======================== */

        $instructorProfiles =
            $this->getIndexInstructorProfiles(
                locale: $locale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sort,
                search: $search,
            );

        if ($useServerProcessing) {
            /**
             * paginate() уже выполнил COUNT
             * с учётом Public search.
             */
            $instructorProfilesFound =
                $instructorProfiles->total();

            /**
             * Без поиска paginator total
             * одновременно является общим
             * количеством Public-инструкторов.
             */
            if (
                $instructorProfilesCount === null
                && $search === ''
            ) {
                $instructorProfilesCount =
                    $instructorProfilesFound;
            }

            /**
             * При поиске paginator total содержит
             * только количество найденных записей.
             *
             * Поэтому общий Public count
             * считаем отдельно.
             */
            if ($instructorProfilesCount === null) {
                $instructorProfilesCount =
                    SchoolInstructorProfile::query()
                        ->forPublic($locale)
                        ->count();
            }
        } else {
            /**
             * Frontend получил весь Public-набор.
             *
             * Поиск и последующая сортировка
             * выполняются Vue.
             */
            $instructorProfilesFound =
                $instructorProfiles->count();

            $instructorProfilesCount ??=
                $instructorProfilesFound;
        }

        $instructorProfiles =
            SchoolInstructorProfileSharedResource::collection(
                $instructorProfiles
            );

        /* ======================== Sidebars ======================== */

        $trackTree =
            $this->buildTrackTree($locale);

        $sidebarData =
            $this->getSidebarData($locale);

        /* ======================== Response ======================== */

        return Inertia::render(
            'Public/Default/School/SchoolInstructors/Index',
            [
                'seo' =>
                    $seo,

                'publicSchoolInstructorsProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

                'instructorProfiles' =>
                    $instructorProfiles,

                'instructorProfilesCount' =>
                    $instructorProfilesCount,

                'instructorProfilesFound' =>
                    $instructorProfilesFound,

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
     * Страница конкретного инструктора.
     */
    public function show(
        Request $request,
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

        $settings =
            app(PublicSettingsService::class);

        /* ======================== Instructor ======================== */

        $instructorProfile =
            SchoolInstructorProfile::query()
                ->forPublic($locale)
                ->where(
                    'slug',
                    $slug
                )
                ->with([
                    /**
                     * Только current + fallback.
                     */
                    'translations' =>
                        fn ($query) =>
                        $query->whereIn(
                            'locale',
                            $locales
                        ),

                    /**
                     * Public email не нужен.
                     */
                    'user:id,name',

                    /**
                     * Изображения вместе
                     * со Spatie Media.
                     */
                    'images.media',
                ])
                ->withCount([
                    /**
                     * Только публично
                     * доступные курсы.
                     */
                    'courses as courses_count' =>
                        fn ($query) =>
                        $query->forPublic(
                            $locale
                        ),
                ])
                ->firstOrFail();

        $instructorProfile->increment(
            'views'
        );

        /* ======================== Course settings ======================== */

        /**
         * Единственный источник истины.
         */
        $perPageCourses = $settings->int(
            'publicSchoolCoursesPerPage',
            12
        );

        $coursesSearch =
            $this->resolveSearch(
                $request,
                'q_courses'
            );

        $coursesSort = (string) $request->query(
            'sort_courses',
            $settings->string(
                'publicSchoolCoursesDefaultSort',
                'sortAsc'
            )
        );

        $processingMode =
            $this->resolveProcessingMode(
                $settings->string(
                    'publicSchoolCoursesProcessingMode',
                    'server'
                )
            );

        /* ======================== Processing mode ======================== */

        $processingModeService =
            app(ProcessingModeService::class);

        $coursesCount = null;

        /**
         * Decision COUNT нужен
         * только режиму auto.
         */
        if ($processingMode === 'auto') {
            $coursesCount =
                SchoolCourse::query()
                    ->forPublic($locale)
                    ->where(
                        'school_instructor_profile_id',
                        $instructorProfile->id
                    )
                    ->count();

            $useServerProcessing =
                $processingModeService->shouldUseServer(
                    $processingMode,
                    $coursesCount,
                    300
                );
        } else {
            $useServerProcessing =
                $processingMode === 'server';
        }

        /* ======================== Courses ======================== */

        $courses =
            $this->getInstructorCourses(
                instructorProfile: $instructorProfile,
                locale: $locale,
                locales: $locales,
                useServerProcessing: $useServerProcessing,
                perPage: $perPageCourses,
                sort: $coursesSort,
                search: $coursesSearch,
            );

        if ($useServerProcessing) {
            $coursesFound =
                $courses->total();

            /**
             * Без поиска используем
             * уже выполненный paginator COUNT.
             */
            if (
                $coursesCount === null
                && $coursesSearch === ''
            ) {
                $coursesCount =
                    $coursesFound;
            }

            /**
             * При поиске paginator total
             * является количеством найденных,
             * поэтому общий Public count
             * считаем отдельно.
             */
            if ($coursesCount === null) {
                $coursesCount =
                    SchoolCourse::query()
                        ->forPublic($locale)
                        ->where(
                            'school_instructor_profile_id',
                            $instructorProfile->id
                        )
                        ->count();
            }
        } else {
            $coursesFound =
                $courses->count();

            $coursesCount ??=
                $coursesFound;
        }

        $courses =
            SchoolCourseSharedResource::collection(
                $courses
            );

        /* ======================== Sidebars ======================== */

        $trackTree =
            $this->buildTrackTree($locale);

        $sidebarData =
            $this->getSidebarData($locale);

        /* ======================== Response ======================== */

        return Inertia::render(
            'Public/Default/School/SchoolInstructors/Show',
            [
                'instructorProfile' =>
                    new SchoolInstructorProfileResource(
                        $instructorProfile
                    ),

                'publicSchoolCoursesProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

                'courses' =>
                    $courses,

                'coursesCount' =>
                    $coursesCount,

                'coursesFound' =>
                    $coursesFound,

                'filters' => [
                    'q_courses' =>
                        $coursesSearch,

                    'per_page_courses' =>
                        $perPageCourses,

                    'sort_courses' =>
                        $coursesSort,
                ],

                'trackTree' =>
                    $trackTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /**
     * Базовый запрос Public Index
     * инструкторов.
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

        return SchoolInstructorProfile::query()
            ->forPublic($locale)
            ->with([
                /**
                 * Только current + fallback.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Public email не нужен.
                 *
                 * user.name используется также
                 * frontend-поиском.
                 */
                'user:id,name',

                /**
                 * Изображения пакетно
                 * вместе со Spatie Media.
                 */
                'images.media',
            ])
            ->withCount([
                /**
                 * В Public Index считаем
                 * только публично доступные
                 * курсы инструктора.
                 */
                'courses as courses_count' =>
                    fn ($query) =>
                    $query->forPublic(
                        $locale
                    ),
            ]);
    }

    /**
     * Получение списка Public-инструкторов
     * по активному режиму обработки.
     */
    private function getIndexInstructorProfiles(
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
         *
         * Public search
         * + Public sort
         * + pagination
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
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        /**
         * Frontend:
         *
         * отдаём полный Public-набор.
         *
         * Начальный порядок формируем
         * тем же Public sort-контрактом,
         * что и в Server.
         */
        return $query
            ->publicSortByParam(
                $sort,
                $locale
            )
            ->get();
    }

    /**
     * Базовый Public-запрос курсов
     * конкретного инструктора.
     */
    private function instructorCoursesQuery(
        SchoolInstructorProfile $instructorProfile,
        string $locale,
        array $locales
    ): Builder {
        $query = SchoolCourse::query()
            ->forPublic($locale)
            ->where(
                'school_instructor_profile_id',
                $instructorProfile->id
            )
            ->with([
                'images.media',

                /**
                 * Инструктор нужен единому
                 * SchoolCourseSharedResource.
                 */
                'instructorProfile.translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                'instructorProfile.images.media',
            ])
            ->withCount([
                'modules',
                'lessons',
                'tracks',
                'hashtags',
                'reviews',
                'likes',
            ]);

        return $this->withUserLike($query);
    }

    /**
     * Получение курсов инструктора
     * по активному режиму обработки.
     */
    private function getInstructorCourses(
        SchoolInstructorProfile $instructorProfile,
        string $locale,
        array $locales,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->instructorCoursesQuery(
            instructorProfile: $instructorProfile,
            locale: $locale,
            locales: $locales,
        );

        if ($useServerProcessing) {
            return $query
                ->publicInstructorSearch(
                    $search,
                    $locale
                )
                ->publicSortByParam(
                    $sort,
                    $locale
                )
                ->paginate(
                    $perPage,
                    ['*'],
                    'page_courses'
                )
                ->withQueryString();
        }

        return $query
            ->publicSortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
