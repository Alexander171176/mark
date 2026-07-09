<?php

namespace App\Http\Controllers\Public\Default\School\SchoolLesson;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonResource;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
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

class SchoolLessonController extends Controller
{
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsTrackTreeTrait;
    use HasSidebarDataTrait;

    /** Страница списка уроков. */
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
                'title' => __('Уроки'),
                'keywords' => '',
                'description' => '',
            ];

        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicSchoolLessonsPerPage', 6)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicSchoolLessonsDefaultSort', 'idDesc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicSchoolLessonsDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicSchoolLessonsProcessingMode', 'server')
        );

        $lessonsCount = SchoolLesson::query()
            ->forPublic($locale)
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $lessonsCount,
                300
            );

        $lessons = $this->getIndexLessons(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        $lessonsFound = $useServerProcessing
            ? $lessons->total()
            : $lessons->count();

        $lessons = $useServerProcessing
            ? $this->appendUserLikes($lessons, SchoolLessonResource::class)
            : SchoolLessonResource::collection($lessons);

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolLessons/Index', [

            'seo' => $seo,

            'publicSchoolLessonsProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'lessons' => $lessons,

            'lessonsCount' => $lessonsCount,
            'lessonsFound' => $lessonsFound,

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

    /** Страница конкретного урока. */
    public function show(string $slug): Response
    {
        $locale = app()->getLocale();

        $lesson = SchoolLesson::query()
            ->forPublic($locale)
            ->where('slug', $slug)
            ->with([
                'translation',
                'translations',
                'images',
                'content',

                'module.translation',
                'module.translations',
                'module.images',

                'module.course.translation',
                'module.course.translations',
                'module.course.images',
                'module.course.instructorProfile.translation',
                'module.course.instructorProfile.translations',
                'module.course.instructorProfile.images',
                'module.course.tracks.translation',
                'module.course.tracks.translations',
                'module.course.hashtags.translation',
                'module.course.hashtags.translations',

                'hashtags.translation',
                'hashtags.translations',
            ])
            ->withCount([
                'likes',
                'hashtags',
                'images',
            ])
            ->firstOrFail();

        $lesson->increment('views');

        $lessonData = (new SchoolLessonResource($lesson))->resolve();

        $lessonData['already_liked'] = auth()->check()
            ? $lesson->likes()->where('user_id', auth()->id())->exists()
            : false;

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/SchoolLessons/Show', [
            'lesson' => $lessonData,

            'trackTree' => $trackTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /** Лайк урока. */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $lesson = SchoolLesson::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = auth()->id();

        if ($lesson->likes()->where('user_id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $lesson->likes()->count(),
            ]);
        }

        $lesson->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $lesson->likes()->count(),
        ]);
    }

    /** Базовый запрос для списка публичных уроков. */
    private function indexQuery(string $locale): Builder
    {
        return SchoolLesson::query()
            ->forPublic($locale)
            ->with([
                'translation',
                'translations',
                'images',
                'content',

                'module.translation',
                'module.translations',
                'module.images',

                'module.course.translation',
                'module.course.translations',
                'module.course.images',
                'module.course.instructorProfile.translation',
                'module.course.instructorProfile.translations',
                'module.course.instructorProfile.images',

                'hashtags.translation',
                'hashtags.translations',
            ])
            ->withCount([
                'likes',
                'hashtags',
                'images',
            ]);
    }

    /** Получение списка публичных уроков по активному режиму обработки. */
    private function getIndexLessons(
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
