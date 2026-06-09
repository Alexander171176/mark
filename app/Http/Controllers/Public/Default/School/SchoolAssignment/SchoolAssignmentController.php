<?php

namespace App\Http\Controllers\Public\Default\School\SchoolAssignment;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolAssignment\SchoolAssignmentResource;
use App\Models\Admin\School\SchoolAssignment\SchoolAssignment;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
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

        $perPage = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicSchoolAssignmentsPerPage', 6)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            (string) config('site_settings.publicSchoolAssignmentsDefaultSort', 'idDesc')
        );

        $view = $this->resolveView(
            $request,
            (string) config('site_settings.publicSchoolAssignmentsDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            (string) config('site_settings.publicSchoolAssignmentsProcessingMode', 'server')
        );

        $assignments = SchoolAssignment::query()
            ->forPublic($locale)
            ->search($search, $locale)
            ->with([
                'translation',
                'translations',
                'images',

                'course.translation',
                'course.translations',
                'course.images',
                'course.instructorProfile.translation',
                'course.instructorProfile.translations',
                'course.instructorProfile.images',

                'module.translation',
                'module.translations',
                'module.images',
                'module.course.translation',
                'module.course.translations',

                'lesson.translation',
                'lesson.translations',
                'lesson.images',
                'lesson.module.translation',
                'lesson.module.translations',
                'lesson.module.course.translation',
                'lesson.module.course.translations',

                'instructor.translation',
                'instructor.translations',
                'instructor.user:id,name,email',
                'instructor.images',
            ])
            ->withCount([
                'submissions',
                'images',
            ])
            ->sortByParam($sort, $locale)
            ->paginate($perPage)
            ->withQueryString();

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolAssignments/Index', [
            'assignments' => SchoolAssignmentResource::collection($assignments),

            'assignmentsCount' => SchoolAssignment::query()
                ->forPublic($locale)
                ->count(),

            'assignmentsFound' => $assignments->total(),

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

    /** Страница конкретного задания. */
    public function show(string $slug): Response
    {
        $locale = app()->getLocale();

        $assignment = SchoolAssignment::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->with([
                'translation',
                'translations',
                'images',

                'course.translation',
                'course.translations',
                'course.images',
                'course.instructorProfile.translation',
                'course.instructorProfile.translations',
                'course.instructorProfile.images',
                'course.tracks.translation',
                'course.tracks.translations',
                'course.hashtags.translation',
                'course.hashtags.translations',

                'module.translation',
                'module.translations',
                'module.images',
                'module.course.translation',
                'module.course.translations',

                'lesson.translation',
                'lesson.translations',
                'lesson.images',
                'lesson.module.translation',
                'lesson.module.translations',
                'lesson.module.course.translation',
                'lesson.module.course.translations',

                'instructor.translation',
                'instructor.translations',
                'instructor.user:id,name,email',
                'instructor.images',

                'submissions',
            ])
            ->withCount([
                'submissions',
                'images',
            ])
            ->firstOrFail();

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolAssignments/Show', [
            'assignment' => new SchoolAssignmentResource($assignment),

            'trackTree' => $trackTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }
}
