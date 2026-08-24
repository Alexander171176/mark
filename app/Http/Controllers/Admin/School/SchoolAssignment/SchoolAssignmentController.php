<?php

namespace App\Http\Controllers\Admin\School\SchoolAssignment;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolAssignment\SchoolAssignmentRequest;
use App\Http\Resources\Admin\School\SchoolAssignment\SchoolAssignmentResource;
use App\Http\Resources\Admin\School\SchoolAssignment\SchoolAssignmentSharedResource;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use App\Models\Admin\School\SchoolAssignment\SchoolAssignment;
use App\Models\Admin\School\SchoolAssignment\SchoolAssignmentImage;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
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

    /** Список заданий */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminSchoolAssignmentsPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolAssignmentsDefaultSort',
            'idDesc'
        );

        $sortParam = (string) $request->query(
            'sort',
            $defaultSort
        );

        $search = trim(
            (string) $request->query(
                'search',
                ''
            )
        );

        $processingMode = $settings->string(
            'adminSchoolAssignmentsProcessingMode',
            'frontend'
        );

        /**
         * Decision COUNT нужен
         * только режиму auto.
         */
        $assignmentsCount = null;

        if ($processingMode === 'auto') {
            $assignmentsCount = $this->baseQuery()->count();

            $useServerProcessing = app(
                ProcessingModeService::class
            )->shouldUseServer(
                $processingMode,
                $assignmentsCount,
                300
            );
        } else {
            $useServerProcessing =
                $processingMode === 'server';
        }

        try {
            $assignments = $this->getIndexAssignments(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            /**
             * Получаем общий total
             * без лишнего COUNT.
             */
            if ($assignmentsCount === null) {
                if (!$useServerProcessing) {
                    /**
                     * Frontend получил
                     * всю коллекцию.
                     */
                    $assignmentsCount =
                        $assignments->count();
                } elseif ($search === '') {
                    /**
                     * paginate() уже знает
                     * общий total.
                     */
                    $assignmentsCount =
                        $assignments->total();
                } else {
                    /**
                     * paginator total при поиске
                     * означает найденное количество,
                     * поэтому нужен отдельный общий total.
                     */
                    $assignmentsCount =
                        $this->baseQuery()->count();
                }
            }

            return Inertia::render(
                'Admin/School/SchoolAssignments/Index',
                [
                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'useServerProcessing' => $useServerProcessing,

                    'adminSchoolAssignmentsPerPage' => $perPage,
                    'adminSchoolAssignmentsDefaultSort' => $defaultSort,
                    'adminSchoolAssignmentsProcessingMode' => $processingMode,

                    /**
                     * Admin Index использует
                     * только Shared Resource.
                     */
                    'assignments' =>
                        SchoolAssignmentSharedResource::collection(
                            $assignments
                        ),

                    'assignmentsCount' => $assignmentsCount,

                    'sortParam' => $sortParam,
                    'search' => $search,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка school assignments: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolAssignments/Index',
                [
                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'useServerProcessing' => $useServerProcessing,

                    'adminSchoolAssignmentsPerPage' => $perPage,
                    'adminSchoolAssignmentsDefaultSort' => $defaultSort,
                    'adminSchoolAssignmentsProcessingMode' => $processingMode,

                    'assignments' => [],
                    'assignmentsCount' => 0,

                    'sortParam' => $sortParam,
                    'search' => $search,

                    'error' => 'Ошибка загрузки заданий.',
                ]
            );
        }
    }

    /** Страница создания задания */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/School/SchoolAssignments/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),

                'modules' =>
                    $this->modulesForSelect(
                        $currentLocale
                    ),

                'lessons' =>
                    $this->lessonsForSelect(
                        $currentLocale
                    ),

                'instructors' =>
                    $this->instructorsForSelect(
                        $currentLocale
                    ),
            ]
        );
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

    /** Страница редактирования задания */
    public function edit(
        int $schoolAssignment,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $assignment = $this->baseQuery()
            ->with([
                /**
                 * Все переводы самого задания
                 * нужны TranslationTabs формы.
                 */
                'translations',

                /**
                 * Изображения + Spatie Media.
                 */
                'images.media',

                /**
                 * Связанный курс.
                 */
                'course' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),
                ]),

                /**
                 * Связанный модуль.
                 */
                'module' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),

                    'course' => fn ($courseQuery) =>
                    $courseQuery->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->where(
                            'locale',
                            $currentLocale
                        ),
                    ]),
                ]),

                /**
                 * Связанный урок.
                 */
                'lesson' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),

                    'module' => fn ($moduleQuery) =>
                    $moduleQuery->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->where(
                            'locale',
                            $currentLocale
                        ),

                        'course' => fn ($courseQuery) =>
                        $courseQuery->with([
                            'translations' => fn ($translationQuery) =>
                            $translationQuery->where(
                                'locale',
                                $currentLocale
                            ),
                        ]),
                    ]),
                ]),

                /**
                 * Преподаватель.
                 */
                'instructor' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),

                    'user',
                ]),
            ])
            ->withCount([
                'images',
                'submissions',
            ])
            ->findOrFail(
                $schoolAssignment
            );

        return Inertia::render(
            'Admin/School/SchoolAssignments/Edit',
            [
                'assignment' =>
                    new SchoolAssignmentResource(
                        $assignment
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),

                'modules' =>
                    $this->modulesForSelect(
                        $currentLocale
                    ),

                'lessons' =>
                    $this->lessonsForSelect(
                        $currentLocale
                    ),

                'instructors' =>
                    $this->instructorsForSelect(
                        $currentLocale
                    ),
            ]
        );
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

    /** Курсы для select */
    private function coursesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $courses = SchoolCourse::query()
            ->with([
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),
            ])
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            )
            ->get();

        return SchoolCourseSharedResource::collection(
            $courses
        );
    }

    /** Модули для select */
    private function modulesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $modules = SchoolModule::query()
            ->with([
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                'course' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $locale
                    ),
                ]),
            ])
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            )
            ->get();

        return SchoolModuleSharedResource::collection(
            $modules
        );
    }

    /** Уроки для select */
    private function lessonsForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $lessons = SchoolLesson::query()
            ->with([
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                'module' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $locale
                    ),

                    'course' => fn ($courseQuery) =>
                    $courseQuery->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->where(
                            'locale',
                            $locale
                        ),
                    ]),
                ]),
            ])
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            )
            ->get();

        return SchoolLessonSharedResource::collection(
            $lessons
        );
    }

    /** Преподаватели для select */
    private function instructorsForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $instructors = SchoolInstructorProfile::query()
            ->with([
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                'user',
            ])
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            )
            ->get();

        return SchoolInstructorProfileSharedResource::collection(
            $instructors
        );
    }

    /** Базовый запрос для Admin Index заданий. */
    private function indexQuery(string $locale): Builder
    {
        return $this->baseQuery()
            ->with([
                /**
                 * Только выбранная локаль задания.
                 */
                'translations' => fn ($query) =>
                $query->where('locale', $locale),

                /**
                 * Изображения + Spatie Media.
                 */
                'images.media',

                /**
                 * Курс.
                 */
                'course' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where('locale', $locale),
                ]),

                /**
                 * Модуль.
                 */
                'module' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where('locale', $locale),
                ]),

                /**
                 * Урок.
                 */
                'lesson' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where('locale', $locale),
                ]),

                /**
                 * Инструктор.
                 */
                'instructor' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where('locale', $locale),

                    /**
                     * Нужен для имени
                     * в таблице/карточках.
                     */
                    'user',
                ]),
            ])
            ->withCount([
                'images',
                'submissions',
            ]);
    }

    /** Получение списка заданий по активному режиму обработки. */
    private function getIndexAssignments(
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
