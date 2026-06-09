<?php

namespace App\Http\Controllers\Admin\School\SchoolCourse;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolCourse\SchoolCourseRequest;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseResource;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileResource;
use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackSharedResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolCourse\SchoolCourseImage;
use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Курсами в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное)
 * - мультиязычность, изображения
 * - связи с треками, хештегами и связанными курсами.
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolCourse
 * @see SchoolCourseRequest
 *
 */
class SchoolCourseController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolCourse::class;

    /** Модель изображений */
    protected string $imageModelClass = SchoolCourseImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для уведомлений */
    protected string $entityLabel = 'курсов';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Список курсов. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $adminSchoolCoursesPerPage = (int) config('site_settings.adminSchoolCoursesPerPage', 6);
        $adminSchoolCoursesDefaultSort = (string) config('site_settings.adminSchoolCoursesDefaultSort', 'idDesc');
        $sort = (string) $request->query('sort', $adminSchoolCoursesDefaultSort);

        try {
            $courses = $this->baseQuery()
                ->with([
                    'translation',
                    'translations',
                    'images',
                    'instructorProfile.translation',
                    'tracks.translation',
                    'hashtags.translation',
                    'relatedCourses.translation',
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
                    'enrollments',
                    'schedules',
                    'quizzes',
                    'likes',
                ])
                ->sortByParam($sort, $currentLocale)
                ->get();

            return Inertia::render('Admin/School/SchoolCourses/Index', [
                'courses' => SchoolCourseResource::collection($courses),
                'coursesCount' => $this->baseQuery()->count(),

                'adminSchoolCoursesPerPage' => $adminSchoolCoursesPerPage,
                'adminSchoolCoursesDefaultSort' => $adminSchoolCoursesDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка school courses: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SchoolCourses/Index', [
                'courses' => [],
                'coursesCount' => 0,

                'adminSchoolCoursesPerPage' => $adminSchoolCoursesPerPage,
                'adminSchoolCoursesDefaultSort' => $adminSchoolCoursesDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'error' => 'Ошибка загрузки курсов.',
            ]);
        }
    }

    /** Страница создания курса. */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/School/SchoolCourses/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'instructorProfiles' => $this->instructorProfilesForSelect(),
            'tracks' => $this->tracksForSelect(),
            'hashtags' => $this->hashtagsForSelect(),
            'courses' => $this->coursesForSelect(),
        ]);
    }

    /** Сохранение нового курса. */
    public function store(SchoolCourseRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        $trackIds = $data['track_ids'] ?? [];
        $hashtagIds = $data['hashtag_ids'] ?? [];
        $relatedCourseIds = $data['related_course_ids'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['track_ids'],
            $data['hashtag_ids'],
            $data['related_course_ids']
        );

        try {
            DB::transaction(function () use (
                $request,
                $data,
                $translations,
                $imagesData,
                $trackIds,
                $hashtagIds,
                $relatedCourseIds
            ) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = SchoolCourse::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                $course = SchoolCourse::create($data);

                $this->syncTranslations($course, $translations);
                $this->syncImages($course, $request, $imagesData);

                $course->tracks()->sync($trackIds);
                $course->hashtags()->sync($hashtagIds);
                $course->relatedCourses()->sync(
                    array_filter($relatedCourseIds, fn ($id) => (int) $id !== (int) $course->id)
                );
            });

            return redirect()
                ->route('admin.schoolCourses.index')
                ->with('success', 'Курс успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school course: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании курса.');
        }
    }

    /** Редирект на страницу редактирования. */
    public function show(int $schoolCourse): RedirectResponse
    {
        return redirect()->route('admin.schoolCourses.edit', $schoolCourse);
    }

    /** Страница редактирования курса. */
    public function edit(int $schoolCourse, Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $course = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'images',
                'instructorProfile.translation',
                'tracks.translation',
                'hashtags.translation',
                'relatedCourses.translation',
                'relatedBy.translation',
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
                'enrollments',
                'schedules',
                'quizzes',
                'likes',
            ])
            ->findOrFail($schoolCourse);

        return Inertia::render('Admin/School/SchoolCourses/Edit', [
            'course' => new SchoolCourseResource($course),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'instructorProfiles' => $this->instructorProfilesForSelect(),
            'tracks' => $this->tracksForSelect(),
            'hashtags' => $this->hashtagsForSelect(),
            'courses' => $this->coursesForSelect($course->id),
        ]);
    }

    /** Обновление курса. */
    public function update(SchoolCourseRequest $request, int $schoolCourse): RedirectResponse
    {
        $course = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolCourse);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        $trackIds = $data['track_ids'] ?? [];
        $hashtagIds = $data['hashtag_ids'] ?? [];
        $relatedCourseIds = $data['related_course_ids'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['track_ids'],
            $data['hashtag_ids'],
            $data['related_course_ids'],
            $data['_method']
        );

        try {
            DB::transaction(function () use (
                $request,
                $course,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds,
                $trackIds,
                $hashtagIds,
                $relatedCourseIds
            ) {
                $course->update($data);

                $this->syncTranslations($course, $translations);
                $this->syncImages($course, $request, $imagesData, $deletedImageIds);

                $course->tracks()->sync($trackIds);
                $course->hashtags()->sync($hashtagIds);
                $course->relatedCourses()->sync(
                    array_filter($relatedCourseIds, fn ($id) => (int) $id !== (int) $course->id)
                );
            });

            return redirect()
                ->route('admin.schoolCourses.index')
                ->with('success', 'Курс успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school course ID ' . $course->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении курса.');
        }
    }

    /** Удаление курса. */
    public function destroy(int $schoolCourse): RedirectResponse
    {
        $course = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolCourse);

        try {
            DB::transaction(function () use ($course) {
                $imageIds = $course->images()
                    ->pluck('school_course_images.id')
                    ->toArray();

                if (!empty($imageIds)) {
                    $course->images()->detach();
                    $this->deleteImages($imageIds);
                }

                $course->tracks()->detach();
                $course->hashtags()->detach();
                $course->relatedCourses()->detach();
                $course->relatedBy()->detach();

                $course->translations()->delete();
                $course->likes()->delete();

                $course->delete();
            });

            return redirect()
                ->route('admin.schoolCourses.index')
                ->with('success', 'Курс успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school course ID ' . $course->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении курса.');
        }
    }

    /** Переключение флага "новый курс". */
    public function updateIsNew(Request $request, int $schoolCourse): RedirectResponse
    {
        return $this->updateBooleanFlag($request, $schoolCourse, 'is_new');
    }

    /** Переключение флага "популярный курс". */
    public function updateIsHit(Request $request, int $schoolCourse): RedirectResponse
    {
        return $this->updateBooleanFlag($request, $schoolCourse, 'is_hit');
    }

    /** Переключение флага "курс со скидкой". */
    public function updateIsSale(Request $request, int $schoolCourse): RedirectResponse
    {
        return $this->updateBooleanFlag($request, $schoolCourse, 'is_sale');
    }

    /** Массовое обновление флага "новый курс". */
    public function bulkUpdateIsNew(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'is_new');
    }

    /** Массовое обновление флага "популярный курс". */
    public function bulkUpdateIsHit(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'is_hit');
    }

    /** Массовое обновление флага "курс со скидкой". */
    public function bulkUpdateIsSale(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'is_sale');
    }

    /** Обновление boolean-флага курса. */
    private function updateBooleanFlag(Request $request, int $schoolCourse, string $field): RedirectResponse
    {
        $validated = $request->validate([
            $field => ['required', 'boolean'],
        ]);

        $course = $this->baseQuery()->findOrFail($schoolCourse);

        $course->update([
            $field => $validated[$field],
        ]);

        return back()->with('success', "Поле {$field} обновлено.");
    }

    /** Массовое обновление boolean-флага курсов. */
    private function bulkUpdateBooleanFlag(Request $request, string $field): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_courses,id'],
            $field => ['required', 'boolean'],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            $message = 'Часть курсов недоступна для обновления.';

            return $request->expectsJson()
                ? response()->json([
                    'success' => false,
                    'message' => $message,
                ], 403)
                : back()->with('error', $message);
        }

        SchoolCourse::whereIn('id', $allowedIds)->update([
            $field => $validated[$field],
        ]);

        $message = "Поле {$field} обновлено.";

        return $request->expectsJson()
            ? response()->json([
                'success' => true,
                'message' => $message,
            ])
            : back()->with('success', $message);
    }

    /** Список инструкторов для select. */
    private function instructorProfilesForSelect(): AnonymousResourceCollection
    {
        $instructors = SchoolInstructorProfile::query()
            ->with([
                'translation',
                'translations',
                'images',
                'user:id,name,email',
            ])
            ->get();

        return SchoolInstructorProfileResource::collection($instructors);
    }

    /** Список треков для select. */
    private function tracksForSelect(): AnonymousResourceCollection
    {
        $tracks = SchoolTrack::query()
            ->with(['translation', 'translations'])
            ->withCount(['children', 'courses'])
            ->get();

        return SchoolTrackSharedResource::collection($tracks);
    }

    /** Список хештегов для select. */
    private function hashtagsForSelect(): AnonymousResourceCollection
    {
        $hashtags = SchoolHashtag::query()
            ->with(['translation', 'translations'])
            ->get();

        return SchoolHashtagSharedResource::collection($hashtags);
    }

    /** Список курсов для select связанных курсов. */
    private function coursesForSelect(?int $excludeId = null): AnonymousResourceCollection
    {
        $courses = SchoolCourse::query()
            ->when($excludeId, fn ($query) => $query->whereKeyNot($excludeId))
            ->with([
                'translation',
                'translations',
                'images',
            ])
            ->get();

        return SchoolCourseSharedResource::collection($courses);
    }
}
