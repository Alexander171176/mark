<?php

namespace App\Http\Controllers\Public\Default\School\SchoolTrack;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseResource;
use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackResource;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
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

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicSchoolTracksPerPage', 6)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicSchoolTracksDefaultSort', 'sortAsc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicSchoolTracksDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicSchoolTracksProcessingMode', 'server')
        );

        $tracksCount = SchoolTrack::query()
            ->forPublic($locale)
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $tracksCount,
                300
            );

        $tracks = $this->getIndexTracks(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        $tracksFound = $useServerProcessing
            ? $tracks->total()
            : $tracks->count();

        $tracks = $useServerProcessing
            ? $this->appendUserLikes($tracks, SchoolTrackResource::class)
            : SchoolTrackResource::collection($tracks);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolTracks/Index', [
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

        $track = SchoolTrack::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->with([
                'translation',
                'translations',
                'parent.translation',
                'parent.translations',
                'images',

                'children' => fn ($query) => $query
                    ->forPublic($locale)
                    ->with([
                        'translation',
                        'translations',
                        'images',
                    ])
                    ->withCount([
                        'children',
                        'courses',
                        'likes',
                        'images',
                    ])
                    ->ordered(),
            ])
            ->withCount([
                'children',
                'courses',
                'likes',
                'images',
            ])
            ->firstOrFail();

        $track->increment('views');

        $trackData = (new SchoolTrackResource($track))->resolve();

        $trackData['already_liked'] = auth()->check()
            ? $track->likes()->where('user_id', auth()->id())->exists()
            : false;

        if ($track->relationLoaded('children')) {
            $trackData['children'] = $track->children
                ->map(function ($child) {
                    $resolved = (new SchoolTrackResource($child))->resolve();

                    $resolved['already_liked'] = auth()->check()
                        ? $child->likes()->where('user_id', auth()->id())->exists()
                        : false;

                    return $resolved;
                })
                ->values()
                ->all();
        }

        $coursesSearch = $this->resolveSearch($request, 'q_courses');

        $perPageCourses = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicSchoolCoursesPerPage', 6),
            3,
            60
        );

        $coursesSort = (string) $request->query(
            'sort_courses',
            config('site_settings.publicSchoolCoursesDefaultSort', 'sortAsc')
        );

        $courses = $track->courses()
            ->forPublic($locale)
            ->search($coursesSearch, $locale)
            ->with([
                'translation',
                'translations',
                'images',
                'instructorProfile',
                'instructorProfile.translation',
                'instructorProfile.translations',
                'instructorProfile.images',
                'tracks.translation',
                'tracks.translations',
                'hashtags.translation',
                'hashtags.translations',
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
            ->sortByParam($coursesSort, $locale)
            ->paginate($perPageCourses, ['*'], 'page_courses')
            ->withQueryString();

        $courses = $this->appendUserLikes(
            $courses,
            SchoolCourseResource::class
        );

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolTracks/Show', [
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
        ]);
    }

    /** Лайк направления обучения. */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $track = SchoolTrack::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = auth()->id();

        if ($track->likes()->where('user_id', $userId)->exists()) {
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

    /** Базовый запрос для списка публичных направлений обучения. */
    private function indexQuery(string $locale): Builder
    {
        return SchoolTrack::query()
            ->forPublic($locale)
            ->with([
                'translation',
                'translations',
                'parent.translation',
                'parent.translations',
                'images',
            ])
            ->withCount([
                'children',
                'courses',
                'likes',
                'images',
            ]);
    }

    /** Получение списка публичных направлений обучения по активному режиму обработки. */
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
