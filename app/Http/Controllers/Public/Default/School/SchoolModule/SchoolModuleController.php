<?php

namespace App\Http\Controllers\Public\Default\School\SchoolModule;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleResource;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolModuleController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /** Страница списка модулей. */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $perPage = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicSchoolModulesPerPage', 6)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            (string) config('site_settings.publicSchoolModulesDefaultSort', 'idDesc')
        );

        $view = $this->resolveView(
            $request,
            (string) config('site_settings.publicSchoolModulesDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            (string) config('site_settings.publicSchoolModulesProcessingMode', 'server')
        );

        $modules = SchoolModule::query()
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
            ])
            ->withCount([
                'lessons',
                'likes',
                'images',
            ])
            ->sortByParam($sort, $locale)
            ->paginate($perPage)
            ->withQueryString();

        $modules = $this->appendUserLikes(
            $modules,
            SchoolModuleResource::class
        );

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Modules/Index', [
            'modules' => $modules,

            'modulesCount' => SchoolModule::query()
                ->forPublic($locale)
                ->count(),

            'modulesFound' => $modules->total(),

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

    /** Страница конкретного модуля. */
    public function show(string $slug): Response
    {
        $locale = app()->getLocale();

        $module = SchoolModule::query()
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

                'lessons' => fn ($query) => $query
                    ->forPublic($locale)
                    ->with([
                        'translation',
                        'translations',
                        'images',
                    ])
                    ->withCount([
                        'likes',
                        'images',
                    ])
                    ->ordered(),
            ])
            ->withCount([
                'lessons',
                'likes',
                'images',
            ])
            ->firstOrFail();

        $module->increment('views');

        $moduleData = (new SchoolModuleResource($module))->resolve();

        $moduleData['already_liked'] = auth()->check()
            ? $module->likes()->where('user_id', auth()->id())->exists()
            : false;

        $lessons = $module->lessons
            ->map(function ($lesson) {
                $resolved = (new SchoolLessonResource($lesson))->resolve();

                $resolved['already_liked'] = auth()->check()
                    ? $lesson->likes()->where('user_id', auth()->id())->exists()
                    : false;

                return $resolved;
            })
            ->values()
            ->all();

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Modules/Show', [
            'module' => $moduleData,
            'lessons' => $lessons,

            'trackTree' => $trackTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /** Лайк модуля. */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $module = SchoolModule::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = auth()->id();

        if ($module->likes()->where('user_id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $module->likes()->count(),
            ]);
        }

        $module->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $module->likes()->count(),
        ]);
    }
}
