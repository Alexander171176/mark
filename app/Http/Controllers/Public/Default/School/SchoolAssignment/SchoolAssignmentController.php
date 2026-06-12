<?php

namespace App\Http\Controllers\Public\Default\School\SchoolAssignment;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolAssignment\SchoolAssignmentResource;
use App\Models\Admin\School\SchoolAssignment\SchoolAssignment;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
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

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicSchoolAssignmentsPerPage', 6)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicSchoolAssignmentsDefaultSort', 'idDesc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicSchoolAssignmentsDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicSchoolAssignmentsProcessingMode', 'server')
        );

        $assignmentsCount = SchoolAssignment::query()
            ->forPublic($locale)
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $assignmentsCount,
                300
            );

        $assignments = $this->getIndexAssignments(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        $assignmentsFound = $useServerProcessing
            ? $assignments->total()
            : $assignments->count();

        $assignments = SchoolAssignmentResource::collection($assignments);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolAssignments/Index', [
            'publicSchoolAssignmentsProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'assignments' => $assignments,

            'assignmentsCount' => $assignmentsCount,
            'assignmentsFound' => $assignmentsFound,

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

    /** Базовый запрос для списка публичных заданий. */
    private function indexQuery(string $locale): Builder
    {
        return SchoolAssignment::query()
            ->forPublic($locale)
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
            ]);
    }

    /** Получение списка публичных заданий по активному режиму обработки. */
    private function getIndexAssignments(
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
