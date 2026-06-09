<?php

namespace App\Http\Controllers\Admin\School\SchoolCourseSchedule;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleRequest;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleImage;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления потоками / расписаниями курсов в админке.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - мультиязычность, изображения
 * - клонирование потока
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolCourseSchedule
 * @see SchoolCourseScheduleRequest
 */
class SchoolCourseScheduleController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolCourseSchedule::class;

    /** Модель изображений */
    protected string $imageModelClass = SchoolCourseScheduleImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для уведомлений */
    protected string $entityLabel = 'расписаний';

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

    /** список расписаний */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $adminSchoolCourseSchedulesPerPage = (int) config('site_settings.adminSchoolCourseSchedulesPerPage', 6);
        $adminSchoolCourseSchedulesDefaultSort = (string) config('site_settings.adminSchoolCourseSchedulesDefaultSort', 'idDesc');
        $sort = (string) $request->query('sort', $adminSchoolCourseSchedulesDefaultSort);

        try {
            $schedules = $this->baseQuery()
                ->with([
                    'translation',
                    'translations',
                    'images',
                    'course.translation',
                    'course.translations',
                    'instructor.translation',
                    'instructor.translations',
                    'instructor.user',
                ])
                ->withCount([
                    'images',
                    'cohortEnrollments',
                ])
                ->sortByParam($sort, $currentLocale)
                ->get();

            return Inertia::render('Admin/School/CourseSchedules/Index', [
                'schedules' => SchoolCourseScheduleResource::collection($schedules),
                'schedulesCount' => $this->baseQuery()->count(),

                'adminSchoolCourseSchedulesPerPage' => $adminSchoolCourseSchedulesPerPage,
                'adminSchoolCourseSchedulesDefaultSort' => $adminSchoolCourseSchedulesDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка school course schedules: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/CourseSchedules/Index', [
                'schedules' => [],
                'schedulesCount' => 0,

                'adminSchoolCourseSchedulesPerPage' => $adminSchoolCourseSchedulesPerPage,
                'adminSchoolCourseSchedulesDefaultSort' => $adminSchoolCourseSchedulesDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'error' => 'Ошибка загрузки расписаний.',
            ]);
        }
    }

    /** Страница создания расписания */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/School/CourseSchedules/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'courses' => $this->coursesForSelect(),
            'instructors' => $this->instructorsForSelect(),
        ]);
    }

    /** Создание расписания */
    public function store(SchoolCourseScheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages']
        );

        try {
            DB::transaction(function () use (
                $request,
                $data,
                $translations,
                $imagesData
            ) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = SchoolCourseSchedule::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                $schedule = SchoolCourseSchedule::create($data);

                $this->syncTranslations($schedule, $translations);
                $this->syncImages($schedule, $request, $imagesData);
            });

            return redirect()
                ->route('admin.schoolCourseSchedules.index')
                ->with('success', 'Расписание успешно создано.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school course schedule: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании расписания.');
        }
    }

    /** Редирект на страницу редактирования */
    public function show(int $schoolCourseSchedule): RedirectResponse
    {
        return redirect()->route('admin.schoolCourseSchedules.edit', $schoolCourseSchedule);
    }

    /** Страница редактирования расписания */
    public function edit(int $schoolCourseSchedule, Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $schedule = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'images',
                'course.translation',
                'course.translations',
                'instructor.translation',
                'instructor.translations',
                'instructor.user',
            ])
            ->withCount([
                'images',
                'cohortEnrollments',
            ])
            ->findOrFail($schoolCourseSchedule);

        return Inertia::render('Admin/School/CourseSchedules/Edit', [
            'schedule' => new SchoolCourseScheduleResource($schedule),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'courses' => $this->coursesForSelect(),
            'instructors' => $this->instructorsForSelect(),
        ]);
    }

    /** Обновление расписания */
    public function update(
        SchoolCourseScheduleRequest $request,
        int $schoolCourseSchedule
    ): RedirectResponse {
        $schedule = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolCourseSchedule);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['_method']
        );

        try {
            DB::transaction(function () use (
                $request,
                $schedule,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds
            ) {
                $schedule->update($data);

                $this->syncTranslations($schedule, $translations);
                $this->syncImages($schedule, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.schoolCourseSchedules.index')
                ->with('success', 'Расписание успешно обновлено.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school course schedule ID ' . $schedule->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении расписания.');
        }
    }

    /** Удаление расписания */
    public function destroy(int $schoolCourseSchedule): RedirectResponse
    {
        $schedule = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolCourseSchedule);

        try {
            DB::transaction(function () use ($schedule) {
                $imageIds = $schedule->images()
                    ->pluck('school_course_schedule_images.id')
                    ->toArray();

                if (!empty($imageIds)) {
                    $schedule->images()->detach();
                    $this->deleteImages($imageIds);
                }

                $schedule->delete();
            });

            return redirect()
                ->route('admin.schoolCourseSchedules.index')
                ->with('success', 'Расписание успешно удалено.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school course schedule ID ' . $schedule->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении расписания.');
        }
    }

    /** Клонирование расписания */
    public function clone(int $schoolCourseSchedule): RedirectResponse
    {
        $schedule = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolCourseSchedule);

        try {
            DB::transaction(function () use ($schedule) {
                $clone = $schedule->replicate([
                    'created_at',
                    'updated_at',
                ]);

                $clone->slug = $schedule->slug . '-copy-' . time();
                $clone->sort = ((int) SchoolCourseSchedule::max('sort')) + 1;
                $clone->activity = false;
                $clone->status = 'draft';

                $clone->save();

                foreach ($schedule->images as $image) {
                    $clone->images()->attach($image->id, [
                        'order' => $image->pivot->order ?? 0,
                    ]);
                }
            });

            return redirect()
                ->route('admin.schoolCourseSchedules.index')
                ->with('success', 'Расписание успешно клонировано.');
        } catch (Throwable $e) {
            Log::error('Ошибка клонирования school course schedule ID ' . $schedule->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при клонировании расписания.');
        }
    }

    /** Список курсов для select */
    private function coursesForSelect(): AnonymousResourceCollection
    {
        $courses = SchoolCourse::query()
            ->with([
                'translation',
                'translations',
                'images',
            ])
            ->get();

        return SchoolCourseSharedResource::collection($courses);
    }

    /** Список инструкторов для select */
    private function instructorsForSelect(): AnonymousResourceCollection
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
}
