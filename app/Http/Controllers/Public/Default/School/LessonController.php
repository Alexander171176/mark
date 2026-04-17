<?php

namespace App\Http\Controllers\Public\Default\School;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\Lesson\LessonResource;
use App\Models\Admin\School\Lesson\Lesson;
use App\Traits\Public\BuildsLearningCategoryTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для показа уроков в публичной части.
 *
 * Паттерн:
 * - список всех уроков
 * - показ конкретного урока
 * - серверный поиск
 * - серверная сортировка
 * - серверная пагинация
 * - лайки
 * - trackTree для левого меню
 * - left/right/main sidebar blocks
 */
class LessonController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsLearningCategoryTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Список уроков.
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

        $lessons = Lesson::query()
            ->active()
            ->byLocale($locale)
            ->published()
            ->search($search)
            ->with([
                'module' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published()
                    ->with([
                        'course' => fn ($courseQ) => $courseQ
                            ->active()
                            ->byLocale($locale)
                            ->published()
                            ->with([
                                'instructorProfile.images' => fn ($imgQ) =>
                                $imgQ->orderBy('instructor_profile_has_images.order', 'asc'),
                            ]),
                    ]),
                'images' => fn ($q) => $q->orderBy('lesson_has_images.order', 'asc'),
                'content',
            ])
            ->withCount([
                'likes',
            ])
            ->sortByParam($sort)
            ->paginate($perPage)
            ->withQueryString();

        $lessons = $this->appendUserLikes($lessons, LessonResource::class);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Lessons/Index', [
            'lessons' => $lessons,
            'lessonsCount' => $lessons->total(),
            'lessonsFound' => $lessons->total(),
            'filters' => $this->buildIndexFilters($search, $perPage, $sort),
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }

    /**
     * Показ конкретного урока.
     *
     * @param string $slug
     * @return Response
     */
    public function show(string $slug): Response
    {
        $locale = app()->getLocale();

        $lesson = Lesson::query()
            ->active()
            ->byLocale($locale)
            ->published()
            ->where('slug', $slug)
            ->with([
                'module' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published()
                    ->with([
                        'course' => fn ($courseQ) => $courseQ
                            ->active()
                            ->byLocale($locale)
                            ->published()
                            ->with([
                                'instructorProfile.images' => fn ($imgQ) =>
                                $imgQ->orderBy('instructor_profile_has_images.order', 'asc'),
                                'images' => fn ($imgQ) =>
                                $imgQ->orderBy('course_has_images.order', 'asc'),
                                'learningCategories' => fn ($catQ) => $catQ
                                    ->active()
                                    ->byLocale($locale)
                                    ->ordered(),
                                'hashtags' => fn ($tagQ) => $tagQ
                                    ->active()
                                    ->byLocale($locale)
                                    ->ordered(),
                            ]),
                    ]),
                'images' => fn ($q) => $q->orderBy('lesson_has_images.order', 'asc'),
                'hashtags' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->ordered(),
                'content',
            ])
            ->withCount([
                'likes',
                'hashtags',
            ])
            ->firstOrFail();

        $lesson->increment('views');

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        $alreadyLiked = auth()->check()
            ? $lesson->likes()->where('user_id', auth()->id())->exists()
            : false;

        $lessonData = (new LessonResource($lesson))->resolve();
        $lessonData['already_liked'] = $alreadyLiked;

        return Inertia::render('Public/Default/School/Lessons/Show', [
            'lesson' => $lessonData,
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }

    /**
     * Лайк урока.
     *
     * @param string $lesson
     * @return JsonResponse
     */
    public function like(string $lesson): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $lesson = Lesson::findOrFail($lesson);
        $user = auth()->user();

        if ($lesson->likes()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $lesson->likes()->count(),
            ]);
        }

        $lesson->likes()->create([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $lesson->likes()->count(),
        ]);
    }
}
