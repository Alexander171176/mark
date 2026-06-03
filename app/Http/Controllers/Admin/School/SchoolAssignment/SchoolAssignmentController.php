<?php

namespace App\Http\Controllers\Admin\School\SchoolAssignment;

use App\Http\Controllers\Admin\School\Base\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolAssignment\SchoolAssignmentRequest;
use App\Http\Resources\Admin\School\SchoolAssignment\SchoolAssignmentResource;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use App\Models\Admin\School\SchoolAssignment\SchoolAssignment;
use App\Models\Admin\School\SchoolAssignment\SchoolAssignmentImage;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Заданиями (SchoolAssignments) в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 * - клонирование заданий
 * - мультиязычность, изображения
 * - связи с курсами, модулями, уроками и преподователями.
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolAssignment
 * @see SchoolAssignmentRequest
 */
class SchoolAssignmentController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolAssignment::class;

    /** Модель изображений */
    protected string $imageModelClass = SchoolAssignmentImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности */
    protected string $entityLabel = 'заданий';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
        'description',
        'instructions',
    ];

    /** Расширение сортировки для заданий. */
    protected function extendedSortMap(): array
    {
        return [
            'published_at' => 'published_at_desc',
            'due_at' => 'due_at_desc',

            'status' => 'status_asc',
            'visibility' => 'visibility_asc',

            'attempts_limit' => 'attempts_limit_desc',
            'max_score' => 'max_score_desc',
            'images_count' => 'images_count_desc',
            'submissions_count' => 'submissions_count_desc',

            'activity' => 'activity',
            'inactive' => 'inactive',

            'left' => 'left',
            'noLeft' => 'no_left',

            'main' => 'main',
            'noMain' => 'no_main',

            'right' => 'right',
            'noRight' => 'no_right',
        ];
    }

    /** Список заданий */
    public function index(Request $request): Response
    {
        // Текущая локаль
        $currentLocale = $this->resolveLocale($request);

        $adminSchoolAssignmentsPerPage = (int) config('site_settings.adminSchoolAssignmentsPerPage', 10);
        $adminSchoolAssignmentsDefaultSort = (string) config('site_settings.adminSchoolAssignmentsDefaultSort', 'idDesc');
        $sort = $this->normalizeSortParam($adminSchoolAssignmentsDefaultSort);

        try {
            $assignments = $this->baseQuery()
                ->with([
                    'translation',
                    'translations',
                    'images',
                    'course.translation',
                    'module.translation',
                    'lesson.translation',
                    'instructor.translation',
                ])
                ->withCount([
                    'images',
                    'submissions',
                ])
                ->when($sort === 'activity', fn ($query) => $query->where('activity', true))
                ->when($sort === 'inactive', fn ($query) => $query->where('activity', false))

                ->when($sort === 'left', fn ($query) => $query->where('left', true))
                ->when($sort === 'no_left', fn ($query) => $query->where('left', false))
                ->when($sort === 'main', fn ($query) => $query->where('main', true))
                ->when($sort === 'no_main', fn ($query) => $query->where('main', false))
                ->when($sort === 'right', fn ($query) => $query->where('right', true))
                ->when($sort === 'no_right', fn ($query) => $query->where('right', false))

                ->when($sort === 'published_at_desc', fn ($query) => $query->orderByDesc('published_at')->orderByDesc('id'))
                ->when($sort === 'due_at_desc', fn ($query) => $query->orderByDesc('due_at')->orderByDesc('id'))

                ->when($sort === 'status_asc', fn ($query) => $query->orderBy('status')->orderByDesc('id'))
                ->when($sort === 'visibility_asc', fn ($query) => $query->orderBy('visibility')->orderByDesc('id'))

                ->when($sort === 'attempts_limit_desc', fn ($query) => $query->orderByDesc('attempts_limit')->orderByDesc('id'))
                ->when($sort === 'max_score_desc', fn ($query) => $query->orderByDesc('max_score')->orderByDesc('id'))
                ->when($sort === 'images_count_desc', fn ($query) => $query->orderByDesc('images_count')->orderByDesc('id'))
                ->when($sort === 'submissions_count_desc', fn ($query) => $query->orderByDesc('submissions_count')->orderByDesc('id'))

                ->when($sort === 'sort_asc', fn ($query) => $query->orderBy('sort')->orderByDesc('id'))
                ->when($sort === 'sort_desc', fn ($query) => $query->orderByDesc('sort')->orderByDesc('id'))
                ->when($sort === 'date_asc', fn ($query) => $query->orderBy('id')->orderByDesc('id'))
                ->when($sort === 'date_desc', fn ($query) => $query->orderByDesc('id'))
                ->get();

            return Inertia::render('Admin/School/Assignments/Index', [
                'assignments' => SchoolAssignmentResource::collection($assignments),
                'assignmentsCount' => $this->baseQuery()->count(),

                'adminSchoolAssignmentsPerPage' => $adminSchoolAssignmentsPerPage,
                'adminSchoolAssignmentsDefaultSort' => $adminSchoolAssignmentsDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка school assignments: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/Assignments/Index', [
                'assignments' => [],
                'assignmentsCount' => 0,

                'adminSchoolAssignmentsPerPage' => $adminSchoolAssignmentsPerPage,
                'adminSchoolAssignmentsDefaultSort' => $adminSchoolAssignmentsDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'error' => 'Ошибка загрузки заданий.',
            ]);
        }
    }

    /** Страница создания задания */
    public function create(Request $request): Response
    {
        // Текущая локаль
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/School/Assignments/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'courses' => $this->coursesForSelect(),
            'modules' => $this->modulesForSelect(),
            'lessons' => $this->lessonsForSelect(),
            'instructors' => $this->instructorsForSelect(),
        ]);
    }

    /** Создание задания */
    public function store(SchoolAssignmentRequest $request): RedirectResponse
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
                    $maxSort = SchoolAssignment::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                $assignment = SchoolAssignment::create($data);

                $this->syncTranslations($assignment, $translations);
                $this->syncImages($assignment, $request, $imagesData);
            });

            return redirect()
                ->route('admin.schoolAssignments.index')
                ->with('success', 'Задание успешно создано.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school assignment: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании задания.');
        }
    }

    /** Переход на редактирование */
    public function show(int $schoolAssignment): RedirectResponse
    {
        return redirect()->route('admin.schoolAssignments.edit', $schoolAssignment);
    }

    /** Страница редактирования */
    public function edit(int $schoolAssignment, Request $request): Response
    {
        // Текущая локаль
        $currentLocale = $this->resolveLocale($request);

        $assignment = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'images',
                'course.translation',
                'module.translation',
                'lesson.translation',
                'instructor.translation',
            ])
            ->withCount([
                'images',
                'submissions',
            ])
            ->findOrFail($schoolAssignment);

        return Inertia::render('Admin/School/Assignments/Edit', [
            'assignment' => new SchoolAssignmentResource($assignment),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'courses' => $this->coursesForSelect(),
            'modules' => $this->modulesForSelect(),
            'lessons' => $this->lessonsForSelect(),
            'instructors' => $this->instructorsForSelect(),
        ]);
    }

    /** Обновление задания */
    public function update(SchoolAssignmentRequest $request, int $schoolAssignment): RedirectResponse
    {
        $assignment = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolAssignment);

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
                $assignment,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds
            ) {
                $assignment->update($data);

                $this->syncTranslations($assignment, $translations);
                $this->syncImages($assignment, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.schoolAssignments.index')
                ->with('success', 'Задание успешно обновлено.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school assignment ID ' . $assignment->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении задания.');
        }
    }

    /** Удаление задания */
    public function destroy(int $schoolAssignment): RedirectResponse
    {
        $assignment = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolAssignment);

        try {
            DB::transaction(function () use ($assignment) {
                $imageIds = $assignment->images()
                    ->pluck('school_assignment_images.id')
                    ->toArray();

                if (!empty($imageIds)) {
                    $assignment->images()->detach();
                    $this->deleteImages($imageIds);
                }

                $assignment->translations()->delete();
                $assignment->submissions()->delete();

                $assignment->delete();
            });

            return redirect()
                ->route('admin.schoolAssignments.index')
                ->with('success', 'Задание успешно удалено.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school assignment ID ' . $assignment->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении задания.');
        }
    }

    /** Массовое удаление заданий */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => [
                'required',
                'integer',
                'exists:school_assignments,id',
            ],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with(
                'error',
                'Часть заданий недоступна для удаления.'
            );
        }

        try {
            DB::transaction(function () use ($allowedIds) {

                $assignments = SchoolAssignment::query()
                    ->whereIn('id', $allowedIds)
                    ->with('images')
                    ->get();

                foreach ($assignments as $assignment) {

                    $imageIds = $assignment->images()
                        ->pluck('school_assignment_images.id')
                        ->toArray();

                    // Удаление изображений
                    if (!empty($imageIds)) {
                        $assignment->images()->detach();

                        $this->deleteImages($imageIds);
                    }

                    // Удаление переводов
                    $assignment->translations()->delete();

                    // Удаление ответов пользователей
                    $assignment->submissions()->delete();

                    // Удаление задания
                    $assignment->delete();
                }
            });

            return back()->with(
                'success',
                'Выбранные задания успешно удалены.'
            );

        } catch (Throwable $e) {

            Log::error(
                'Ошибка bulkDestroy school assignments: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении заданий.'
            );
        }
    }

    /** Клонирование задания */
    public function clone(int $schoolAssignment): RedirectResponse
    {
        $assignment = $this->baseQuery()
            ->with([
                'translations',
                'images',
            ])
            ->findOrFail($schoolAssignment);

        try {
            DB::transaction(function () use ($assignment) {
                // Создание копии
                $clone = $assignment->replicate([
                    'created_at',
                    'updated_at',
                ]);

                $clone->slug = $assignment->slug . '-copy-' . time();
                $clone->sort = ((int) SchoolAssignment::max('sort')) + 1;
                // Сброс служебных полей
                $clone->activity = false;
                $clone->left = false;
                $clone->main = false;
                $clone->right = false;
                $clone->published_at = null;
                $clone->due_at = null;
                $clone->status = 'draft';

                $clone->save();

                // Копирование переводов
                foreach ($assignment->translations as $translation) {
                    $newTranslation = $translation->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                    $newTranslation->school_assignment_id = $clone->id;
                    $newTranslation->title = $translation->title . ' copy';

                    $newTranslation->save();
                }

                // Копирование изображений
                foreach ($assignment->images as $image) {
                    $clone->images()->attach($image->id, [
                        'order' => $image->pivot->order ?? 0,
                    ]);
                }
            });

            return redirect()
                ->route('admin.schoolAssignments.index')
                ->with('success', 'Задание успешно клонировано.');
        } catch (Throwable $e) {
            Log::error('Ошибка клонирования school assignment ID ' . $assignment->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при клонировании задания.');
        }
    }

    /** Курсы для селекта */
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

    /** Модули для селекта */
    private function modulesForSelect(): AnonymousResourceCollection
    {
        $modules = SchoolModule::query()
            ->with([
                'translation',
                'translations',
                'images',
                'course.translation',
            ])
            ->get();

        return SchoolModuleSharedResource::collection($modules);
    }

    /** Уроки для селекта */
    private function lessonsForSelect(): AnonymousResourceCollection
    {
        $lessons = SchoolLesson::query()
            ->with([
                'translation',
                'translations',
                'images',
                'module.translation',
                'module.course.translation',
            ])
            ->get();

        return SchoolLessonSharedResource::collection($lessons);
    }

    /** Преподаватели для селекта */
    private function instructorsForSelect(): AnonymousResourceCollection
    {
        $instructors = SchoolInstructorProfile::query()
            ->with([
                'translation',
                'translations',
                'images',
                'user',
            ])
            ->get();

        return SchoolInstructorProfileResource::collection($instructors);
    }
}
