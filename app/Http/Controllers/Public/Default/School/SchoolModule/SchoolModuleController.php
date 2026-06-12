<?php

namespace App\Http\Controllers\Public\Default\School\SchoolModule;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleResource;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\School\BuildsTrackTreeTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
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

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicSchoolModulesPerPage', 6)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicSchoolModulesDefaultSort', 'idDesc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicSchoolModulesDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicSchoolModulesProcessingMode', 'server')
        );

        $modulesCount = SchoolModule::query()
            ->forPublic($locale)
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $modulesCount,
                300
            );

        $modules = $this->getIndexModules(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        $modulesFound = $useServerProcessing
            ? $modules->total()
            : $modules->count();

        $modules = $useServerProcessing
            ? $this->appendUserLikes($modules, SchoolModuleResource::class)
            : SchoolModuleResource::collection($modules);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolModules/Index', [
            'publicSchoolModulesProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'modules' => $modules,

            'modulesCount' => $modulesCount,
            'modulesFound' => $modulesFound,

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

        return Inertia::render('Public/Default/School/SchoolModules/Show', [
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

    /** Базовый запрос для списка публичных модулей. */
    private function indexQuery(string $locale): Builder
    {
        return SchoolModule::query()
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
            ])
            ->withCount([
                'lessons',
                'likes',
                'images',
            ]);
    }

    /** Получение списка публичных модулей по активному режиму обработки. */
    private function getIndexModules(
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
