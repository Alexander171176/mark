<?php

namespace App\Http\Controllers\Public\Default\School;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\Course\CourseResource;
use App\Http\Resources\Admin\School\InstructorProfile\InstructorProfileResource;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Traits\Public\BuildsLearningCategoryTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для показа инструкторов в публичной части.
 *
 * Паттерн:
 * - список инструкторов
 * - страница конкретного инструктора
 * - поиск
 * - сортировка
 * - пагинация
 * - sidebar blocks
 * - trackTree для левого меню
 *
 * @version 1.0
 * @author Александр Косолапов
 */
class InstructorController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsLearningCategoryTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Список инструкторов.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $perPage = $this->resolvePerPage($request, 12);
        $search = $this->resolveSearch($request);
        $sort = $this->resolveSort($request);

        $instructorProfiles = InstructorProfile::query()
            ->active()
            ->byLocale($locale)
            ->search($search)
            ->with([
                'user:id,name',
                'images' => fn ($q) => $q->orderBy('instructor_profile_has_images.order', 'asc'),
            ])
            ->withCount([
                'courses' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published(),
            ])
            ->sortByParam($sort)
            ->paginate($perPage)
            ->withQueryString();

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Instructors/Index', [
            'instructorProfiles' => InstructorProfileResource::collection($instructorProfiles),
            'instructorProfilesCount' => InstructorProfile::query()
                ->active()
                ->byLocale($locale)
                ->count(),
            'instructorProfilesFound' => $instructorProfiles->total(),
            'filters' => $this->buildIndexFilters($search, $perPage, $sort),
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }

    /**
     * Страница инструктора.
     *
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function show(Request $request, string $slug): Response
    {
        $locale = app()->getLocale();

        $instructorProfile = InstructorProfile::query()
            ->active()
            ->byLocale($locale)
            ->where('slug', $slug)
            ->with([
                'user:id,name,email',
                'images' => fn ($q) => $q->orderBy('instructor_profile_has_images.order', 'asc'),
            ])
            ->withCount([
                'courses' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published(),
            ])
            ->firstOrFail();

        $instructorProfile->increment('views');

        $qCourses = $this->resolveSearch($request, 'q_courses');

        $perPageCourses = (int) $request->integer('per_page_courses', 12);
        $perPageCourses = max(3, min($perPageCourses, 60));

        $sortCourses = (string) $request->query('sort_courses', 'date_desc');

        $courses = Course::query()
            ->active()
            ->byLocale($locale)
            ->published()
            ->where('instructor_profile_id', $instructorProfile->id)
            ->search($qCourses)
            ->with([
                'images' => fn ($q) => $q->orderBy('course_has_images.order', 'asc'),
                'instructorProfile' => fn ($q) => $q->with([
                    'images' => fn ($imgQ) => $imgQ->orderBy('instructor_profile_has_images.order', 'asc'),
                ]),
            ])
            ->withCount('likes')
            ->sortByParam($sortCourses)
            ->paginate($perPageCourses, ['*'], 'page_courses')
            ->withQueryString();

        $courses = $this->appendUserLikes($courses, CourseResource::class);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Instructors/Show', [
            'instructorProfile' => new InstructorProfileResource($instructorProfile),
            'courses' => $courses,
            'coursesFound' => $courses->total(),
            'filters' => [
                'q_courses' => $qCourses,
                'per_page_courses' => $perPageCourses,
                'sort_courses' => $sortCourses,
            ],
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }
}
