<?php

namespace App\Http\Controllers\Public\Default\School;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\Course\CourseResource;
use App\Http\Resources\Admin\School\Module\ModuleResource;
use App\Models\Admin\School\Course\Course;
use App\Traits\Public\BuildsLearningCategoryTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для показа курсов в публичной части.
 *
 * Паттерн:
 * - список всех курсов
 * - показ конкретного курса
 * - серверный поиск
 * - серверная сортировка
 * - серверная пагинация
 * - хлебная крошка по первой категории обучения
 * - лайки
 * - связанные курсы
 * - trackTree для левого меню
 * - left/right/main sidebar blocks
 */
class CourseController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsLearningCategoryTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Список курсов.
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

        $courses = Course::query()
            ->active()
            ->byLocale($locale)
            ->published()
            ->search($search)
            ->withCount([
                'modules',
                'lessons',
                'reviews',
                'learningCategories',
                'hashtags',
                'likes',
            ])
            ->with([
                'instructorProfile.images' => fn ($q) =>
                $q->orderBy('instructor_profile_has_images.order', 'asc'),

                'images' => fn ($q) =>
                $q->orderBy('course_has_images.order', 'asc'),

                'learningCategories' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->ordered(),

                'hashtags' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->ordered(),
            ])
            ->sortByParam($sort)
            ->paginate($perPage)
            ->withQueryString();

        $courses = $this->appendUserLikes($courses, CourseResource::class);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Courses/Index', [
            'courses' => $courses,
            'coursesCount' => $courses->total(),
            'coursesFound' => $courses->total(),
            'filters' => $this->buildIndexFilters($search, $perPage, $sort),
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }

    /**
     * Показ курса.
     *
     * @param string $slug
     * @return Response
     */
    public function show(string $slug): Response
    {
        $locale = app()->getLocale();

        $course = Course::query()
            ->active()
            ->byLocale($locale)
            ->published()
            ->where('slug', $slug)
            ->with([
                'instructorProfile.images' => fn ($q) =>
                $q->orderBy('instructor_profile_has_images.order', 'asc'),

                'images' => fn ($q) =>
                $q->orderBy('course_has_images.order', 'asc'),

                'learningCategories' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->ordered(),

                'hashtags' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->ordered(),

                'reviews' => fn ($q) => $q
                    ->published()
                    ->with('user:id,name')
                    ->latest(),

                'relatedCourses' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published()
                    ->with([
                        'instructorProfile.images' => fn ($imgQ) =>
                        $imgQ->orderBy('instructor_profile_has_images.order', 'asc'),
                        'images' => fn ($imgQ) =>
                        $imgQ->orderBy('course_has_images.order', 'asc'),
                    ])
                    ->withCount('likes')
                    ->ordered(),

                'modules' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published()
                    ->with([
                        'images' => fn ($imgQ) =>
                        $imgQ->orderBy('module_has_images.order', 'asc'),
                        'lessons' => fn ($lessonQ) => $lessonQ
                            ->active()
                            ->byLocale($locale)
                            ->published()
                            ->with([
                                'images' => fn ($imgQ) =>
                                $imgQ->orderBy('lesson_has_images.order', 'asc'),
                            ])
                            ->orderBy('sort', 'asc'),
                    ])
                    ->withCount([
                        'lessons',
                        'likes',
                    ])
                    ->ordered(),
            ])
            ->withCount([
                'likes',
                'reviews',
            ])
            ->firstOrFail();

        $course->increment('views');

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        $alreadyLiked = auth()->check()
            ? $course->likes()->where('user_id', auth()->id())->exists()
            : false;

        $recommendedCourses = $course->relatedCourses->map(function ($relatedCourse) {
            $resolved = (new CourseResource($relatedCourse))->resolve();

            $resolved['already_liked'] = auth()->check()
                ? $relatedCourse->likes()->where('user_id', auth()->id())->exists()
                : false;

            return $resolved;
        });

        $courseModules = $course->modules->map(function ($module) {
            $resolved = (new ModuleResource($module))->resolve();

            $resolved['already_liked'] = auth()->check()
                ? $module->likes()->where('user_id', auth()->id())->exists()
                : false;

            return $resolved;
        });

        $courseData = (new CourseResource($course))->resolve();
        $courseData['already_liked'] = $alreadyLiked;
        $courseData['related_courses'] = $recommendedCourses;

        return Inertia::render('Public/Default/School/Courses/Show', [
            'course' => $courseData,
            'modules' => $courseModules,
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }

    /**
     * Лайк курса.
     *
     * @param string $course
     * @return JsonResponse
     */
    public function like(string $course): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $course = Course::findOrFail($course);
        $user = auth()->user();

        if ($course->likes()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $course->likes()->count(),
            ]);
        }

        $course->likes()->create([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $course->likes()->count(),
        ]);
    }
}
