<?php

namespace App\Http\Controllers\Public\Default\School;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\Assignment\AssignmentResource;
use App\Models\Admin\School\Assignment\Assignment;
use App\Traits\Public\BuildsLearningCategoryTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для показа заданий в публичной части.
 *
 * Паттерн:
 * - список всех заданий
 * - показ конкретного задания
 * - серверный поиск
 * - серверная сортировка
 * - серверная пагинация
 * - trackTree для левого меню
 * - left/right/main sidebar blocks
 */
class AssignmentController extends Controller
{
    use HasPublicIndexFiltersTrait;
    use BuildsLearningCategoryTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Список заданий.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $perPage = $this->resolvePerPage($request);
        $search = $this->resolveSearch($request);
        $sort = $this->resolveSort($request);

        $assignments = Assignment::query()
            ->active()
            ->byLocale($locale)
            ->published()
            ->search($search)
            ->with([
                'course' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published()
                    ->with([
                        'instructorProfile.images' => fn ($imgQ) =>
                        $imgQ->orderBy('instructor_profile_has_images.order', 'asc'),
                        'images' => fn ($imgQ) =>
                        $imgQ->orderBy('course_has_images.order', 'asc'),
                    ]),

                'module' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published()
                    ->with([
                        'course' => fn ($courseQ) => $courseQ
                            ->active()
                            ->byLocale($locale)
                            ->published(),
                    ]),

                'lesson' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published()
                    ->with([
                        'module' => fn ($moduleQ) => $moduleQ
                            ->active()
                            ->byLocale($locale)
                            ->published()
                            ->with([
                                'course' => fn ($courseQ) => $courseQ
                                    ->active()
                                    ->byLocale($locale)
                                    ->published(),
                            ]),
                    ]),

                'instructor' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->with([
                        'user',
                        'images' => fn ($imgQ) =>
                        $imgQ->orderBy('instructor_profile_has_images.order', 'asc'),
                    ]),

                'images' => fn ($q) =>
                $q->orderBy('assignment_has_images.order', 'asc'),
            ])
            ->sortByParam($sort)
            ->paginate($perPage)
            ->withQueryString();

        $assignments = AssignmentResource::collection($assignments);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Assignments/Index', [
            'assignments' => $assignments,
            'assignmentsCount' => $assignments->total(),
            'assignmentsFound' => $assignments->total(),
            'filters' => $this->buildIndexFilters($search, $perPage, $sort),
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }

    /**
     * Показ конкретного задания.
     *
     * @param string $slug
     * @return Response
     */
    public function show(string $slug): Response
    {
        $locale = app()->getLocale();

        $assignment = Assignment::query()
            ->active()
            ->byLocale($locale)
            ->published()
            ->where('slug', $slug)
            ->with([
                'course' => fn ($q) => $q->with([
                    'instructorProfile.images' => fn ($imgQ) =>
                    $imgQ->orderBy('instructor_profile_has_images.order', 'asc'),
                    'images' => fn ($imgQ) =>
                    $imgQ->orderBy('course_has_images.order', 'asc'),
                    'learningCategories' => fn ($catQ) => $catQ->ordered(),
                    'hashtags' => fn ($tagQ) => $tagQ->ordered(),
                ]),

                'module' => fn ($q) => $q->with([
                    'course',
                ]),

                'lesson' => fn ($q) => $q->with([
                    'module' => fn ($moduleQ) => $moduleQ->with([
                        'course',
                    ]),
                ]),

                'instructor' => fn ($q) => $q->with([
                    'user',
                    'images' => fn ($imgQ) =>
                    $imgQ->orderBy('instructor_profile_has_images.order', 'asc'),
                ]),

                'images' => fn ($q) =>
                $q->orderBy('assignment_has_images.order', 'asc'),
            ])
            ->firstOrFail();

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Assignments/Show', [
            'assignment' => (new AssignmentResource($assignment))->resolve(),
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }
}
