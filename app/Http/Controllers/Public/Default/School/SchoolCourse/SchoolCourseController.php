<?php

namespace App\Http\Controllers\Public\Default\School\SchoolCourse;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
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

class SchoolCourseController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /** Страница списка курсов. */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $cmsSeoPage = app(CmsPageResolverService::class)
            ->resolveSeo($request->path());

        $cmsSeoTranslation = $cmsSeoPage?->translationOrFallback();

        $seo = $cmsSeoTranslation
            ? [
                'title' => $cmsSeoTranslation->meta_title ?: $cmsSeoTranslation->title,
                'keywords' => $cmsSeoTranslation->meta_keywords,
                'description' => $cmsSeoTranslation->meta_desc ?: $cmsSeoTranslation->short,
            ]
            : [
                'title' => __('Курсы'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicSchoolCoursesPerPage', 6)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicSchoolCoursesDefaultSort', 'idDesc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicSchoolCoursesDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicSchoolCoursesProcessingMode', 'server')
        );

        $coursesCount = SchoolCourse::query()
            ->forPublic($locale)
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $coursesCount,
                300
            );

        $courses = $this->getIndexCourses(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        $coursesFound = $useServerProcessing
            ? $courses->total()
            : $courses->count();

        $courses = $useServerProcessing
            ? $this->appendUserLikes($courses, SchoolCourseResource::class)
            : SchoolCourseResource::collection($courses);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolCourses/Index', [

            'seo' => $seo,

            'publicSchoolCoursesProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'courses' => $courses,

            'coursesCount' => $coursesCount,
            'coursesFound' => $coursesFound,

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

    /** Страница конкретного курса. */
    public function show(string $slug): Response
    {
        $locale = app()->getLocale();

        $course = SchoolCourse::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->with([
                'translation',
                'translations',
                'images',

                'instructorProfile',
                'instructorProfile.translation',
                'instructorProfile.translations',
                'instructorProfile.images',

                'tracks.translation',
                'tracks.translations',
                'tracks.images',

                'hashtags.translation',
                'hashtags.translations',

                'prices',

                'reviews' => fn ($query) => $query
                    ->with('user:id,name')
                    ->latest(),

                'relatedCourses' => fn ($query) => $query
                    ->forPublic($locale)
                    ->with([
                        'translation',
                        'translations',
                        'images',
                        'instructorProfile.translation',
                        'instructorProfile.translations',
                        'instructorProfile.images',
                    ])
                    ->withCount('likes')
                    ->ordered(),

                'modules' => fn ($query) => $query
                    ->forPublic($locale)
                    ->with([
                        'translation',
                        'translations',
                        'images',

                        'lessons' => fn ($lessonQuery) => $lessonQuery
                            ->forPublic($locale)
                            ->with([
                                'translation',
                                'translations',
                                'images',
                            ])
                            ->ordered(),
                    ])
                    ->withCount([
                        'lessons',
                        'likes',
                    ])
                    ->ordered(),
            ])
            ->withCount([
                'modules',
                'lessons',
                'tracks',
                'hashtags',
                'images',
                'prices',
                'reviews',
                'likes',
            ])
            ->firstOrFail();

        $course->increment('views');

        $courseData = (new SchoolCourseResource($course))->resolve();

        $courseData['already_liked'] = auth()->check()
            ? $course->likes()->where('user_id', auth()->id())->exists()
            : false;

        $courseData['related_courses'] = $course->relatedCourses
            ->map(function ($relatedCourse) {
                $resolved = (new SchoolCourseResource($relatedCourse))->resolve();

                $resolved['already_liked'] = auth()->check()
                    ? $relatedCourse->likes()->where('user_id', auth()->id())->exists()
                    : false;

                return $resolved;
            })
            ->values()
            ->all();

        $modules = $course->modules
            ->map(function ($module) {
                $resolved = (new SchoolModuleResource($module))->resolve();

                $resolved['already_liked'] = auth()->check()
                    ? $module->likes()->where('user_id', auth()->id())->exists()
                    : false;

                return $resolved;
            })
            ->values()
            ->all();

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolCourses/Show', [
            'course' => $courseData,
            'modules' => $modules,

            'trackTree' => $trackTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /** Лайк курса. */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $course = SchoolCourse::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = auth()->id();

        if ($course->likes()->where('user_id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $course->likes()->count(),
            ]);
        }

        $course->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $course->likes()->count(),
        ]);
    }

    /** Базовый запрос для списка публичных курсов. */
    private function indexQuery(string $locale): Builder
    {
        return SchoolCourse::query()
            ->forPublic($locale)
            ->with([
                'translation',
                'translations',
                'images',

                'instructorProfile',
                'instructorProfile.translation',
                'instructorProfile.translations',
                'instructorProfile.images',

                'tracks.translation',
                'tracks.translations',
                'tracks.images',

                'hashtags.translation',
                'hashtags.translations',

                'prices',
            ])
            ->withCount([
                'modules',
                'lessons',
                'tracks',
                'hashtags',
                'images',
                'prices',
                'reviews',
                'likes',
            ]);
    }

    /** Получение списка публичных курсов по активному режиму обработки. */
    private function getIndexCourses(
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
