<?php

namespace App\Http\Controllers\Public\Default\School;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\Course\CourseResource;
use App\Http\Resources\Admin\School\Hashtag\HashtagResource;
use App\Models\Admin\School\Hashtag\Hashtag;
use App\Traits\Public\BuildsLearningCategoryTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для показа хештега школы в публичной части.
 *
 * Паттерн:
 * - показ конкретного хештега
 * - список курсов, привязанных к хештегу
 * - серверный поиск
 * - сортировка
 * - пагинация
 * - trackTree для левого меню
 * - left/right sidebar blocks
 *
 * @version 1.0
 * @author Александр Косолапов
 */
class HashtagController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsLearningCategoryTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Страница конкретного хештега школы.
     *
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function show(Request $request, string $slug): Response
    {
        $locale = app()->getLocale();

        $coursesSearch = $this->resolveSearch($request, 'q_courses');

        $perPageCourses = (int) $request->integer('per_page_courses', 6);
        $perPageCourses = max(3, min($perPageCourses, 60));

        $coursesSort = (string) $request->query('sort_courses', 'date_desc');

        $hashtag = Hashtag::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->withCount([
                'courses' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published(),
            ])
            ->firstOrFail();

        $hashtag->increment('views');

        $courses = $hashtag->courses()
            ->active()
            ->byLocale($locale)
            ->published()
            ->search($coursesSearch)
            ->with([
                'images' => fn ($q) => $q->orderBy('course_has_images.order', 'asc'),
                'instructorProfile' => fn ($q) => $q->with([
                    'images' => fn ($imgQ) => $imgQ->orderBy('instructor_profile_has_images.order', 'asc'),
                ]),
            ])
            ->withCount('likes')
            ->sortByParam($coursesSort)
            ->paginate($perPageCourses, ['*'], 'page_courses')
            ->withQueryString();

        $courses = $this->appendUserLikes($courses, CourseResource::class);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Hashtags/Show', [
            'hashtag' => new HashtagResource($hashtag),
            'courses' => $courses,
            'coursesFound' => $courses->total(),
            'filters' => [
                'q_courses' => $coursesSearch,
                'per_page_courses' => $perPageCourses,
                'sort_courses' => $coursesSort,
            ],
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }
}
