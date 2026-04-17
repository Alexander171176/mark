<?php

namespace App\Http\Controllers\Public\Default\School;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\Module\ModuleResource;
use App\Models\Admin\School\Module\Module;
use App\Traits\Public\BuildsLearningCategoryTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Контроллер для показа модулей в публичной части.
 *
 * Паттерн:
 * - список всех модулей
 * - показ конкретного модуля
 * - серверный поиск
 * - серверная сортировка
 * - серверная пагинация
 * - лайки
 * - trackTree для левого меню
 * - left/right/main sidebar blocks
 *
 * @version 1.1
 * @author Александр Косолапов
 */
class ModuleController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsLearningCategoryTreeTrait;
    use HasSidebarDataTrait;

    /**
     * Список модулей.
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

        $modules = Module::query()
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
                    ]),

                'images' => fn ($q) =>
                $q->orderBy('module_has_images.order', 'asc'),

                'lessons' => fn ($q) => $q
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
            ->sortByParam($sort)
            ->paginate($perPage)
            ->withQueryString();

        $modules = $this->appendUserLikes($modules, ModuleResource::class);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Modules/Index', [
            'modules' => $modules,
            'modulesCount' => $modules->total(),
            'modulesFound' => $modules->total(),
            'filters' => $this->buildIndexFilters($search, $perPage, $sort),
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }

    /**
     * Показ конкретного модуля.
     *
     * @param string $slug
     * @return Response
     */
    public function show(string $slug): Response
    {
        $locale = app()->getLocale();

        $module = Module::query()
            ->active()
            ->byLocale($locale)
            ->published()
            ->where('slug', $slug)
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
                        'learningCategories' => fn ($catQ) => $catQ
                            ->active()
                            ->byLocale($locale)
                            ->ordered(),
                        'hashtags' => fn ($tagQ) => $tagQ
                            ->active()
                            ->byLocale($locale)
                            ->ordered(),
                    ]),

                'images' => fn ($q) =>
                $q->orderBy('module_has_images.order', 'asc'),

                'lessons' => fn ($q) => $q
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
            ->firstOrFail();

        $module->increment('views');

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        $alreadyLiked = auth()->check()
            ? $module->likes()->where('user_id', auth()->id())->exists()
            : false;

        return Inertia::render('Public/Default/School/Modules/Show', [
            'module' => array_merge(
                (new ModuleResource($module))->resolve(),
                ['already_liked' => $alreadyLiked]
            ),
            'trackTree' => $trackTree,
            ...$sidebarData,
        ]);
    }

    /**
     * Лайк модуля.
     *
     * @param string $module
     * @return JsonResponse
     */
    public function like(string $module): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $module = Module::findOrFail($module);
        $user = auth()->user();

        if ($module->likes()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $module->likes()->count(),
            ]);
        }

        $module->likes()->create([
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $module->likes()->count(),
        ]);
    }
}
