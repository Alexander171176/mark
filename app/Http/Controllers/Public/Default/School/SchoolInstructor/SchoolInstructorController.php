<?php

namespace App\Http\Controllers\Public\Default\School\SchoolInstructor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolInstructorController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /** Страница списка инструкторов. */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $perPage = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicSchoolInstructorsPerPage', 20)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            (string) config('site_settings.publicSchoolInstructorsDefaultSort', 'idDesc')
        );

        $view = $this->resolveView(
            $request,
            (string) config('site_settings.publicSchoolInstructorsDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            (string) config('site_settings.publicSchoolInstructorsProcessingMode', 'server')
        );

        $instructorProfiles = SchoolInstructorProfile::query()
            ->forPublic($locale)
            ->search($search, $locale)
            ->with([
                'translation',
                'translations',
                'user:id,name,email',
                'images',
            ])
            ->withCount([
                'courses',
                'payouts',
            ])
            ->sortByParam($sort, $locale)
            ->paginate($perPage)
            ->withQueryString();

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolInstructors/Index', [
            'instructorProfiles' => SchoolInstructorProfileResource::collection($instructorProfiles),

            'instructorProfilesCount' => SchoolInstructorProfile::query()
                ->forPublic($locale)
                ->count(),

            'instructorProfilesFound' => $instructorProfiles->total(),

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

    /** Страница конкретного инструктора. */
    public function show(Request $request, string $slug): Response
    {
        $locale = app()->getLocale();

        $instructorProfile = SchoolInstructorProfile::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->with([
                'translation',
                'translations',
                'user:id,name,email',
                'images',
            ])
            ->withCount([
                'courses',
                'payouts',
            ])
            ->firstOrFail();

        $instructorProfile->increment('views');

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

        $courses = SchoolCourse::query()
            ->forPublic($locale)
            ->where('school_instructor_profile_id', $instructorProfile->id)
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

        return Inertia::render('Public/Default/School/SchoolInstructors/Show', [
            'instructorProfile' => new SchoolInstructorProfileResource($instructorProfile),

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
