<?php

namespace App\Http\Controllers\Public\Default\School\SchoolLesson;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonResource;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Traits\Public\BuildsTrackTreeTrait;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\HasSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
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

        $perPage = $this->resolvePerPage(
            $request,
            (int) config('site_settings.publicSchoolLessonsPerPage', 20)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            (string) config('site_settings.publicSchoolLessonsDefaultSort', 'id_desc')
        );

        $view = $this->resolveView(
            $request,
            (string) config('site_settings.publicSchoolLessonsDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            (string) config('site_settings.publicSchoolLessonsProcessingMode', 'server')
        );

        $lessons = SchoolLesson::query()
            ->forPublic($locale)
            ->search($search, $locale)
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
            ])
            ->sortByParam($sort)
            ->paginate($perPage)
            ->withQueryString();

        $lessons = $this->appendUserLikes(
            $lessons,
            SchoolLessonResource::class
        );

        $trackTree = $this->buildTrackTree($locale);
        $sidebarData = $this->getSidebarData($locale);

        return Inertia::render('Public/Default/School/Lessons/Index', [
            'lessons' => $lessons,

            'lessonsCount' => SchoolLesson::query()
                ->forPublic($locale)
                ->count(),

            'lessonsFound' => $lessons->total(),

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

        return Inertia::render('Public/Default/School/Lessons/Show', [
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
}
