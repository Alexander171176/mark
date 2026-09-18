<?php

namespace App\Http\Controllers\Public\Default\School\SchoolAssignment;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\School\SchoolAssignment\SchoolAssignmentResource;
use App\Http\Resources\Public\School\SchoolAssignment\SchoolAssignmentSharedResource;
use App\Models\Admin\School\SchoolAssignment\SchoolAssignment;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\School\HasSidebarDataTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolAssignmentController extends Controller
{
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /** Страница списка заданий. */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

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
                'title' => __('Задания'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(
            PublicSettingsService::class
        );

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int(
                'publicSchoolAssignmentsPerPage',
                12
            )
        );

        $search = $this->resolveSearch(
            $request
        );

        $defaultSort = $settings->string(
            'publicSchoolAssignmentsDefaultSort',
            'idDesc'
        );

        $sort = $this->resolveSort(
            $request,
            $defaultSort
        );

        $view = $this->resolveView(
            $request,
            $settings->string(
                'publicSchoolAssignmentsDefaultView',
                'grid'
            )
        );

        $processingMode =
            $this->resolveProcessingMode(
                $settings->string(
                    'publicSchoolAssignmentsProcessingMode',
                    'server'
                )
            );

        /**
         * Общее количество Public-заданий.
         */
        $assignmentsCount =
            SchoolAssignment::query()
                ->forPublic()
                ->count();

        $useServerProcessing =
            app(ProcessingModeService::class)
                ->shouldUseServer(
                    $processingMode,
                    $assignmentsCount,
                    300
                );

        $assignments =
            $this->getIndexAssignments(
                locale: $locale,
                useServerProcessing:
                $useServerProcessing,
                perPage: $perPage,
                sort: $sort,
                search: $search,
            );

        /**
         * Количество найденных заданий.
         *
         * В server-режиме total()
         * уже учитывает Public-поиск.
         *
         * Во frontend-режиме поиск
         * выполняется во Vue.
         */
        $assignmentsFound =
            $useServerProcessing
                ? $assignments->total()
                : $assignments->count();

        $assignments =
            SchoolAssignmentSharedResource::collection(
                $assignments
            );

        $trackTree =
            $this->buildTrackTree($locale);

        $sidebarData =
            $this->getSidebarData($locale);

        return Inertia::render(
            'Public/Default/School/SchoolAssignments/Index',
            [
                'seo' => $seo,

                'publicSchoolAssignmentsProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

                'assignments' =>
                    $assignments,

                'assignmentsCount' =>
                    $assignmentsCount,

                'assignmentsFound' =>
                    $assignmentsFound,

                'filters' =>
                    $this->buildIndexFilters(
                        $search,
                        $perPage,
                        $sort,
                        $view,
                        $processingMode
                    ),

                'defaultSort' =>
                    $defaultSort,

                'trackTree' =>
                    $trackTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /** Страница конкретного задания. */
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

        $assignment =
            SchoolAssignment::query()
                ->forPublic()
                ->where(
                    'slug',
                    $slug
                )
                ->with([
                    /**
                     * Переводы задания:
                     * текущая локаль + fallback.
                     */
                    'translations' =>
                        fn ($query) =>
                        $query->whereIn(
                            'locale',
                            $locales
                        ),

                    /**
                     * Изображения задания.
                     */
                    'images',

                    /**
                     * Курс.
                     */
                    'course' =>
                        fn ($query) =>
                        $query
                            ->forPublic($locale)
                            ->with([
                                'translations' =>
                                    fn ($translationQuery) =>
                                    $translationQuery
                                        ->whereIn(
                                            'locale',
                                            $locales
                                        ),

                                'images',
                            ]),

                    /**
                     * Модуль.
                     */
                    'module' =>
                        fn ($query) =>
                        $query
                            ->forPublic($locale)
                            ->with([
                                'translations' =>
                                    fn ($translationQuery) =>
                                    $translationQuery
                                        ->whereIn(
                                            'locale',
                                            $locales
                                        ),

                                'images',
                            ]),

                    /**
                     * Урок.
                     */
                    'lesson' =>
                        fn ($query) =>
                        $query
                            ->forPublic($locale)
                            ->with([
                                'translations' =>
                                    fn ($translationQuery) =>
                                    $translationQuery
                                        ->whereIn(
                                            'locale',
                                            $locales
                                        ),

                                'images',
                            ]),

                    /**
                     * Преподаватель.
                     */
                    'instructor' =>
                        fn ($query) =>
                        $query
                            ->forPublic($locale)
                            ->with([
                                'translations' =>
                                    fn ($translationQuery) =>
                                    $translationQuery
                                        ->whereIn(
                                            'locale',
                                            $locales
                                        ),

                                'user:id,name',

                                'images',
                            ]),
                ])
                ->withCount([
                    'submissions',
                    'images',
                ])
                ->firstOrFail();

        $trackTree =
            $this->buildTrackTree($locale);

        $sidebarData =
            $this->getSidebarData($locale);

        return Inertia::render(
            'Public/Default/School/SchoolAssignments/Show',
            [
                'assignment' =>
                    new SchoolAssignmentResource(
                        $assignment
                    ),

                'trackTree' =>
                    $trackTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /** Базовый запрос Public Index заданий. */
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

        return SchoolAssignment::query()
            ->forPublic()
            ->with([
                /**
                 * Переводы задания:
                 * текущая локаль + fallback.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Изображения задания.
                 */
                'images',

                /**
                 * Курс.
                 */
                'course' =>
                    fn ($query) =>
                    $query
                        ->forPublic($locale)
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),
                        ]),

                /**
                 * Модуль.
                 */
                'module' =>
                    fn ($query) =>
                    $query
                        ->forPublic($locale)
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),
                        ]),

                /**
                 * Урок.
                 */
                'lesson' =>
                    fn ($query) =>
                    $query
                        ->forPublic($locale)
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),
                        ]),

                /**
                 * Преподаватель.
                 */
                'instructor' =>
                    fn ($query) =>
                    $query
                        ->forPublic($locale)
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),

                            'user:id,name',
                        ]),
            ])
            ->withCount([
                'submissions',
                'images',
            ]);
    }

    /**
     * Получение списка Public-заданий
     * по активному режиму обработки.
     */
    private function getIndexAssignments(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query =
            $this->indexQuery($locale);

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

        return $query
            ->publicSortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
