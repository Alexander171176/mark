<?php

namespace App\Http\Controllers\Admin\School\SchoolEnrollment;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use App\Http\Resources\Admin\School\SchoolEnrollment\SchoolEnrollmentResource;
use App\Http\Resources\Admin\School\SchoolEnrollment\SchoolEnrollmentSharedResource;
use App\Http\Resources\Admin\School\SchoolOrder\SchoolOrderSharedResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\SchoolEnrollment\SchoolEnrollment;
use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use App\Models\User;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Управление зачислениями пользователей
 * на курсы онлайн-школы.
 *
 * Admin contract:
 *
 * Index:
 * SchoolEnrollmentSharedResource
 *
 * Edit:
 * SchoolEnrollmentResource
 *
 * Create / Edit selectors:
 * SharedResource связанных сущностей.
 */
class SchoolEnrollmentController extends Controller
{
    /* =========================================================
     | INDEX
     ========================================================= */

    /**
     * Список зачислений.
     */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale(
            $request
        );

        /**
         * Дополнительные фильтры.
         */
        $status = $request->query(
            'status'
        );

        $userId = $request->query(
            'user_id'
        );

        $courseId = $request->query(
            'school_course_id'
        );

        $scheduleId = $request->query(
            'school_course_schedule_id'
        );

        /**
         * Настройки.
         */
        $settings = app(
            AdminSettingsService::class
        );

        $perPage = $settings->int(
            'adminSchoolEnrollmentsPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolEnrollmentsDefaultSort',
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
            'adminSchoolEnrollmentsProcessingMode',
            'frontend'
        );

        /**
         * Определение режима.
         *
         * server:
         * дополнительный COUNT не нужен.
         *
         * frontend:
         * дополнительный COUNT не нужен.
         *
         * auto:
         * один COUNT нужен для определения
         * server/frontend.
         */
        $autoCount = null;

        if ($processingMode === 'server') {
            $useServerProcessing = true;
        } elseif ($processingMode === 'frontend') {
            $useServerProcessing = false;
        } else {
            $autoCount = $this->countIndexEnrollments(
                status: $status,
                userId: $userId,
                courseId: $courseId,
                scheduleId: $scheduleId,
            );

            $useServerProcessing = app(
                ProcessingModeService::class
            )->shouldUseServer(
                $processingMode,
                $autoCount,
                300
            );
        }

