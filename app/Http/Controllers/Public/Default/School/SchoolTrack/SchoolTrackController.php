<?php

namespace App\Http\Controllers\Public\Default\School\SchoolTrack;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseResource;
use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackResource;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolTrackController extends Controller
{
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Страница списка направлений обучения.
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $cmsSeoPage = app(CmsPageResolverService::class)
            ->resolveSeo($request->path());

        $cmsSeoTranslation = $cmsSeoPage?->translationOrFallback();

        $seo = $cmsSeoTranslation
            ? [
                'title' => $cmsSeoTranslation->meta_title
                    ?: $cmsSeoTranslation->title,

                'keywords' => $cmsSeoTranslation->meta_keywords,

                'description' => $cmsSeoTranslation->meta_desc
                    ?: $cmsSeoTranslation->short,
            ]
            : [
                'title' => __('Направления обучения'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int(
                'publicSchoolTracksPerPage',
                12
            )
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

        /**
         * Предварительный COUNT нужен только
         * для автоматического режима.
         */
        $tracksCount = null;

        if ($processingMode === 'auto') {
            $tracksCount = SchoolTrack::query()
                ->forPublic($locale)
                ->count();
        }

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $tracksCount ?? 0,
                300
            );

        $tracks = $this->getIndexTracks(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        /**
         * Количество найденных направлений.
         */
        $tracksFound = $useServerProcessing
            ? $tracks->total()
            : $tracks->count();

        /**
         * Если предварительного COUNT не было,
         * используем уже полученный результат.
         */
        if ($tracksCount === null) {
            $tracksCount = $tracksFound;
        }

        $tracks = SchoolTrackResource::collection(
            $tracks
        );

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render(
            'Public/Default/School/SchoolTracks/Index',
            [
                'seo' => $seo,

                'publicSchoolTracksProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

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
            ]
        );
    }

    /**
     * Страница конкретного направления обучения.
     */
    public function show(
        Request $request,
        string $slug
    ): Response {
        $locale = app()->getLocale();
        $userId = auth()->id();

        /**
         * Основное направление.
         */
        $track = SchoolTrack::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->with([
                'parent.translations' => fn ($query) => $query->where('locale', $locale),
                'images.media',

                'children' => fn ($query) => $query
                    ->forPublic($locale)
                    ->with([
                        'images.media',
                    ])
                    ->withCount([
                        'children',
                        'courses',
                        'likes',
                        'images',
                    ])
                    ->when(
                        $userId,
                        fn ($childQuery) =>
                        $childQuery->withExists([
                            'likes as already_liked' =>
                                fn ($likesQuery) =>
                                $likesQuery->where(
                                    'user_id',
                                    $userId
                                ),
                        ])
                    )
                    ->ordered(),
            ])
            ->withCount([
                'children',
                'courses',
                'likes',
                'images',
            ])
            ->when(
                $userId,
                fn ($query) => $query->withExists([
                    'likes as already_liked' =>
                        fn ($likesQuery) =>
                        $likesQuery->where(
                            'user_id',
                            $userId
                        ),
                ])
            )
            ->firstOrFail();

        $track->increment('views');

        /**
         * SchoolTrackResource теперь получает
         * already_liked уже из основного SQL.
         */
        $trackData = (
        new SchoolTrackResource($track)
        )->resolve();

        /**
         * Дочерние направления уже содержат
         * already_liked благодаря withExists().
         */
        if ($track->relationLoaded('children')) {
            $trackData['children'] = $track->children
                ->map(
                    fn ($child) =>
                    (new SchoolTrackResource(
                        $child
                    ))->resolve()
                )
                ->values()
                ->all();
        }

        /**
         * Фильтры курсов направления.
         */
        $coursesSearch = $this->resolveSearch(
            $request,
            'q_courses'
        );

        $perPageCourses = $this->resolvePerPage(
            $request,
            (int) config(
                'site_settings.publicSchoolCoursesPerPage',
                6
            ),
            3,
            60
        );

        $coursesSort = (string) $request->query(
            'sort_courses',
            config(
                'site_settings.publicSchoolCoursesDefaultSort',
                'sortAsc'
            )
        );

        /**
         * Курсы направления.
         *
         * scopeForPublic() уже загружает translations
         * текущей локали.
         *
         * already_liked получаем через withExists(),
         * изображения — вместе с media.
         */

        $courses = $track->courses()
            ->forPublic($locale)
            ->search($coursesSearch, $locale)
            ->with([
                'images.media',

                'instructorProfile.translations' => fn ($query) =>
                $query->where('locale', $locale),

                'instructorProfile.images.media',

                'tracks.translations' => fn ($query) =>
                $query->where('locale', $locale),

                'hashtags.translations' => fn ($query) =>
                $query->where('locale', $locale),

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
            ])
            ->when(
                $userId,
                fn ($query) => $query->withExists([
                    'likes as already_liked' => fn ($likesQuery) =>
                    $likesQuery->where(
                        'user_id',
                        $userId
                    ),
                ])
            )
            ->sortByParam(
                $coursesSort,
                $locale
            )
            ->paginate(
                $perPageCourses,
                ['*'],
                'page_courses'
            )
            ->withQueryString();

        /**
         * Сериализуем курсы.
         *
         * already_liked уже получен
         * основным SQL-запросом.
         */
        $courses = SchoolCourseResource::collection($courses);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render(
            'Public/Default/School/SchoolTracks/Show',
            [
                'track' => $trackData,

                'courses' => $courses,
                'coursesFound' => $courses->total(),

                'filters' => [
                    'q_courses' => $coursesSearch,
                    'per_page_courses' => $perPageCourses,
                    'sort_courses' => $coursesSort,
                ],

                'trackTree' => $trackTree,
                'locale' => $locale,

                ...$sidebarData,
            ]
        );
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
     * Базовый запрос для списка
     * публичных направлений обучения.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        $userId = auth()->id();

        return SchoolTrack::query()
            ->forPublic($locale)
            ->with([
                'parent.translations' => fn ($query) =>
                $query->where('locale', $locale),

                'images.media',
            ])
            ->withCount([
                'children',
                'courses',
                'likes',
                'images',
            ])
            ->when(
                $userId,
                fn ($query) => $query->withExists([
                    'likes as already_liked' =>
                        fn ($likesQuery) =>
                        $likesQuery->where(
                            'user_id',
                            $userId
                        ),
                ])
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
