<?php

namespace App\Http\Controllers\Public\Default\School;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\Course\CourseResource;
use App\Http\Resources\Admin\School\LearningCategory\LearningCategoryResource;
use App\Models\Admin\School\LearningCategory\LearningCategory;
use App\Traits\Public\BuildsLearningCategoryTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для показа категорий обучения (Track / LearningCategory)
 * в публичной части.
 *
 * Паттерн:
 * - дерево категорий
 * - поиск
 * - сортировка
 * - пагинация
 * - изображения категорий
 * - дочерние категории
 * - курсы категории
 * - показ левой/правой колонки и нижних блоков
 *
 * @version 1.0
 * @author Александр Косолапов
 */
class TrackController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsLearningCategoryTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Страница всех категорий обучения.
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

        $tracks = LearningCategory::query()
            ->active()
            ->byLocale($locale)
            ->search($search)
            ->with([
                'parent:id,name,slug',
                'images' => fn ($q) => $q->orderBy('learning_category_has_images.order', 'asc'),
            ])
            ->withCount([
                'children' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale),
                'courses' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published(),
                'likes',
            ])
            ->sortByParam($sort)
            ->paginate($perPage)
            ->withQueryString();

        $tracks = $this->appendUserLikes($tracks, LearningCategoryResource::class);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Tracks/Index', [
            'tracks' => $tracks,
            'tracksCount' => LearningCategory::query()
                ->active()
                ->byLocale($locale)
                ->count(),
            'tracksFound' => $tracks->total(),
            'filters' => $this->buildIndexFilters($search, $perPage, $sort),
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }

    /**
     * Страница конкретной категории обучения.
     *
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function show(Request $request, string $slug): Response
    {
        $locale = app()->getLocale();

        $track = LearningCategory::query()
            ->active()
            ->byLocale($locale)
            ->where('slug', $slug)
            ->with([
                'parent:id,name,slug',
                'images' => fn ($q) => $q->orderBy('learning_category_has_images.order', 'asc'),
                'children' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->with([
                        'images' => fn ($imgQ) => $imgQ->orderBy('learning_category_has_images.order', 'asc'),
                    ])
                    ->withCount([
                        'courses' => fn ($courseQ) => $courseQ
                            ->active()
                            ->byLocale($locale)
                            ->published(),
                        'likes',
                    ])
                    ->ordered(),
            ])
            ->withCount([
                'children' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale),
                'courses' => fn ($q) => $q
                    ->active()
                    ->byLocale($locale)
                    ->published(),
                'likes',
            ])
            ->firstOrFail();

        $track->increment('views');

        $trackData = (new LearningCategoryResource($track))->resolve();

        $trackData['already_liked'] = auth()->check()
            ? $track->likes()->where('user_id', auth()->id())->exists()
            : false;

        if ($track->relationLoaded('children')) {
            $trackData['children'] = $track->children->map(function ($child) {
                $resolved = (new LearningCategoryResource($child))->resolve();

                $resolved['already_liked'] = auth()->check()
                    ? $child->likes()->where('user_id', auth()->id())->exists()
                    : false;

                return $resolved;
            })->values()->all();
        }

        $coursesSearch = $this->resolveSearch($request, 'q_courses');

        $perPageCourses = (int) $request->integer('per_page_courses', 12);
        $perPageCourses = max(3, min($perPageCourses, 60));

        $coursesSort = (string) $request->query('sort_courses', 'date_desc');

        $courses = $track->courses()
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
            ...$sidebarData,
        ]);
    }

    /**
     * Лайк трека.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $track = LearningCategory::findOrFail($id);
        $user = auth()->user();

        if ($track->likes()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $track->likes()->count(),
            ]);
        }

        $track->likes()->create([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $track->likes()->count(),
        ]);
    }
}
