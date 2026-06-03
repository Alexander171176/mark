<?php

namespace App\Http\Controllers\Public\Default\School\SchoolHashtag;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseResource;
use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagResource;
use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
use App\Traits\Public\BuildsTrackTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolHashtagController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /** Страница списка хештегов школы. */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $perPage = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicSchoolHashtagsPerPage', 20)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            (string) config('site_settings.publicSchoolHashtagsDefaultSort', 'id_desc')
        );

        $view = $this->resolveView(
            $request,
            (string) config('site_settings.publicSchoolHashtagsDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            (string) config('site_settings.publicSchoolHashtagsProcessingMode', 'server')
        );

        $hashtags = SchoolHashtag::query()
            ->forPublic($locale)
            ->search($search, $locale)
            ->with([
                'translation',
                'translations',
            ])
            ->withCount([
                'courses',
                'modules',
                'lessons',
            ])
            ->sortByParam($sort)
            ->paginate($perPage)
            ->withQueryString();

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Hashtags/Index', [
            'hashtags' => SchoolHashtagResource::collection($hashtags),

            'hashtagsCount' => SchoolHashtag::query()
                ->forPublic($locale)
                ->count(),

            'hashtagsFound' => $hashtags->total(),

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

    /** Страница конкретного хештега школы. */
    public function show(Request $request, string $slug): Response
    {
        $locale = app()->getLocale();

        $hashtag = SchoolHashtag::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->with([
                'translation',
                'translations',
            ])
            ->withCount([
                'courses',
                'modules',
                'lessons',
            ])
            ->firstOrFail();

        $hashtag->increment('views');

        $coursesSearch = $this->resolveSearch($request, 'q_courses');

        $perPageCourses = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicSchoolCoursesPerPage', 20),
            3,
            60
        );

        $coursesSort = (string) $request->query(
            'sort_courses',
            config('site_settings.publicSchoolCoursesDefaultSort', 'sort_asc')
        );

        $courses = $hashtag->courses()
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
            ->sortByParam($coursesSort)
            ->paginate($perPageCourses, ['*'], 'page_courses')
            ->withQueryString();

        $courses = $this->appendUserLikes(
            $courses,
            SchoolCourseResource::class
        );

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Hashtags/Show', [
            'hashtag' => new SchoolHashtagResource($hashtag),

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
}
