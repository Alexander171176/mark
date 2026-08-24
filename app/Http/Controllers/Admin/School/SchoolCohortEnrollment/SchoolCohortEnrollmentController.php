<?php

namespace App\Http\Controllers\Admin\School\SchoolCohortEnrollment;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Resources\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollmentResource;
use App\Http\Resources\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollmentSharedResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use App\Models\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollment;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\User;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SchoolCohortEnrollmentController extends BaseSchoolAdminController
{
    /**
     * Список записей пользователей на потоки.
     *
     * Поддерживаются режимы:
     *
     * - server;
     * - frontend;
     * - auto.
     *
     * Admin Index всегда использует
     * краткий SharedResource.
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $settings = app(
            AdminSettingsService::class
        );

        /**
         * Настройки Index.
         */
        $perPage = $settings->int(
            'adminSchoolCohortEnrollmentsPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolCohortEnrollmentsDefaultSort',
            'idDesc'
        );

        $processingMode = $settings->string(
            'adminSchoolCohortEnrollmentsProcessingMode',
            'frontend'
        );

        /**
         * Параметры страницы.
         */
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

        $status = $request->query(
            'status'
        );

        $scheduleId = $request->query(
            'school_course_schedule_id'
        );

        /**
         * В чистом frontend-режиме отдельный COUNT
         * не нужен.
         *
         * Мы всё равно получаем всю коллекцию,
         * поэтому total определим через Collection::count().
         *
         * Для server/auto COUNT нужен:
         *
         * - server: как total paginator;
         * - auto: для определения режима.
         */
        $knownTotal = null;

        if ($processingMode === 'frontend') {
            $useServerProcessing = false;
        } else {
            $knownTotal = $this->baseIndexQuery(
                status: $status,
                scheduleId: $scheduleId,
            )->count();

            $useServerProcessing = app(
                ProcessingModeService::class
            )->shouldUseServer(
                $processingMode,
                $knownTotal,
                300
            );
        }

        try {
            /**
             * Получаем данные согласно
             * фактически выбранному режиму.
             */
            $enrollments = $this->getIndexEnrollments(
                locale: $locale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                status: $status,
                scheduleId: $scheduleId,
                knownTotal: $knownTotal,
            );

            /**
             * Для frontend-режима отдельного SQL COUNT
             * не было — total берём из Collection.
             *
             * Для server paginator уже знает total.
             */
            $enrollmentsCount = $useServerProcessing
                ? $enrollments->total()
                : $enrollments->count();

            return Inertia::render(
                'Admin/School/SchoolCohortEnrollments/Index',
                [
                    'currentLocale' =>
                        $locale,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolCohortEnrollmentsPerPage' =>
                        $perPage,

                    'adminSchoolCohortEnrollmentsDefaultSort' =>
                        $defaultSort,

                    'adminSchoolCohortEnrollmentsProcessingMode' =>
                        $processingMode,

                    /**
                     * Admin Index всегда работает
                     * через краткий Resource.
                     */
                    'enrollments' =>
                        SchoolCohortEnrollmentSharedResource::collection(
                            $enrollments
                        ),

                    'enrollmentsCount' =>
                        $enrollmentsCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    /**
                     * Оставляем реальные фильтры Index.
                     */
                    'filters' => [
                        'status' =>
                            $status,

                        'school_course_schedule_id' =>
                            $scheduleId,
                    ],
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки school cohort enrollments: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolCohortEnrollments/Index',
                [
                    'currentLocale' =>
                        $locale,

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolCohortEnrollmentsPerPage' =>
                        $perPage,

                    'adminSchoolCohortEnrollmentsDefaultSort' =>
                        $defaultSort,

                    'adminSchoolCohortEnrollmentsProcessingMode' =>
                        $processingMode,

                    'enrollments' => [],
                    'enrollmentsCount' => 0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' => [
                        'status' =>
                            $status,

                        'school_course_schedule_id' =>
                            $scheduleId,
                    ],

                    'error' =>
                        'Ошибка загрузки записей на потоки.',
                ]
            );
        }
    }

    /**
     * Страница создания записи пользователя на поток.
     *
     * Сам SchoolCohortEnrollment переводов не имеет.
     *
     * currentLocale используется только
     * для локализации связанных расписаний
     * и их курсов.
     */
    public function create(
        Request $request
    ): Response {
        $currentLocale =
            $this->resolveLocale(
                $request
            );

        return Inertia::render(
            'Admin/School/SchoolCohortEnrollments/Create',
            [
                /**
                 * Locale-механика формы.
                 */
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                /**
                 * Расписания для VueMultiselect.
                 *
                 * Schedule + Course приходят
                 * только в current locale.
                 */
                'schedules' =>
                    $this->schedulesForSelect(
                        $currentLocale
                    ),

                /**
                 * Пользователи не локализуются.
                 */
                'users' =>
                    $this->usersForSelect(),
            ]
        );
    }

    /**
     * Создание записи.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $data = $request->validate([
            'school_course_schedule_id' => [
                'required',
                'integer',
                'exists:school_course_schedules,id',
            ],

            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'status' => [
                'required',
                'string',
                'max:32',
            ],

            'enrolled_at' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            SchoolCohortEnrollment::query()
                ->create(
                    $data
                );

            return redirect()
                ->route(
                    'admin.schoolCohortEnrollments.index'
                )
                ->with(
                    'success',
                    'Запись на поток успешно создана.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка создания school cohort enrollment: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании записи на поток.'
                );
        }
    }

    /**
     * Редирект на редактирование.
     */
    public function show(
        int $schoolCohortEnrollment
    ): RedirectResponse {
        return redirect()->route(
            'admin.schoolCohortEnrollments.edit',
            $schoolCohortEnrollment
        );
    }

    /**
     * Страница редактирования записи пользователя на поток.
     */
    public function edit(
        int $schoolCohortEnrollment,
        Request $request
    ): Response {
        $currentLocale =
            $this->resolveLocale(
                $request
            );

        /**
         * SchoolCohortEnrollment сам
         * мультиязычным не является.
         *
         * Поэтому для самой записи
         * никаких translations загружать не нужно.
         *
         * Локализуем только связанные:
         *
         * - Schedule;
         * - Course Schedule-а.
         */
        $enrollment =
            SchoolCohortEnrollment::query()
                ->with([
                    /**
                     * Текущий поток.
                     */
                    'schedule' =>
                        fn ($query) =>
                        $query->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery->where(
                                    'locale',
                                    $currentLocale
                                ),

                            /**
                             * Курс выбранного потока.
                             */
                            'course' =>
                                fn ($courseQuery) =>
                                $courseQuery->with([
                                    'translations' =>
                                        fn ($translationQuery) =>
                                        $translationQuery->where(
                                            'locale',
                                            $currentLocale
                                        ),
                                ]),
                        ]),

                    /**
                     * Текущий пользователь.
                     */
                    'user:id,name,email',
                ])
                ->findOrFail(
                    $schoolCohortEnrollment
                );

        return Inertia::render(
            'Admin/School/SchoolCohortEnrollments/Edit',
            [
                /**
                 * Полный Admin Resource.
                 */
                'enrollment' =>
                    new SchoolCohortEnrollmentResource(
                        $enrollment
                    ),

                /**
                 * Locale-механика.
                 */
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                /**
                 * Selector-данные.
                 */
                'schedules' =>
                    $this->schedulesForSelect(
                        $currentLocale
                    ),

                'users' =>
                    $this->usersForSelect(),
            ]
        );
    }

    /**
     * Обновление записи.
     */
    public function update(
        Request $request,
        int $schoolCohortEnrollment
    ): RedirectResponse {
        $enrollment = SchoolCohortEnrollment::query()
            ->findOrFail(
                $schoolCohortEnrollment
            );

        $data = $request->validate([
            'school_course_schedule_id' => [
                'required',
                'integer',
                'exists:school_course_schedules,id',
            ],

            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'status' => [
                'required',
                'string',
                'max:32',
            ],

            'enrolled_at' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            $enrollment->update(
                $data
            );

            return redirect()
                ->route(
                    'admin.schoolCohortEnrollments.index'
                )
                ->with(
                    'success',
                    'Запись на поток успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления school cohort enrollment ID '
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
                    'Ошибка при обновлении записи на поток.'
                );
        }
    }

    /**
     * Удаление записи.
     */
    public function destroy(
        int $schoolCohortEnrollment
    ): RedirectResponse {
        $enrollment = SchoolCohortEnrollment::query()
            ->findOrFail(
                $schoolCohortEnrollment
            );

        try {
            $enrollment->delete();

            return redirect()
                ->route(
                    'admin.schoolCohortEnrollments.index'
                )
                ->with(
                    'success',
                    'Запись на поток успешно удалена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления school cohort enrollment ID '
                . $enrollment->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении записи на поток.'
            );
        }
    }

    /**
     * Массовое удаление записей.
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
                'exists:school_cohort_enrollments,id',
            ],
        ]);

        try {
            DB::transaction(
                function () use ($data) {
                    SchoolCohortEnrollment::query()
                        ->whereIn(
                            'id',
                            $data['ids']
                        )
                        ->delete();
                }
            );

            return back()->with(
                'success',
                'Выбранные записи на потоки успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового удаления school cohort enrollments: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении записей на потоки.'
            );
        }
    }

    /**
     * Обновление статуса записи.
     */
    public function updateStatus(
        Request $request,
        int $schoolCohortEnrollment
    ): RedirectResponse|JsonResponse {
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

        $enrollment = SchoolCohortEnrollment::query()
            ->findOrFail(
                $schoolCohortEnrollment
            );

        try {
            $enrollment->status =
                $data['status'];

            /**
             * При первом подтверждении
             * автоматически фиксируем дату зачисления.
             */
            if (
                $data['status'] === 'approved'
                && !$enrollment->enrolled_at
            ) {
                $enrollment->enrolled_at =
                    now();
            }

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

            $message =
                'Статус записи успешно обновлён.';

            return $request->expectsJson()
                ? response()->json([
                    'message' => $message,
                ])
                : back()->with(
                    'success',
                    $message
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления статуса school cohort enrollment ID '
                . $enrollment->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            $message =
                'Ошибка при обновлении статуса записи.';

            return $request->expectsJson()
                ? response()->json(
                    [
                        'message' => $message,
                    ],
                    500
                )
                : back()->with(
                    'error',
                    $message
                );
        }
    }

    /**
     * Обновление заметок записи.
     */
    public function updateNotes(
        Request $request,
        int $schoolCohortEnrollment
    ): RedirectResponse|JsonResponse {
        $data = $request->validate([
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $enrollment = SchoolCohortEnrollment::query()
            ->findOrFail(
                $schoolCohortEnrollment
            );

        try {
            $enrollment->update([
                'notes' =>
                    $data['notes'] ?? null,
            ]);

            $message =
                'Заметки записи успешно обновлены.';

            return $request->expectsJson()
                ? response()->json([
                    'message' => $message,
                ])
                : back()->with(
                    'success',
                    $message
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления заметок school cohort enrollment ID '
                . $enrollment->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            $message =
                'Ошибка при обновлении заметок записи.';

            return $request->expectsJson()
                ? response()->json(
                    [
                        'message' => $message,
                    ],
                    500
                )
                : back()->with(
                    'error',
                    $message
                );
        }
    }

    /* ==========================================================
     * SELECT DATA
     * ========================================================== */

    /**
     * Расписания потоков для Create/Edit.
     *
     * Загружается только выбранная locale.
     *
     * Важный контракт:
     *
     * schedule.translation
     * schedule.course.translation
     */
    private function schedulesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $schedules =
            SchoolCourseSchedule::query()
                ->with([
                    /**
                     * Перевод Schedule.
                     */
                    'translations' =>
                        fn ($query) =>
                        $query->where(
                            'locale',
                            $locale
                        ),

                    /**
                     * Курс + его перевод.
                     */
                    'course' =>
                        fn ($query) =>
                        $query->with([
                            'translations' =>
                                fn ($translationQuery) =>
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

        return SchoolCourseScheduleSharedResource::collection(
            $schedules
        );
    }

    /**
     * Пользователи для Create/Edit.
     */
    private function usersForSelect(): Collection
    {
        return User::query()
            ->select(
                'id',
                'name',
                'email'
            )
            ->orderBy(
                'name'
            )
            ->get();
    }

    /* ==========================================================
     * INDEX QUERY
     * ========================================================== */

    /**
     * Минимальный запрос для COUNT
     * и определения processing mode.
     *
     * Никаких eager-load здесь не требуется.
     */
    private function baseIndexQuery(
        ?string $status = null,
        mixed $scheduleId = null
    ): Builder {
        return SchoolCohortEnrollment::query()
            ->when(
                $status !== null
                && $status !== '',
                function (
                    Builder $query
                ) use ($status) {
                    $query->where(
                        'school_cohort_enrollments.status',
                        $status
                    );
                }
            )
            ->when(
                $scheduleId !== null
                && $scheduleId !== '',
                function (
                    Builder $query
                ) use ($scheduleId) {
                    $query->where(
                        'school_cohort_enrollments.school_course_schedule_id',
                        (int) $scheduleId
                    );
                }
            );
    }

    /**
     * Полный Index-query.
     *
     * Загружает только данные,
     * необходимые SharedResource и frontend-поиску.
     */
    private function indexQuery(
        string $locale,
        ?string $status = null,
        mixed $scheduleId = null
    ): Builder {
        return $this->baseIndexQuery(
            status: $status,
            scheduleId: $scheduleId,
        )
            ->with([
                /**
                 * Поток:
                 * только текущая locale.
                 */
                'schedule.translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),

                /**
                 * Курс потока:
                 * только текущая locale.
                 */
                'schedule.course.translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),

                /**
                 * Пользователь.
                 */
                'user:id,name,email',
            ]);
    }

    /**
     * Серверный поиск.
     *
     * Важно:
     * поиск по переводимым сущностям
     * ограничиваем той же locale,
     * которую видит frontend.
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
            function (
                Builder $builder
            ) use (
                $search,
                $locale
            ) {
                $builder
                    ->where(
                        'school_cohort_enrollments.status',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'school_cohort_enrollments.notes',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'user',
                        function (
                            Builder $userQuery
                        ) use ($search) {
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
                    ->orWhereHas(
                        'schedule.translations',
                        function (
                            Builder $scheduleQuery
                        ) use (
                            $search,
                            $locale
                        ) {
                            $scheduleQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (
                                        Builder $translationQuery
                                    ) use ($search) {
                                        $translationQuery
                                            ->where(
                                                'title',
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
                    ->orWhereHas(
                        'schedule.course.translations',
                        function (
                            Builder $courseQuery
                        ) use (
                            $search,
                            $locale
                        ) {
                            $courseQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (
                                        Builder $translationQuery
                                    ) use ($search) {
                                        $translationQuery
                                            ->where(
                                                'title',
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
                    );
            }
        );
    }

    /**
     * Сортировка записей.
     *
     * JOIN-ветки используют addSelect(),
     * чтобы не уничтожать уже существующие
     * select/subselect конструкции запроса.
     */
    private function applySort(
        Builder $query,
        string $sort,
        string $locale
    ): Builder {
        return match ($sort) {
            'idAsc' =>
            $query->orderBy(
                'school_cohort_enrollments.id',
                'asc'
            ),

            'idDesc' =>
            $query->orderBy(
                'school_cohort_enrollments.id',
                'desc'
            ),

            'enrolledAtAsc' =>
            $query
                ->orderBy(
                    'school_cohort_enrollments.enrolled_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'enrolledAtDesc' =>
            $query
                ->orderBy(
                    'school_cohort_enrollments.enrolled_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'statusAsc' =>
            $query
                ->orderBy(
                    'school_cohort_enrollments.status',
                    'asc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'statusDesc' =>
            $query
                ->orderBy(
                    'school_cohort_enrollments.status',
                    'desc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'userNameAsc' =>
            $query
                ->join(
                    'users as users_sort',
                    'school_cohort_enrollments.user_id',
                    '=',
                    'users_sort.id'
                )
                ->addSelect(
                    'school_cohort_enrollments.*'
                )
                ->orderBy(
                    'users_sort.name',
                    'asc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'userNameDesc' =>
            $query
                ->join(
                    'users as users_sort',
                    'school_cohort_enrollments.user_id',
                    '=',
                    'users_sort.id'
                )
                ->addSelect(
                    'school_cohort_enrollments.*'
                )
                ->orderBy(
                    'users_sort.name',
                    'desc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'userEmailAsc' =>
            $query
                ->join(
                    'users as users_sort',
                    'school_cohort_enrollments.user_id',
                    '=',
                    'users_sort.id'
                )
                ->addSelect(
                    'school_cohort_enrollments.*'
                )
                ->orderBy(
                    'users_sort.email',
                    'asc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'userEmailDesc' =>
            $query
                ->join(
                    'users as users_sort',
                    'school_cohort_enrollments.user_id',
                    '=',
                    'users_sort.id'
                )
                ->addSelect(
                    'school_cohort_enrollments.*'
                )
                ->orderBy(
                    'users_sort.email',
                    'desc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'scheduleTitleAsc' =>
            $query
                ->leftJoin(
                    'school_course_schedule_translations as sct_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'school_cohort_enrollments.school_course_schedule_id',
                                '=',
                                'sct_sort.school_course_schedule_id'
                            )
                            ->where(
                                'sct_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_cohort_enrollments.*'
                )
                ->orderBy(
                    'sct_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'scheduleTitleDesc' =>
            $query
                ->leftJoin(
                    'school_course_schedule_translations as sct_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'school_cohort_enrollments.school_course_schedule_id',
                                '=',
                                'sct_sort.school_course_schedule_id'
                            )
                            ->where(
                                'sct_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'school_cohort_enrollments.*'
                )
                ->orderBy(
                    'sct_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'createdAtAsc' =>
            $query
                ->orderBy(
                    'school_cohort_enrollments.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'createdAtDesc' =>
            $query
                ->orderBy(
                    'school_cohort_enrollments.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'updatedAtAsc' =>
            $query
                ->orderBy(
                    'school_cohort_enrollments.updated_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            'updatedAtDesc' =>
            $query
                ->orderBy(
                    'school_cohort_enrollments.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_cohort_enrollments.id'
                ),

            default =>
            $query->orderByDesc(
                'school_cohort_enrollments.id'
            ),
        };
    }

    /**
     * Получение Index-данных
     * по активному processing mode.
     */
    private function getIndexEnrollments(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        ?string $status = null,
        mixed $scheduleId = null,
        ?int $knownTotal = null,
    ): Collection|LengthAwarePaginator {
        $query = $this->indexQuery(
            locale: $locale,
            status: $status,
            scheduleId: $scheduleId,
        );

        /**
         * SERVER
         */
        if ($useServerProcessing) {
            $query = $this->applySearch(
                $query,
                $search,
                $locale
            );

            /**
             * Если поиска нет, используем COUNT,
             * который уже был выполнен перед
             * определением processing mode.
             *
             * Поэтому второго COUNT здесь нет.
             */
            if (
                $search === ''
                && $knownTotal !== null
            ) {
                $total =
                    $knownTotal;
            } else {
                /**
                 * При серверном поиске total должен
                 * отражать уже отфильтрованный набор.
                 *
                 * Поэтому дополнительный COUNT
                 * здесь корректен и необходим.
                 */
                $total =
                    (clone $query)->count();
            }

            $query = $this->applySort(
                $query,
                $sort,
                $locale
            );

            return $this->paginateKnownTotal(
                query: $query,
                perPage: $perPage,
                total: $total,
            );
        }

        /**
         * FRONTEND / AUTO → FRONTEND
         *
         * Backend не выполняет search:
         * Vue получает всю коллекцию.
         */
        return $this->applySort(
            $query,
            $sort,
            $locale
        )->get();
    }

    /**
     * Пагинация с уже известным total.
     *
     * В отличие от paginate()
     * не выполняет дополнительный COUNT(*).
     */
    private function paginateKnownTotal(
        Builder $query,
        int $perPage,
        int $total
    ): LengthAwarePaginator {
        $page = LengthAwarePaginator::resolveCurrentPage(
            'page'
        );

        $items = $query
            ->forPage(
                $page,
                $perPage
            )
            ->get();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' =>
                    LengthAwarePaginator::resolveCurrentPath(),

                'pageName' =>
                    'page',
            ]
        );
    }
}
