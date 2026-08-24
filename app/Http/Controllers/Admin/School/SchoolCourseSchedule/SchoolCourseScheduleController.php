<?php

namespace App\Http\Controllers\Admin\School\SchoolCourseSchedule;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleRequest;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleImage;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
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

    /** Список расписаний */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);
        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminSchoolCourseSchedulesPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolCourseSchedulesDefaultSort',
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
            'adminSchoolCourseSchedulesProcessingMode',
            'frontend'
        );

        /**
         * Decision COUNT нужен только auto.
         */
        $schedulesCount = null;

        if ($processingMode === 'auto') {
            $schedulesCount = $this->baseQuery()->count();

            $useServerProcessing = app(
                ProcessingModeService::class
            )->shouldUseServer(
                $processingMode,
                $schedulesCount,
                300
            );
        } else {
            $useServerProcessing =
                $processingMode === 'server';
        }

        try {
            $schedules = $this->getIndexSchedules(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            /**
             * Получаем total без лишнего COUNT.
             */
            if ($schedulesCount === null) {
                if (!$useServerProcessing) {
                    $schedulesCount =
                        $schedules->count();
                } elseif ($search === '') {
                    $schedulesCount =
                        $schedules->total();
                } else {
                    $schedulesCount =
                        $this->baseQuery()->count();
                }
            }

            return Inertia::render(
                'Admin/School/SchoolCourseSchedules/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolCourseSchedulesPerPage' =>
                        $perPage,

                    'adminSchoolCourseSchedulesDefaultSort' =>
                        $defaultSort,

                    'adminSchoolCourseSchedulesProcessingMode' =>
                        $processingMode,

                    /**
                     * Admin Index использует
                     * только Shared Resource.
                     */
                    'schedules' =>
                        SchoolCourseScheduleSharedResource::collection(
                            $schedules
                        ),

                    'schedulesCount' =>
                        $schedulesCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка school course schedules: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolCourseSchedules/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolCourseSchedulesPerPage' =>
                        $perPage,

                    'adminSchoolCourseSchedulesDefaultSort' =>
                        $defaultSort,

                    'adminSchoolCourseSchedulesProcessingMode' =>
                        $processingMode,

                    'schedules' => [],
                    'schedulesCount' => 0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'error' =>
                        'Ошибка загрузки расписаний.',
                ]
            );
        }
    }

    /** Страница создания расписания */
    public function create(Request $request): Response
    {
        $currentLocale =
            $this->resolveLocale($request);

        return Inertia::render(
            'Admin/School/SchoolCourseSchedules/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),

                'instructors' =>
                    $this->instructorsForSelect(
                        $currentLocale
                    ),
            ]
        );
    }

    /** Создание расписания */
    public function store(
        SchoolCourseScheduleRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $translations =
            $data['translations'] ?? [];

        $imagesData =
            $data['images'] ?? [];

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
                if (
                    !isset($data['sort'])
                    || is_null($data['sort'])
                ) {
                    $maxSort =
                        SchoolCourseSchedule::query()
                            ->max('sort');

                    $data['sort'] =
                        is_null($maxSort)
                            ? 1
                            : $maxSort + 1;
                }

                $schedule =
                    SchoolCourseSchedule::create(
                        $data
                    );

                $this->syncTranslations(
                    $schedule,
                    $translations
                );

                $this->syncImages(
                    $schedule,
                    $request,
                    $imagesData
                );
            });

            return redirect()
                ->route(
                    'admin.schoolCourseSchedules.index'
                )
                ->with(
                    'success',
                    'Расписание успешно создано.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка создания school course schedule: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании расписания.'
                );
        }
    }

    /** Редирект на страницу редактирования */
    public function show(
        int $schoolCourseSchedule
    ): RedirectResponse {
        return redirect()->route(
            'admin.schoolCourseSchedules.edit',
            $schoolCourseSchedule
        );
    }

    /** Страница редактирования расписания */
    public function edit(
        int $schoolCourseSchedule,
        Request $request
    ): Response {
        $currentLocale =
            $this->resolveLocale($request);

        $schedule = $this->baseQuery()
            ->with([
                /**
                 * Все переводы самого потока
                 * нужны TranslationTabs.
                 */
                'translations',

                /**
                 * Изображения + Spatie Media.
                 */
                'images.media',

                /**
                 * Курс текущей locale.
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
                 * Преподаватель текущей locale.
                 */
                'instructor' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),

                    'user:id,name,email',
                ]),
            ])
            ->withCount([
                'images',
                'cohortEnrollments',
            ])
            ->findOrFail(
                $schoolCourseSchedule
            );

        return Inertia::render(
            'Admin/School/SchoolCourseSchedules/Edit',
            [
                'schedule' =>
                    new SchoolCourseScheduleResource(
                        $schedule
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),

                'instructors' =>
                    $this->instructorsForSelect(
                        $currentLocale
                    ),
            ]
        );
    }

    /** Обновление расписания */
    public function update(
        SchoolCourseScheduleRequest $request,
        int $schoolCourseSchedule
    ): RedirectResponse {
        $schedule = $this->baseQuery()
            ->with('images')
            ->findOrFail(
                $schoolCourseSchedule
            );

        $data =
            $request->validated();

        $translations =
            $data['translations'] ?? [];

        $imagesData =
            $data['images'] ?? [];

        $deletedImageIds =
            $data['deletedImages'] ?? [];

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
                $schedule->update(
                    $data
                );

                $this->syncTranslations(
                    $schedule,
                    $translations
                );

                $this->syncImages(
                    $schedule,
                    $request,
                    $imagesData,
                    $deletedImageIds
                );
            });

            return redirect()
                ->route(
                    'admin.schoolCourseSchedules.index'
                )
                ->with(
                    'success',
                    'Расписание успешно обновлено.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления school course schedule ID '
                . $schedule->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при обновлении расписания.'
                );
        }
    }

    /** Удаление расписания */
    public function destroy(
        int $schoolCourseSchedule
    ): RedirectResponse {
        $schedule = $this->baseQuery()
            ->with('images')
            ->findOrFail(
                $schoolCourseSchedule
            );

        try {
            DB::transaction(function () use (
                $schedule
            ) {
                $imageIds =
                    $schedule->images()
                        ->pluck(
                            'school_course_schedule_images.id'
                        )
                        ->toArray();

                if (!empty($imageIds)) {
                    $schedule
                        ->images()
                        ->detach();

                    $this->deleteImages(
                        $imageIds
                    );
                }

                $schedule->delete();
            });

            return redirect()
                ->route(
                    'admin.schoolCourseSchedules.index'
                )
                ->with(
                    'success',
                    'Расписание успешно удалено.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления school course schedule ID '
                . $schedule->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении расписания.'
            );
        }
    }

    /** Клонирование расписания */
    public function clone(
        int $schoolCourseSchedule
    ): RedirectResponse {
        $schedule = $this->baseQuery()
            ->with('images')
            ->findOrFail(
                $schoolCourseSchedule
            );

        try {
            DB::transaction(function () use (
                $schedule
            ) {
                $clone =
                    $schedule->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                $clone->slug =
                    $schedule->slug
                    . '-copy-'
                    . time();

                $clone->sort =
                    ((int) SchoolCourseSchedule::max(
                        'sort'
                    )) + 1;

                $clone->activity = false;
                $clone->status = 'draft';

                $clone->save();

                foreach (
                    $schedule->images
                    as $image
                ) {
                    $clone
                        ->images()
                        ->attach(
                            $image->id,
                            [
                                'order' =>
                                    $image->pivot->order
                                    ?? 0,
                            ]
                        );
                }
            });

            return redirect()
                ->route(
                    'admin.schoolCourseSchedules.index'
                )
                ->with(
                    'success',
                    'Расписание успешно клонировано.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка клонирования school course schedule ID '
                . $schedule->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при клонировании расписания.'
            );
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

    /** Преподаватели для select */
    private function instructorsForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $instructors =
            SchoolInstructorProfile::query()
                ->with([
                    'translations' => fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),

                    'user:id,name,email',
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

    /** Базовый запрос Admin Index */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                /**
                 * Только выбранная locale.
                 */
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                /**
                 * Изображения + media
                 * для thumbnail_url.
                 */
                'images.media',

                /**
                 * Курс.
                 */
                'course' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $locale
                    ),
                ]),

                /**
                 * Преподаватель.
                 */
                'instructor' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $locale
                    ),

                    'user:id,name,email',
                ]),
            ])
            ->withCount([
                'images',
                'cohortEnrollments',
            ]);
    }

    /** Получение списка по активному режиму обработки */
    private function getIndexSchedules(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query =
            $this->indexQuery(
                $locale
            );

        if ($useServerProcessing) {
            return $query
                ->search(
                    $search,
                    $locale
                )
                ->sortByParam(
                    $sort,
                    $locale
                )
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        return $query
            ->sortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
