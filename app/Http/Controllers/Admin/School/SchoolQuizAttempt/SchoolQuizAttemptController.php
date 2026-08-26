<?php

namespace App\Http\Controllers\Admin\School\SchoolQuizAttempt;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolQuizAttempt\SchoolQuizAttemptRequest;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolEnrollment\SchoolEnrollmentSharedResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use App\Http\Resources\Admin\School\SchoolQuiz\SchoolQuizSharedResource;
use App\Http\Resources\Admin\School\SchoolQuizAttempt\SchoolQuizAttemptResource;
use App\Http\Resources\Admin\School\SchoolQuizAttempt\SchoolQuizAttemptSharedResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolEnrollment\SchoolEnrollment;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\Admin\School\SchoolQuiz\SchoolQuiz;
use App\Models\Admin\School\SchoolQuizAttempt\SchoolQuizAttempt;
use App\Models\User;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use Throwable;

/**
 * Контроллер для управления попытками ответов на викторины
 * (SchoolQuizAttempt) в административной панели.
 *
 * SchoolQuizAttempt — общая попытка пользователя
 * пройти викторину.
 *
 * CRUD +:
 * - удаление (одиночное и массовое);
 * - массовое изменение статуса;
 * - связи с пользователем, квизом, зачислением,
 *   курсом, модулем и уроком.
 *
 * @version 1.1
 */
class SchoolQuizAttemptController extends BaseSchoolAdminController
{
    /**
     * =========================================================
     * INDEX
     * =========================================================
     */

    /** Список попыток квиза. */
    public function index(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $status =
            $request->query('status');

        $userId =
            $request->query('user_id');

        $quizId =
            $request->query(
                'school_quiz_id'
            );

        $enrollmentId =
            $request->query(
                'school_enrollment_id'
            );

        $settings = app(
            AdminSettingsService::class
        );

        $perPage = $settings->int(
            'adminSchoolQuizAttemptsPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolQuizAttemptsDefaultSort',
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
            'adminSchoolQuizAttemptsProcessingMode',
            'frontend'
        );

        /**
         * Count выполняем отдельным лёгким запросом.
         *
         * Eager loading Index здесь не нужен.
         */
        $attemptsCount = $this->countQuery(
            status: $status,
            userId: $userId,
            quizId: $quizId,
            enrollmentId: $enrollmentId,
        )->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $attemptsCount,
            300
        );

        try {
            $attempts = $this->getIndexAttempts(
                locale: $currentLocale,
                useServerProcessing:
                $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                status: $status,
                userId: $userId,
                quizId: $quizId,
                enrollmentId: $enrollmentId,
            );

            return Inertia::render(
                'Admin/School/SchoolQuizAttempts/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolQuizAttemptsPerPage' =>
                        $perPage,

                    'adminSchoolQuizAttemptsDefaultSort' =>
                        $defaultSort,

                    'adminSchoolQuizAttemptsProcessingMode' =>
                        $processingMode,

                    /**
                     * Admin Index использует
                     * краткий Shared Resource.
                     */
                    'attempts' =>
                        SchoolQuizAttemptSharedResource::collection(
                            $attempts
                        ),

                    'attemptsCount' =>
                        $attemptsCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' => [
                        'status' =>
                            $status,

                        'user_id' =>
                            $userId,

                        'school_quiz_id' =>
                            $quizId,

                        'school_enrollment_id' =>
                            $enrollmentId,
                    ],

                    /**
                     * Все select-helper имеют
                     * единый locale-контракт.
                     */
                    'users' => $this->usersForSelect(),

                    'quizzes' =>
                        $this->quizzesForSelect(
                            $currentLocale
                        ),

                    'enrollments' =>
                        $this->enrollmentsForSelect(
                            $currentLocale
                        ),

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
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки school quiz attempts: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolQuizAttempts/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolQuizAttemptsPerPage' =>
                        $perPage,

                    'adminSchoolQuizAttemptsDefaultSort' =>
                        $defaultSort,

                    'adminSchoolQuizAttemptsProcessingMode' =>
                        $processingMode,

                    'attempts' =>
                        [],

                    'attemptsCount' =>
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

                        'school_quiz_id' =>
                            $quizId,

                        'school_enrollment_id' =>
                            $enrollmentId,
                    ],

                    'users' =>
                        [],

                    'quizzes' =>
                        [],

                    'enrollments' =>
                        [],

                    'courses' =>
                        [],

                    'modules' =>
                        [],

