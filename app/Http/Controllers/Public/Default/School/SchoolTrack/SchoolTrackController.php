<?php

namespace App\Http\Controllers\Public\Default\School\SchoolTrack;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Public\School\SchoolTrack\SchoolTrackResource;
use App\Http\Resources\Public\School\SchoolTrack\SchoolTrackSharedResource;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolTrackController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /** Страница списка направлений обучения. */
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
                'title' => __('Направления обучения'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(PublicSettingsService::class);

        /* ======================== Filters ======================== */

        /**
         * Единственный источник истины.
         *
         * Public-пользователь количество
         * элементов не регулирует.
         */
        $perPage = $settings->int(
            'publicSchoolTracksPerPage',
            12
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string(
                'publicSchoolTracksDefaultSort',
                'sortAsc'
            )
        );

        $view = $this->resolveView(
            $request,
            $settings->string(
                'publicSchoolTracksDefaultView',
                'grid'
            )
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string(
                'publicSchoolTracksProcessingMode',
                'server'
            )
        );

        /* ======================== Processing mode ======================== */

        $processingModeService = app(ProcessingModeService::class);
        $tracksCount = null;

        /**
         * Предварительный COUNT нужен
         * только режиму auto.
         */
        if ($processingMode === 'auto') {
            $tracksCount = SchoolTrack::query()
                ->forPublic($locale)
                ->count();

            $useServerProcessing = $processingModeService->shouldUseServer(
                $processingMode,
                $tracksCount,
                300
            );
        } else {
            $useServerProcessing = $processingMode === 'server';
        }

        /* ======================== Tracks ======================== */

        $tracks = $this->getIndexTracks(
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
            $tracksFound = $tracks->total();

            /**
             * Без поиска paginator total является
             * одновременно общим количеством.
             */
            if ($tracksCount === null && $search === '') {
                $tracksCount = $tracksFound;
            }

            /**
             * При поиске total содержит только
             * количество найденных треков.
             */
            if ($tracksCount === null) {
                $tracksCount = SchoolTrack::query()
                    ->forPublic($locale)
                    ->count();
            }
        } else {
            /**
             * get() уже получил весь Public-набор.
             */
            $tracksFound = $tracks->count();
            $tracksCount ??= $tracksFound;
        }

        $tracks = SchoolTrackSharedResource::collection($tracks);

        /* ======================== Sidebars ======================== */

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        /* ======================== Response ======================== */

        return Inertia::render('Public/Default/School/SchoolTracks/Index', [
            'seo' => $seo,

            'publicSchoolTracksProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'tracks' => $tracks,
            'tracksCount' => $tracksCount,
            'tracksFound' => $tracksFound,

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
        ]);
    }

    /** Страница конкретного направления обучения. */
    public function show(Request $request, string $slug): Response
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        $locales = array_values(array_unique([
            $locale,
            $fallbackLocale,
        ]));

        $settings = app(PublicSettingsService::class);

        /* ======================== Track ======================== */

        $trackQuery = SchoolTrack::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->with([
                'parent.translations' => fn ($query) => $query->whereIn('locale', $locales),
                'images.media',

                'children' => function ($query) use ($locale) {
                    $query
                        ->forPublic($locale)
                        ->with(['images.media'])
                        ->withCount([
                            'children',
                            'courses',
                            'likes',
                            'images',
                        ])
                        ->ordered();

                    $this->withUserLike($query);
                },
            ])
            ->withCount([
                'children',
                'courses',
                'likes',
                'images',
            ])
            ->orderByDesc('id');

        $trackQuery = $this->withUserLike($trackQuery);

        $track = $trackQuery->firstOrFail();
        $track->increment('views');

        /* ======================== Course settings ======================== */

        /**
         * Единственный источник истины.
         *
         * Public-пользователь количество
         * элементов не регулирует.
         */
        $perPageCourses = $settings->int(
            'publicSchoolCoursesPerPage',
            12
        );

        $coursesSearch = $this->resolveSearch(
            $request,
            'q_courses'
        );

        $coursesSort = (string) $request->query(
            'sort_courses',
            $settings->string('publicSchoolCoursesDefaultSort', 'idDesc')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicSchoolCoursesProcessingMode', 'server')
        );

        /* ======================== Processing mode ======================== */

        $processingModeService = app(ProcessingModeService::class);
        $coursesCount = null;

        /**
         * Предварительный COUNT нужен только auto.
         */
        if ($processingMode === 'auto') {
            $coursesCount = $track
                ->courses()
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

        /* ======================== Courses ======================== */

        $courses = $this->getTrackCourses(
            track: $track,
            locale: $locale,
            locales: $locales,
            useServerProcessing: $useServerProcessing,
            perPage: $perPageCourses,
            sort: $coursesSort,
            search: $coursesSearch,
        );

        if ($useServerProcessing) {
            $coursesFound = $courses->total();

            /**
             * Без поиска paginator total одновременно
             * является общим количеством Public-курсов.
             */
            if ($coursesCount === null && $coursesSearch === '') {
                $coursesCount = $coursesFound;
            }

            /**
             * При поиске total содержит только найденное,
             * поэтому общий coursesCount считаем отдельно.
             */
            if ($coursesCount === null) {
                $coursesCount = $track
                    ->courses()
                    ->forPublic($locale)
                    ->count();
            }
        } else {
            /**
             * Во frontend mode get() уже получил
             * весь публичный набор.
             */
            $coursesFound = $courses->count();
            $coursesCount ??= $coursesFound;
        }

        $courses = SchoolCourseSharedResource::collection($courses);

        /* ======================== Sidebars ======================== */

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        /* ======================== Response ======================== */

        return Inertia::render('Public/Default/School/SchoolTracks/Show', [
            'track' => new SchoolTrackResource($track),

            'publicSchoolCoursesProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'courses' => $courses,
            'coursesCount' => $coursesCount,
            'coursesFound' => $coursesFound,

            'filters' => [
                'q_courses' => $coursesSearch,
                'per_page_courses' => $perPageCourses,
                'sort_courses' => $coursesSort,
            ],

            'trackTree' => $trackTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /**
     * Базовый запрос публичных курсов
     * текущего направления.
     */
    private function coursesQuery(
        SchoolTrack $track,
        string $locale,
        array $locales
    ): Builder|Relation {
        $query = $track
            ->courses()
            ->forPublic($locale)
            ->with([
                'translations' => fn ($query) => $query->whereIn('locale', $locales),

                'images.media',

                'instructorProfile.translations' =>
                    fn ($query) => $query->whereIn('locale', $locales),

                'instructorProfile.images.media',

                'tracks.translations' =>
                    fn ($query) => $query->whereIn('locale', $locales),

                'hashtags.translations' =>
                    fn ($query) => $query->whereIn('locale', $locales),

                'prices',
            ])
            ->withCount([
                'modules',
                'lessons',
                'tracks',
                'hashtags',
                'images',
                'prices',
                'reviews',
                'likes',
            ]);

        return $this->withUserLike($query);
    }

    /**
     * Получение курсов направления
     * по активному режиму обработки.
     */
    private function getTrackCourses(
        SchoolTrack $track,
        string $locale,
        array $locales,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->coursesQuery(
            track: $track,
            locale: $locale,
            locales: $locales,
        );

        if ($useServerProcessing) {
            return $query
                ->search($search, $locale)
                ->sortByParam($sort, $locale)
                ->paginate(
                    $perPage,
                    ['*'],
                    'page_courses'
                )
                ->withQueryString();
        }

        return $query
            ->sortByParam($sort, $locale)
            ->get();
    }

    /**
     * Лайк направления обучения.
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

        $track = SchoolTrack::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = auth()->id();

        if (
            $track->likes()
                ->where(
                    'user_id',
                    $userId
                )
                ->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $track->likes()->count(),
            ]);
        }

        $track->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $track->likes()->count(),
        ]);
    }

    /**
     * Базовый запрос Public Index.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        $query = SchoolTrack::query()
            ->forPublic(
                $locale
            )
            ->with([
                /**
                 * Изображения + Media.
                 */
                'images.media',
            ])
            ->withCount([
                'children',
                'courses',
                'likes',
                'images',
            ]);

        /**
         * already_liked одним EXISTS.
         *
         * Для гостя дополнительного
         * SQL-подзапроса нет.
         */
        return $this->withUserLike(
            $query
        );
    }

    /**
     * Получение списка публичных направлений
     * по активному режиму обработки.
     */
    private function getIndexTracks(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery($locale);

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
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->sortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