        try {
            $enrollments = $this->getIndexEnrollments(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                status: $status,
                userId: $userId,
                courseId: $courseId,
                scheduleId: $scheduleId,
            );

            /**
             * server:
             * total берём из paginator.
             *
             * frontend:
             * Collection уже загружена целиком.
             *
             * auto/frontend:
             * используем уже выполненный autoCount.
             */
            $enrollmentsCount = $useServerProcessing
                ? $enrollments->total()
                : (
                    $autoCount
                    ?? $enrollments->count()
                );

            return Inertia::render(
                'Admin/School/SchoolEnrollments/Index',
                [
                    /**
                     * Locale.
                     */
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    /**
                     * Processing.
                     */
                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolEnrollmentsPerPage' =>
                        $perPage,

                    'adminSchoolEnrollmentsDefaultSort' =>
                        $defaultSort,

                    'adminSchoolEnrollmentsProcessingMode' =>
                        $processingMode,

                    /**
                     * Index использует
                     * только SharedResource.
                     */
                    'enrollments' =>
                        SchoolEnrollmentSharedResource::collection(
                            $enrollments
                        ),

                    'enrollmentsCount' =>
                        $enrollmentsCount,

                    /**
                     * Query state.
                     */
                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' => [
                        'status' =>
                            $status,

                        'user_id' =>
                            $userId,

                        'school_course_id' =>
                            $courseId,

                        'school_course_schedule_id' =>
                            $scheduleId,
                    ],
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки school enrollments: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolEnrollments/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolEnrollmentsPerPage' =>
                        $perPage,

                    'adminSchoolEnrollmentsDefaultSort' =>
                        $defaultSort,

                    'adminSchoolEnrollmentsProcessingMode' =>
                        $processingMode,

                    'enrollments' =>
                        [],

                    'enrollmentsCount' =>
                        0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' => [
                        'status' =>
                            $status,

                        'user_id' =>
                            $userId,

                        'school_course_id' =>
                            $courseId,

                        'school_course_schedule_id' =>
                            $scheduleId,
                    ],

                    'error' =>
                        'Ошибка загрузки зачислений.',
                ]
            );
        }
    }

    /* =========================================================
     | CREATE
     ========================================================= */

    /**
     * Страница создания.
     *
     * Сам SchoolEnrollment
     * переводов не имеет.
     *
     * currentLocale используется
     * для связанных Course / Schedule.
     */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/School/SchoolEnrollments/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'users' =>
                    $this->usersForSelect(),

                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),

                'schedules' =>
                    $this->schedulesForSelect(
                        $currentLocale
                    ),

                'orders' =>
                    $this->ordersForSelect(),
            ]
        );
    }

    /**
     * Сохранение.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $data = $this->validateEnrollment(
            $request
        );

        try {
            SchoolEnrollment::query()
                ->create(
                    $data
                );

            return redirect()
                ->route(
                    'admin.schoolEnrollments.index'
                )
                ->with(
                    'success',
                    'Зачисление успешно создано.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка создания school enrollment: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании зачисления.'
                );
        }
    }

    /* =========================================================
     | SHOW
     ========================================================= */

    /**
     * Show перенаправляет на Edit.
     */
    public function show(
        int $schoolEnrollment
    ): RedirectResponse {
        return redirect()->route(
            'admin.schoolEnrollments.edit',
            $schoolEnrollment
        );
    }

    /* =========================================================
     | EDIT
     ========================================================= */

    /**
     * Страница редактирования.
     */
    public function edit(
        Request $request,
        int $schoolEnrollment
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        /**
         * ВАЖНО:
         *
         * callbacks relations внутри with()
         * специально НЕ типизируем Builder.
         *
         * Laravel передаёт сюда Relation:
         *
         * BelongsTo
         * HasMany
         * HasOne
         * ...
         */
        $enrollment = SchoolEnrollment::query()
            ->with([
                /**
                 * Пользователь.
                 */
                'user:id,name,email',

                /**
                 * Курс.
                 */
                'course' => function ($query) use ($currentLocale) {
                    $query->with([
                        'translations' => function ($query) use ($currentLocale) {
                            $query->where(
                                'locale',
                                $currentLocale
                            );
                        },
                    ]);
                },

                /**
                 * Поток.
                 */
                'schedule' => function ($query) use ($currentLocale) {
                    $query->with([
                        /**
                         * Перевод потока.
                         */
                        'translations' => function ($query) use ($currentLocale) {
                            $query->where(
                                'locale',
                                $currentLocale
                            );
                        },

                        /**
                         * Курс потока.
                         */
                        'course' => function ($query) use ($currentLocale) {
                            $query->with([
                                'translations' => function ($query) use ($currentLocale) {
                                    $query->where(
                                        'locale',
                                        $currentLocale
                                    );
                                },
                            ]);
                        },
                    ]);
                },

                /**
                 * Заказ.
                 */
                'order',

                /**
                 * Дополнительные данные Edit.
                 */
                'progressRecords',

                'certificate',
            ])
            ->withCount([
                'progressRecords',
            ])
            ->findOrFail(
                $schoolEnrollment
            );

        return Inertia::render(
            'Admin/School/SchoolEnrollments/Edit',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                /**
                 * Edit использует
                 * полный Resource.
                 */
                'enrollment' =>
                    new SchoolEnrollmentResource(
                        $enrollment
                    ),

                /**
                 * Selector collections.
                 */
                'users' =>
                    $this->usersForSelect(),

                'courses' =>
                    $this->coursesForSelect(
                        $currentLocale
                    ),

                'schedules' =>
                    $this->schedulesForSelect(
                        $currentLocale
                    ),

                'orders' =>
                    $this->ordersForSelect(),
            ]
        );
    }

    /**
     * Обновление.
     */
    public function update(
        Request $request,
        int $schoolEnrollment
    ): RedirectResponse {
        $enrollment = SchoolEnrollment::query()
            ->findOrFail(
                $schoolEnrollment
            );

        $data = $this->validateEnrollment(
            $request
        );

        try {
            $enrollment->update(
                $data
            );

            return redirect()
                ->route(
                    'admin.schoolEnrollments.index'
                )
                ->with(
                    'success',
                    'Зачисление успешно обновлено.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления school enrollment ID '
                . $enrollment->id
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
                    'Ошибка при обновлении зачисления.'
                );
        }
    }

    /* =========================================================
     | DELETE
     ========================================================= */

    /**
     * Удаление.
     */
    public function destroy(
        int $schoolEnrollment
    ): RedirectResponse {
        $enrollment = SchoolEnrollment::query()
            ->findOrFail(
                $schoolEnrollment
            );

        try {
            $enrollment->delete();

            return redirect()
                ->route(
                    'admin.schoolEnrollments.index'
                )
                ->with(
                    'success',
                    'Зачисление успешно удалено.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления school enrollment ID '
                . $enrollment->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении зачисления.'
            );
        }
    }

    /**
     * Массовое удаление.
     */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse {
        $data = $request->validate([
            'ids' => [
                'required',
                'array',
            ],

            'ids.*' => [
                'required',
                'integer',
                'exists:school_enrollments,id',
            ],
        ]);

        try {
            DB::transaction(
                function () use ($data) {
                    SchoolEnrollment::query()
                        ->whereIn(
                            'id',
                            $data['ids']
                        )
                        ->delete();
                }
            );

            return back()->with(
                'success',
                'Выбранные зачисления успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового удаления school enrollments: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении зачислений.'
            );
        }
    }

    /* =========================================================
     | STATUS / NOTES
     ========================================================= */

    /**
     * Обновление статуса.
     */
    public function updateStatus(
        Request $request,
        int $schoolEnrollment
    ): RedirectResponse {
        $data = $request->validate([
            'status' => [
                'required',
                'string',
                'max:32',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $enrollment = SchoolEnrollment::query()
            ->findOrFail(
                $schoolEnrollment
            );

        try {
            $enrollment->status =
                $data['status'];

            /**
             * Первый переход в active.
             */
            if (
                $data['status'] === 'active'
                && !$enrollment->started_at
            ) {
                $enrollment->started_at =
                    now();
            }

            /**
             * Завершение.
             */
            if (
                $data['status'] === 'completed'
                && !$enrollment->completed_at
            ) {
                $enrollment->completed_at =
                    now();

                $enrollment->progress_percent =
                    100;
            }

            /**
             * Notes меняем только если
             * поле действительно передано.
             */
            if (
                array_key_exists(
                    'notes',
                    $data
                )
            ) {
                $enrollment->notes =
                    $data['notes'];
            }

            $enrollment->save();

            return back()->with(
                'success',
                'Статус зачисления успешно обновлён.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления статуса school enrollment ID '
                . $enrollment->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при обновлении статуса зачисления.'
            );
        }
    }

    /**
     * Обновление заметок.
     */
    public function updateNotes(
        Request $request,
        int $schoolEnrollment
    ): RedirectResponse {
        $data = $request->validate([
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $enrollment = SchoolEnrollment::query()
            ->findOrFail(
                $schoolEnrollment
            );

        try {
            $enrollment->update([
                'notes' =>
                    $data['notes']
                    ?? null,
            ]);

            return back()->with(
                'success',
                'Заметки зачисления успешно обновлены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления заметок school enrollment ID '
                . $enrollment->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при обновлении заметок зачисления.'
            );
        }
    }

    /* =========================================================
     | VALIDATION
     ========================================================= */

    /**
     * Валидация Create / Edit.
     */
    private function validateEnrollment(
        Request $request
    ): array {
        return $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'school_course_id' => [
                'required',
                'integer',
                'exists:school_courses,id',
            ],

            'school_course_schedule_id' => [
                'nullable',
                'integer',
                'exists:school_course_schedules,id',
            ],

            'school_order_id' => [
                'nullable',
                'integer',
                'exists:school_orders,id',
            ],

            'status' => [
                'required',
                'string',
                'max:32',
            ],

            'started_at' => [
                'nullable',
                'date',
            ],

            'expires_at' => [
                'nullable',
                'date',
            ],

            'completed_at' => [
                'nullable',
                'date',
            ],

            'progress_percent' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'meta' => [
                'nullable',
                'array',
            ],
        ]);
    }

    /* =========================================================
     | CREATE / EDIT SELECTS
     ========================================================= */

    /**
     * Пользователи.
     */
    private function usersForSelect(): Collection
    {
        return User::query()
            ->select([
                'id',
                'name',
                'email',
            ])
            ->orderBy(
                'name'
            )
            ->get();
    }

    /**
     * Курсы.
     *
     * Только выбранная locale.
     */
    private function coursesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $courses = SchoolCourse::query()
            ->with([
                /**
                 * Relation callback
                 * НЕ типизируем Builder.
                 */
                'translations' => function ($query) use ($locale) {
                    $query->where(
                        'locale',
                        $locale
                    );
                },
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

    /**
     * Потоки.
     *
     * Только выбранная locale:
     *
     * schedule.translations
     * schedule.course.translations
     */
    private function schedulesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $schedules = SchoolCourseSchedule::query()
            ->with([
                /**
                 * Перевод Schedule.
                 */
                'translations' => function ($query) use ($locale) {
                    $query->where(
                        'locale',
                        $locale
                    );
                },

                /**
                 * Course Schedule-а.
                 */
                'course' => function ($query) use ($locale) {
                    $query->with([
                        'translations' => function ($query) use ($locale) {
                            $query->where(
                                'locale',
                                $locale
                            );
                        },
                    ]);
                },
            ])
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            )
            ->get();

        return SchoolCourseScheduleSharedResource::collection(
            $schedules
        );
    }

    /**
     * Заказы.
     */
    private function ordersForSelect(): AnonymousResourceCollection
    {
        $orders = SchoolOrder::query()
            ->with([
                'user:id,name,email',
            ])
            ->orderByDesc(
                'id'
            )
            ->get();

        return SchoolOrderSharedResource::collection(
            $orders
        );
    }

    /* =========================================================
     | INDEX QUERY
     ========================================================= */

    /**
     * Базовый запрос Admin Index.
     *
     * Index НЕ загружает:
     *
     * - selector collections;
     * - все translations;
     * - progressRecords collection.
     *
     * Загружается только то,
     * что реально использует SharedResource.
     */
    private function indexQuery(
        string $locale,
        ?string $status = null,
        mixed $userId = null,
        mixed $courseId = null,
        mixed $scheduleId = null,
    ): Builder {
        return SchoolEnrollment::query()
            ->addSelect(
                'school_enrollments.*'
            )
            ->with([
                /**
                 * Пользователь.
                 */
                'user:id,name,email',

                /**
                 * Курс.
                 */
                'course' => function ($query) use ($locale) {
                    $query->with([
                        'translations' => function ($query) use ($locale) {
                            $query->where(
                                'locale',
                                $locale
                            );
                        },
                    ]);
                },

                /**
                 * Поток.
                 */
                'schedule' => function ($query) use ($locale) {
                    $query->with([
                        /**
                         * Schedule translation.
                         */
                        'translations' => function ($query) use ($locale) {
                            $query->where(
                                'locale',
                                $locale
                            );
                        },

                        /**
                         * Course Schedule-а.
                         */
                        'course' => function ($query) use ($locale) {
                            $query->with([
                                'translations' => function ($query) use ($locale) {
                                    $query->where(
                                        'locale',
                                        $locale
                                    );
                                },
                            ]);
                        },
                    ]);
                },

                /**
                 * Заказ.
                 */
                'order',

                /**
                 * Index отображает наличие
                 * сертификата.
                 */
                'certificate',
            ])
            ->withCount([
                'progressRecords',
            ])

            /**
             * Дополнительные фильтры.
             */
            ->when(
                $status,
                fn (Builder $query) =>
                $query->where(
                    'school_enrollments.status',
                    $status
                )
            )
            ->when(
                $userId,
                fn (Builder $query) =>
                $query->where(
                    'school_enrollments.user_id',
                    (int) $userId
                )
            )
            ->when(
                $courseId,
                fn (Builder $query) =>
                $query->where(
                    'school_enrollments.school_course_id',
                    (int) $courseId
                )
            )
            ->when(
                $scheduleId,
                fn (Builder $query) =>
                $query->where(
                    'school_enrollments.school_course_schedule_id',
                    (int) $scheduleId
                )
            );
    }

    /**
     * COUNT только для AUTO.
     *
     * Без:
     *
     * eager loading
     * withCount
     * Resource
     */
    private function countIndexEnrollments(
        ?string $status = null,
        mixed $userId = null,
        mixed $courseId = null,
        mixed $scheduleId = null,
    ): int {
        return SchoolEnrollment::query()
            ->when(
                $status,
                fn (Builder $query) =>
                $query->where(
                    'status',
                    $status
                )
            )
            ->when(
                $userId,
                fn (Builder $query) =>
                $query->where(
                    'user_id',
                    (int) $userId
                )
            )
            ->when(
                $courseId,
                fn (Builder $query) =>
                $query->where(
                    'school_course_id',
                    (int) $courseId
                )
            )
            ->when(
                $scheduleId,
                fn (Builder $query) =>
                $query->where(
                    'school_course_schedule_id',
                    (int) $scheduleId
                )
            )
            ->count();
    }

    /* =========================================================
     | SERVER SEARCH
     ========================================================= */

    /**
     * Серверный поиск.
     *
     * Переводимые связи ищутся
     * только в выбранной locale.
     */
    private function applySearch(
        Builder $query,
        string $search,
        string $locale
    ): Builder {
        if ($search === '') {
            return $query;
        }

        return $query->where(
            function (Builder $query) use (
                $search,
                $locale
            ) {
                $query
                    /**
                     * Enrollment.
                     */
                    ->where(
                        'school_enrollments.id',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'school_enrollments.status',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'school_enrollments.notes',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'school_enrollments.progress_percent',
                        'like',
                        "%{$search}%"
                    )

                    /**
                     * User.
                     */
                    ->orWhereHas(
                        'user',
                        function (Builder $userQuery) use ($search) {
                            $userQuery
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    )

                    /**
                     * Course slug.
                     */
                    ->orWhereHas(
                        'course',
                        function (Builder $courseQuery) use ($search) {
                            $courseQuery->where(
                                'slug',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )

                    /**
                     * Course translation.
                     */
                    ->orWhereHas(
                        'course.translations',
                        function (Builder $courseTranslationQuery) use (
                            $search,
                            $locale
                        ) {
                            $courseTranslationQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (Builder $translationQuery) use ($search) {
                                        $translationQuery
                                            ->where(
                                                'title',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'subtitle',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'short',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'description',
                                                'like',
                                                "%{$search}%"
                                            );
                                    }
                                );
                        }
                    )

                    /**
                     * Schedule slug.
                     */
                    ->orWhereHas(
                        'schedule',
                        function (Builder $scheduleQuery) use ($search) {
                            $scheduleQuery->where(
                                'slug',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )

                    /**
                     * Schedule translation.
                     */
                    ->orWhereHas(
                        'schedule.translations',
                        function (Builder $scheduleTranslationQuery) use (
                            $search,
                            $locale
                        ) {
                            $scheduleTranslationQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (Builder $translationQuery) use ($search) {
                                        $translationQuery
                                            ->where(
                                                'title',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'subtitle',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'short',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'description',
                                                'like',
                                                "%{$search}%"
                                            );
                                    }
                                );
                        }
                    )

                    /**
                     * Course связанного Schedule.
                     *
                     * Добавляем для симметрии
                     * с frontend-поиском.
                     */
                    ->orWhereHas(
                        'schedule.course.translations',
                        function (Builder $translationQuery) use (
                            $search,
                            $locale
                        ) {
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (Builder $query) use ($search) {
                                        $query
                                            ->where(
                                                'title',
                                                'like',
                                                "%{$search}%"
                                            )
                                            ->orWhere(
                                                'short',
                                                'like',
                                                "%{$search}%"
                                            );
                                    }
                                );
                        }
                    )

                    /**
                     * Order.
                     */
                    ->orWhereHas(
                        'order',
                        function (Builder $orderQuery) use ($search) {
                            $orderQuery
                                ->where(
                                    'id',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'number',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
            }
        );
    }

    /* =========================================================
     | SERVER SORT
     ========================================================= */

    /**
     * Серверная сортировка.
     */
    private function applySort(
        Builder $query,
        string $sort,
        string $locale
    ): Builder {
        return match ($sort) {
            /* ================= ID ================= */

            'idAsc' =>
            $query->orderBy(
                'school_enrollments.id',
                'asc'
            ),

            'idDesc' =>
            $query->orderBy(
                'school_enrollments.id',
                'desc'
            ),

            /* ================= STARTED ================= */

            'startedAtAsc' =>
            $query
                ->orderBy(
                    'school_enrollments.started_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            'startedAtDesc' =>
            $query
                ->orderBy(
                    'school_enrollments.started_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            /* ================= EXPIRES ================= */

            'expiresAtAsc' =>
            $query
                ->orderBy(
                    'school_enrollments.expires_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            'expiresAtDesc' =>
            $query
                ->orderBy(
                    'school_enrollments.expires_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            /* ================= COMPLETED ================= */

            'completedAtAsc' =>
            $query
                ->orderBy(
                    'school_enrollments.completed_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            'completedAtDesc' =>
            $query
                ->orderBy(
                    'school_enrollments.completed_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            /* ================= PROGRESS ================= */

            'progressAsc' =>
            $query
                ->orderBy(
                    'school_enrollments.progress_percent',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            'progressDesc' =>
            $query
                ->orderBy(
                    'school_enrollments.progress_percent',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            /* ================= STATUS ================= */

            'statusAsc' =>
            $query
                ->orderBy(
                    'school_enrollments.status',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            'statusDesc' =>
            $query
                ->orderBy(
                    'school_enrollments.status',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            /* ================= USER NAME ================= */

            'userNameAsc' =>
            $query
                ->join(
                    'users as users_sort',
                    'school_enrollments.user_id',
                    '=',
                    'users_sort.id'
                )
                ->orderBy(
                    'users_sort.name',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                )
                ->addSelect(
                    'school_enrollments.*'
                ),

            'userNameDesc' =>
            $query
                ->join(
                    'users as users_sort',
                    'school_enrollments.user_id',
                    '=',
                    'users_sort.id'
                )
                ->orderBy(
                    'users_sort.name',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                )
                ->addSelect(
                    'school_enrollments.*'
                ),

            /* ================= USER EMAIL ================= */

            'userEmailAsc' =>
            $query
                ->join(
                    'users as users_sort',
                    'school_enrollments.user_id',
                    '=',
                    'users_sort.id'
                )
                ->orderBy(
                    'users_sort.email',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                )
                ->addSelect(
                    'school_enrollments.*'
                ),

            'userEmailDesc' =>
            $query
                ->join(
                    'users as users_sort',
                    'school_enrollments.user_id',
                    '=',
                    'users_sort.id'
                )
                ->orderBy(
                    'users_sort.email',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                )
                ->addSelect(
                    'school_enrollments.*'
                ),

            /* ================= COURSE TITLE ================= */

            'courseTitleAsc' =>
            $query
                ->leftJoin(
                    'school_course_translations as sct_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'school_enrollments.school_course_id',
                                '=',
                                'sct_sort.school_course_id'
                            )
                            ->where(
                                'sct_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->orderBy(
                    'sct_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                )
                ->addSelect(
                    'school_enrollments.*'
                ),

            'courseTitleDesc' =>
            $query
                ->leftJoin(
                    'school_course_translations as sct_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'school_enrollments.school_course_id',
                                '=',
                                'sct_sort.school_course_id'
                            )
                            ->where(
                                'sct_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->orderBy(
                    'sct_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                )
                ->addSelect(
                    'school_enrollments.*'
                ),

            /* ================= SCHEDULE TITLE ================= */

            'scheduleTitleAsc' =>
            $query
                ->leftJoin(
                    'school_course_schedule_translations as scst_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'school_enrollments.school_course_schedule_id',
                                '=',
                                'scst_sort.school_course_schedule_id'
                            )
                            ->where(
                                'scst_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->orderBy(
                    'scst_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                )
                ->addSelect(
                    'school_enrollments.*'
                ),

            'scheduleTitleDesc' =>
            $query
                ->leftJoin(
                    'school_course_schedule_translations as scst_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'school_enrollments.school_course_schedule_id',
                                '=',
                                'scst_sort.school_course_schedule_id'
                            )
                            ->where(
                                'scst_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->orderBy(
                    'scst_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                )
                ->addSelect(
                    'school_enrollments.*'
                ),

            /* ================= PROGRESS RECORDS ================= */

            'progressRecordsAsc' =>
            $query
                ->orderBy(
                    'progress_records_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            'progressRecordsDesc' =>
            $query
                ->orderBy(
                    'progress_records_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            /* ================= CREATED ================= */

            'createdAtAsc' =>
            $query
                ->orderBy(
                    'school_enrollments.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            'createdAtDesc' =>
            $query
                ->orderBy(
                    'school_enrollments.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            /* ================= UPDATED ================= */

            'updatedAtAsc' =>
            $query
                ->orderBy(
                    'school_enrollments.updated_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            'updatedAtDesc' =>
            $query
                ->orderBy(
                    'school_enrollments.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_enrollments.id'
                ),

            default =>
            $query->orderByDesc(
                'school_enrollments.id'
            ),
        };
    }

    /* =========================================================
     | INDEX DATA
     ========================================================= */

    /**
     * Получение Index-данных.
     */
    private function getIndexEnrollments(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        ?string $status = null,
        mixed $userId = null,
        mixed $courseId = null,
        mixed $scheduleId = null,
    ): Collection|LengthAwarePaginator {
        $query = $this->indexQuery(
            locale: $locale,
            status: $status,
            userId: $userId,
            courseId: $courseId,
            scheduleId: $scheduleId,
        );

        /**
         * SERVER:
         *
         * search
         * sort
         * pagination
         */
        if ($useServerProcessing) {
            return $this->applySort(
                $this->applySearch(
                    $query,
                    $search,
                    $locale
                ),
                $sort,
                $locale
            )
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        /**
         * FRONTEND:
         *
         * Загружается вся коллекция.
         *
         * Search / pagination выполняет Vue.
         *
         * Начальная сортировка выполняется
         * на backend.
         */
        return $this->applySort(
            $query,
            $sort,
            $locale
        )->get();
    }

    /* =========================================================
     | LOCALE
     ========================================================= */

    /**
     * Определение locale.
     */
    private function resolveLocale(
        Request $request
    ): string {
        $availableLocales =
            $this->availableLocales();

        $locale = (string) $request->query(
            'locale',
            app()->getLocale()
        );

        if (
            !in_array(
                $locale,
                $availableLocales,
                true
            )
        ) {
            $locale = config(
                'app.fallback_locale',
                'ru'
            );
        }

        app()->setLocale(
            $locale
        );

        return $locale;
    }

    /**
     * Доступные locale.
     */
    private function availableLocales(): array
    {
        $locales = config(
            'app.available_locales',
            [
                'ru',
                'en',
            ]
        );

        if (!is_array($locales)) {
            return [
                'ru',
                'en',
            ];
        }

        /**
         * Поддерживаем:
         *
         * ['ru', 'en']
         *
         * и:
         *
         * [
         *     'ru' => 'Русский',
         *     'en' => 'English',
         * ]
         */
        return array_is_list(
            $locales
        )
            ? array_values(
                $locales
            )
            : array_keys(
                $locales
            );
    }
}