                    'lessons' =>
                        [],

                    'error' =>
                        'Ошибка загрузки попыток квиза.',
                ]
            );
        }
    }

    /**
     * =========================================================
     * CREATE / STORE
     * =========================================================
     */

    /** Страница создания попытки. */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/School/SchoolQuizAttempts/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'users' => $this->usersForSelect(),

                'quizzes' =>
                    $this->quizzesForSelect(
                        $currentLocale
                    ),

                'enrollments' =>
                    $this->enrollmentsForSelect(
                        $currentLocale
                    ),

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

                'defaultUserId' =>
                    $request->query(
                        'user_id'
                    )
                        ? (int) $request->query(
                        'user_id'
                    )
                        : null,

                'defaultQuizId' =>
                    $request->query(
                        'school_quiz_id'
                    )
                        ? (int) $request->query(
                        'school_quiz_id'
                    )
                        : null,

                'defaultEnrollmentId' =>
                    $request->query(
                        'school_enrollment_id'
                    )
                        ? (int) $request->query(
                        'school_enrollment_id'
                    )
                        : null,
            ]
        );
    }

    /** Сохранение новой попытки. */
    public function store(
        SchoolQuizAttemptRequest $request
    ): RedirectResponse {
        $data =
            $request->validated();

        try {
            $attempt = DB::transaction(
                function () use ($data) {
                    /**
                     * Если номер попытки
                     * не передан вручную —
                     * определяем следующий.
                     */
                    if (
                        empty(
                        $data['attempt_number']
                        )
                    ) {
                        $maxAttempt =
                            SchoolQuizAttempt::query()
                                ->where(
                                    'user_id',
                                    $data['user_id']
                                )
                                ->where(
                                    'school_quiz_id',
                                    $data['school_quiz_id']
                                )
                                ->lockForUpdate()
                                ->max(
                                    'attempt_number'
                                );

                        $data['attempt_number'] =
                            $maxAttempt
                                ? (
                                (int) $maxAttempt
                                + 1
                            )
                                : 1;
                    }

                    /**
                     * Проверяем зачисление.
                     */
                    if (
                        !empty(
                        $data[
                        'school_enrollment_id'
                        ]
                        )
                    ) {
                        $enrollment =
                            SchoolEnrollment::query()
                                ->findOrFail(
                                    (int) $data[
                                    'school_enrollment_id'
                                    ]
                                );

                        if (
                            (int) $enrollment->user_id
                            !== (int) $data['user_id']
                        ) {
                            throw new RuntimeException(
                                'Зачисление не принадлежит выбранному пользователю.'
                            );
                        }

                        /**
                         * Если курс явно не передан,
                         * берём его из Enrollment.
                         */
                        if (
                            empty(
                            $data[
                            'school_course_id'
                            ]
                            )
                            && !empty(
                            $enrollment
                                ->school_course_id
                            )
                        ) {
                            $data[
                            'school_course_id'
                            ] = (int) $enrollment
                                ->school_course_id;
                        }
                    }

                    $attempt =
                        SchoolQuizAttempt::create([
                            'user_id' =>
                                $data['user_id'],

                            'school_quiz_id' =>
                                $data[
                                'school_quiz_id'
                                ],

                            'school_enrollment_id' =>
                                $data[
                                'school_enrollment_id'
                                ] ?? null,

                            'school_course_id' =>
                                $data[
                                'school_course_id'
                                ] ?? null,

                            'school_module_id' =>
                                $data[
                                'school_module_id'
                                ] ?? null,

                            'school_lesson_id' =>
                                $data[
                                'school_lesson_id'
                                ] ?? null,

                            'attempt_number' =>
                                $data[
                                'attempt_number'
                                ],

                            'score' =>
                                $data['score']
                                ?? 0,

                            'max_score' =>
                                $data['max_score']
                                ?? 0,

                            'status' =>
                                $data['status']
                                ?? 'in_progress',

                            'started_at' =>
                                $data['started_at']
                                ?? null,

                            'finished_at' =>
                                $data['finished_at']
                                ?? null,

                            'duration_seconds' =>
                                $data[
                                'duration_seconds'
                                ] ?? 0,

                            'ip_address' =>
                                $data['ip_address']
                                ?? null,

                            'user_agent' =>
                                $data['user_agent']
                                ?? null,
                        ]);

                    /**
                     * Автоматически рассчитываем
                     * длительность.
                     */
                    if (
                        !$attempt->duration_seconds
                        && $attempt->started_at
                        && $attempt->finished_at
                    ) {
                        $attempt->duration_seconds =
                            $attempt->finished_at
                                ->diffInSeconds(
                                    $attempt->started_at
                                );
                    }

                    $attempt->recalcPercent();

                    $attempt->save();

                    return $attempt;
                }
            );

            return redirect()
                ->route(
                    'admin.schoolQuizAttempts.index',
                    [
                        'school_quiz_id' =>
                            $attempt->school_quiz_id,

                        'user_id' =>
                            $attempt->user_id,
                    ]
                )
                ->with(
                    'success',
                    'Попытка квиза успешно создана.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка создания school quiz attempt: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,

                    'payload' =>
                        $data,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при создании попытки квиза.'
                );
        }
    }

    /**
     * =========================================================
     * SHOW / EDIT / UPDATE
     * =========================================================
     */

    /** Редирект на страницу редактирования. */
    public function show(
        int $schoolQuizAttempt
    ): RedirectResponse {
        return redirect()->route(
            'admin.schoolQuizAttempts.edit',
            $schoolQuizAttempt
        );
    }

    /** Страница редактирования попытки. */
    public function edit(
        int $schoolQuizAttempt,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $attempt = SchoolQuizAttempt::query()
            ->with([
                'user:id,name,email',

                /**
                 * Quiz:
                 * только текущая locale.
                 */
                'quiz' => fn ($query) =>
                $query->with([
                    'translations' =>
                        fn ($translationQuery) =>
                        $translationQuery
                            ->where(
                                'locale',
                                $currentLocale
                            ),
                ]),

                /**
                 * Enrollment +
                 * пользователь и курс.
                 */
                'enrollment' => fn ($query) =>
                $query->with([
                    'user:id,name,email',

                    'course' =>
                        fn ($courseQuery) =>
                        $courseQuery->with([
                            'translations' =>
                                fn (
                                    $translationQuery
                                ) =>
                                $translationQuery
                                    ->where(
                                        'locale',
                                        $currentLocale
                                    ),
                        ]),
                ]),

                /**
                 * Course.
                 */
                'course' => fn ($query) =>
                $query->with([
                    'translations' =>
                        fn ($translationQuery) =>
                        $translationQuery
                            ->where(
                                'locale',
                                $currentLocale
                            ),
                ]),

                /**
                 * Module.
                 */
                'module' => fn ($query) =>
                $query->with([
                    'translations' =>
                        fn ($translationQuery) =>
                        $translationQuery
                            ->where(
                                'locale',
                                $currentLocale
                            ),
                ]),

                /**
                 * Lesson.
                 */
                'lesson' => fn ($query) =>
                $query->with([
                    'translations' =>
                        fn ($translationQuery) =>
                        $translationQuery
                            ->where(
                                'locale',
                                $currentLocale
                            ),
                ]),

                /**
                 * Full Resource:
                 * элементы попытки нужны Edit.
                 */
                'items',
            ])
            ->withCount([
                'items',
            ])
            ->findOrFail(
                $schoolQuizAttempt
            );

        return Inertia::render(
            'Admin/School/SchoolQuizAttempts/Edit',
            [
                'attempt' =>
                    new SchoolQuizAttemptResource(
                        $attempt
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'users' =>
                    $this->usersForSelect(),

                'quizzes' =>
                    $this->quizzesForSelect(
                        $currentLocale
                    ),

                'enrollments' =>
                    $this->enrollmentsForSelect(
                        $currentLocale
                    ),

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
            ]
        );
    }

    /** Обновление попытки. */
    public function update(
        SchoolQuizAttemptRequest $request,
        int $schoolQuizAttempt
    ): RedirectResponse {
        $attempt =
            SchoolQuizAttempt::query()
                ->findOrFail(
                    $schoolQuizAttempt
                );

        $data =
            $request->validated();

        try {
            /**
             * Сохраняем существующую
             * бизнес-логику:
             *
             * связи попытки здесь
             * не изменяются.
             */
            $attempt->fill([
                'status' =>
                    $data['status']
                    ?? $attempt->status,

                'score' =>
                    $data['score']
                    ?? $attempt->score,

                'max_score' =>
                    $data['max_score']
                    ?? $attempt->max_score,

                'started_at' =>
                    $data['started_at']
                    ?? $attempt->started_at,

                'finished_at' =>
                    $data['finished_at']
                    ?? $attempt->finished_at,

                'duration_seconds' =>
                    $data['duration_seconds']
                    ?? $attempt->duration_seconds,
            ]);

            if (
                empty(
                $data['duration_seconds']
                )
                && $attempt->started_at
                && $attempt->finished_at
            ) {
                $attempt->duration_seconds =
                    $attempt->finished_at
                        ->diffInSeconds(
                            $attempt->started_at
                        );
            }

            $attempt->recalcPercent();

            $attempt->save();

            return redirect()
                ->route(
                    'admin.schoolQuizAttempts.index',
                    [
                        'school_quiz_id' =>
                            $attempt->school_quiz_id,

                        'user_id' =>
                            $attempt->user_id,
                    ]
                )
                ->with(
                    'success',
                    'Попытка квиза успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления school quiz attempt ID '
                . $attempt->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ошибка при обновлении попытки квиза.'
                );
        }
    }

    /**
     * =========================================================
     * DELETE
     * =========================================================
     */

    /** Удаление попытки. */
    public function destroy(
        int $schoolQuizAttempt
    ): RedirectResponse {
        $attempt =
            SchoolQuizAttempt::query()
                ->findOrFail(
                    $schoolQuizAttempt
                );

        $quizId =
            $attempt->school_quiz_id;

        $userId =
            $attempt->user_id;

        try {
            $attempt->delete();

            return redirect()
                ->route(
                    'admin.schoolQuizAttempts.index',
                    [
                        'school_quiz_id' =>
                            $quizId,

                        'user_id' =>
                            $userId,
                    ]
                )
                ->with(
                    'success',
                    'Попытка квиза успешно удалена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления school quiz attempt ID '
                . $attempt->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении попытки квиза.'
            );
        }
    }

    /** Массовое удаление попыток. */
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
                'exists:school_quiz_attempts,id',
            ],

            'school_quiz_id' => [
                'nullable',
                'integer',
                'exists:school_quizzes,id',
            ],

            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'status' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            DB::transaction(
                function () use ($data) {
                    SchoolQuizAttempt::query()
                        ->whereIn(
                            'id',
                            $data['ids']
                        )
                        ->delete();
                }
            );

            return back()->with(
                'success',
                'Выбранные попытки квиза успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового удаления school quiz attempts: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении попыток квиза.'
            );
        }
    }

    /** Массовое обновление статуса попыток. */
    public function bulkUpdateStatus(
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
                'exists:school_quiz_attempts,id',
            ],

            'status' => [
                'required',
                'string',
                'in:in_progress,completed,graded',
            ],
        ]);

        try {
            SchoolQuizAttempt::query()
                ->whereIn(
                    'id',
                    $data['ids']
                )
                ->update([
                    'status' =>
                        $data['status'],
                ]);

            return back()->with(
                'success',
                'Статус выбранных попыток успешно обновлён.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового обновления статуса school quiz attempts: '
                . $e->getMessage(),
                [
                    'exception' =>
                        $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом обновлении статуса попыток.'
            );
        }
    }

    /**
     * =========================================================
     * SELECT HELPERS
     * =========================================================
     */

    /**
     * Пользователи для select.
     *
     * User не является переводимой сущностью.
     * $locale принимается для единого контракта
     * всех select-helper контроллера.
     */
    private function usersForSelect(): Collection {
        return User::query()
            ->select([
                'id',
                'name',
                'email',
            ])
            ->orderBy('name')
            ->get();
    }

    /**
     * Квизы для select.
     *
     * Только выбранная locale
     * самого Quiz и его иерархии.
     */
    private function quizzesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $quizzes = SchoolQuiz::query()
            ->with([
                'translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),

                'course' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn (
                                $translationQuery
                            ) =>
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                ),
                    ]),

                'module' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn (
                                $translationQuery
                            ) =>
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                ),
                    ]),

                'lesson' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn (
                                $translationQuery
                            ) =>
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                ),
                    ]),
            ])
            ->orderByDesc('id')
            ->get();

        return SchoolQuizSharedResource::collection(
            $quizzes
        );
    }

    /**
     * Зачисления для select.
     *
     * Enrollment сам не переводимый,
     * но его Course — переводимый.
     */
    private function enrollmentsForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $enrollments =
            SchoolEnrollment::query()
                ->with([
                    'user:id,name,email',

                    'course' =>
                        fn ($query) =>
                        $query->with([
                            'translations' =>
                                fn (
                                    $translationQuery
                                ) =>
                                $translationQuery
                                    ->where(
                                        'locale',
                                        $locale
                                    ),
                        ]),
                ])
                ->orderByDesc('id')
                ->get();

        return SchoolEnrollmentSharedResource::collection(
            $enrollments
        );
    }

    /**
     * Курсы для select.
     */
    private function coursesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $courses = SchoolCourse::query()
            ->with([
                'translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return SchoolCourseSharedResource::collection(
            $courses
        );
    }

    /**
     * Модули для select.
     */
    private function modulesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $modules = SchoolModule::query()
            ->with([
                /**
                 * Module:
                 * текущая locale.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),

                /**
                 * Родительский Course:
                 * текущая locale.
                 */
                'course' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn (
                                $translationQuery
                            ) =>
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                ),
                    ]),
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return SchoolModuleSharedResource::collection(
            $modules
        );
    }

    /**
     * Уроки для select.
     */
    private function lessonsForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $lessons = SchoolLesson::query()
            ->with([
                /**
                 * Lesson:
                 * текущая locale.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->where(
                        'locale',
                        $locale
                    ),

                /**
                 * Module +
                 * его Course.
                 */
                'module' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn (
                                $translationQuery
                            ) =>
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                ),

                        'course' =>
                            fn (
                                $courseQuery
                            ) =>
                            $courseQuery->with([
                                'translations' =>
                                    fn (
                                        $translationQuery
                                    ) =>
                                    $translationQuery
                                        ->where(
                                            'locale',
                                            $locale
                                        ),
                            ]),
                    ]),
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return SchoolLessonSharedResource::collection(
            $lessons
        );
    }

    /**
     * =========================================================
     * INDEX QUERIES
     * =========================================================
     */

    /**
     * Лёгкий query для подсчёта количества
     * попыток с активными фильтрами.
     */
    private function countQuery(
        null|string|int $status = null,
        null|string|int $userId = null,
        null|string|int $quizId = null,
        null|string|int $enrollmentId = null,
    ): Builder {
        return SchoolQuizAttempt::query()
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
                $quizId,
                fn (Builder $query) =>
                $query->where(
                    'school_quiz_id',
                    (int) $quizId
                )
            )
            ->when(
                $enrollmentId,
                fn (Builder $query) =>
                $query->where(
                    'school_enrollment_id',
                    (int) $enrollmentId
                )
            );
    }

    /**
     * Базовый запрос для Admin Index
     * попыток квиза.
     */
    private function indexQuery(
        string $locale,
        null|string|int $status = null,
        null|string|int $userId = null,
        null|string|int $quizId = null,
        null|string|int $enrollmentId = null,
    ): Builder {
        return $this->countQuery(
            status: $status,
            userId: $userId,
            quizId: $quizId,
            enrollmentId: $enrollmentId,
        )
            ->with([
                /**
                 * Пользователь.
                 */
                'user:id,name,email',

                /**
                 * Quiz:
                 * только текущая locale.
                 */
                'quiz' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn (
                                $translationQuery
                            ) =>
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                ),
                    ]),

                /**
                 * Enrollment:
                 * пользователь +
                 * Course текущей locale.
                 */
                'enrollment' =>
                    fn ($query) =>
                    $query->with([
                        'user:id,name,email',

                        'course' =>
                            fn (
                                $courseQuery
                            ) =>
                            $courseQuery->with([
                                'translations' =>
                                    fn (
                                        $translationQuery
                                    ) =>
                                    $translationQuery
                                        ->where(
                                            'locale',
                                            $locale
                                        ),
                            ]),
                    ]),

                /**
                 * Course.
                 */
                'course' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn (
                                $translationQuery
                            ) =>
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                ),
                    ]),

                /**
                 * Module.
                 */
                'module' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn (
                                $translationQuery
                            ) =>
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                ),
                    ]),

                /**
                 * Lesson.
                 */
                'lesson' =>
                    fn ($query) =>
                    $query->with([
                        'translations' =>
                            fn (
                                $translationQuery
                            ) =>
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                ),
                    ]),
            ])
            ->withCount([
                'items',
            ]);
    }

    /**
     * Получение списка попыток
     * по активному режиму обработки.
     */
    private function getIndexAttempts(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        null|string|int $status = null,
        null|string|int $userId = null,
        null|string|int $quizId = null,
        null|string|int $enrollmentId = null,
    ) {
        $query = $this->indexQuery(
            locale: $locale,
            status: $status,
            userId: $userId,
            quizId: $quizId,
            enrollmentId: $enrollmentId,
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
