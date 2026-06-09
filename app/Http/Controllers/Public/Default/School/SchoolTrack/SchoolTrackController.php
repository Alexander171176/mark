<?php

namespace App\Http\Controllers\Public\Default\School\SchoolTrack;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseResource;
use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackResource;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\WithUserLikesTrait;
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

        $perPage = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicSchoolTracksPerPage', 6)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            (string) config(
                'site_settings.publicSchoolTracksDefaultSort',
                'sortAsc'
            )
        );

        $view = $this->resolveView(
            $request,
            (string) config('site_settings.publicSchoolTracksDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            (string) config('site_settings.publicSchoolTracksProcessingMode', 'server')
        );

        $tracks = SchoolTrack::query()
            ->forPublic($locale)
            ->search($search, $locale)
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
            ])
            ->sortByParam($sort, $locale)
            ->paginate($perPage)
            ->withQueryString();

        $tracks = $this->appendUserLikes(
            $tracks,
            SchoolTrackResource::class
        );

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Tracks/Index', [
            'tracks' => $tracks,

            'tracksCount' => SchoolTrack::query()
                ->forPublic($locale)
                ->count(),

            'tracksFound' => $tracks->total(),

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

        return Inertia::render('Public/Default/School/Tracks/Show', [
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
}
