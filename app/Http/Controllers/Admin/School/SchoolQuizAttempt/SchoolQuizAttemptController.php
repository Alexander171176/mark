<?php

namespace App\Http\Controllers\Admin\School\SchoolQuizAttempt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\SchoolQuizAttempt\SchoolQuizAttemptRequest;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolEnrollment\SchoolEnrollmentSharedResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use App\Http\Resources\Admin\School\SchoolQuiz\SchoolQuizSharedResource;
use App\Http\Resources\Admin\School\SchoolQuizAttempt\SchoolQuizAttemptResource;
use App\Models\Admin\School\SchoolQuiz\SchoolQuiz;
use App\Models\Admin\School\SchoolQuizAttempt\SchoolQuizAttempt;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolEnrollment\SchoolEnrollment;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
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
use Throwable;

/**
 * Контроллер для управления попытками ответов на викторины
 * (SchoolQuizAttempt) в административной панели.
 * SchoolQuizAttempt — это общая попытка пользователя пройти всю викторину
 *
 * CRUD +:
 * - удаление (одиночное и массовое)
 * - связи с викторинами и вопросами викторин.
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolQuizAttempt
 * @see SchoolQuizAttemptRequest
 *
 */
class SchoolQuizAttemptController extends Controller
{
    /** Список попыток квиза. */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        $status = $request->query('status');
        $userId = $request->query('user_id');
        $quizId = $request->query('school_quiz_id');
        $enrollmentId = $request->query('school_enrollment_id');

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminSchoolQuizAttemptsPerPage', 6);
        $defaultSort = $settings->string('adminSchoolQuizAttemptsDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSchoolQuizAttemptsProcessingMode',
            'frontend'
        );

        $attemptsCount = $this->indexQuery(
            status: $status,
            userId: $userId,
            quizId: $quizId,
            enrollmentId: $enrollmentId,
        )->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer($processingMode, $attemptsCount, 300);

        try {
            $attempts = $this->getIndexAttempts(
                locale: $locale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                status: $status,
                userId: $userId,
                quizId: $quizId,
                enrollmentId: $enrollmentId,
            );

            return Inertia::render('Admin/School/SchoolQuizAttempts/Index', [
                'useServerProcessing' => $useServerProcessing,

                'adminSchoolQuizAttemptsPerPage' => $perPage,
                'adminSchoolQuizAttemptsDefaultSort' => $defaultSort,
                'adminSchoolQuizAttemptsProcessingMode' => $processingMode,

                'attempts' => SchoolQuizAttemptResource::collection($attempts),
                'attemptsCount' => $attemptsCount,

                'sortParam' => $sortParam,
                'search' => $search,

                'filters' => [
                    'status' => $status,
                    'user_id' => $userId,
                    'school_quiz_id' => $quizId,
                    'school_enrollment_id' => $enrollmentId,
                ],

                'users' => $this->usersForSelect(),
                'quizzes' => $this->quizzesForSelect(),
                'enrollments' => $this->enrollmentsForSelect(),
                'courses' => $this->coursesForSelect(),
                'modules' => $this->modulesForSelect(),
                'lessons' => $this->lessonsForSelect(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки school quiz attempts: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SchoolQuizAttempts/Index', [
                'useServerProcessing' => $useServerProcessing,

                'adminSchoolQuizAttemptsPerPage' => $perPage,
                'adminSchoolQuizAttemptsDefaultSort' => $defaultSort,
                'adminSchoolQuizAttemptsProcessingMode' => $processingMode,

                'attempts' => [],
                'attemptsCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'filters' => [
                    'status' => $status,
                    'user_id' => $userId,
                    'school_quiz_id' => $quizId,
                    'school_enrollment_id' => $enrollmentId,
                ],

                'users' => [],
                'quizzes' => [],
                'enrollments' => [],
                'courses' => [],
                'modules' => [],
                'lessons' => [],

                'error' => 'Ошибка загрузки попыток квиза.',
            ]);
        }
    }

    /** Страница создания попытки. */
    public function create(Request $request): Response
    {
        return Inertia::render('Admin/School/SchoolQuizAttempts/Create', [
            'users' => $this->usersForSelect(),
            'quizzes' => $this->quizzesForSelect(),
            'enrollments' => $this->enrollmentsForSelect(),
            'courses' => $this->coursesForSelect(),
            'modules' => $this->modulesForSelect(),
            'lessons' => $this->lessonsForSelect(),

            'defaultUserId' => $request->query('user_id') ? (int) $request->query('user_id') : null,
            'defaultQuizId' => $request->query('school_quiz_id') ? (int) $request->query('school_quiz_id') : null,
            'defaultEnrollmentId' => $request->query('school_enrollment_id') ? (int) $request->query('school_enrollment_id') : null,
        ]);
    }

    /** Сохранение новой попытки. */
    public function store(SchoolQuizAttemptRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $attempt = DB::transaction(function () use ($data) {
                if (empty($data['attempt_number'])) {
                    $maxAttempt = SchoolQuizAttempt::query()
                        ->where('user_id', $data['user_id'])
                        ->where('school_quiz_id', $data['school_quiz_id'])
                        ->lockForUpdate()
                        ->max('attempt_number');

                    $data['attempt_number'] = $maxAttempt ? ((int) $maxAttempt + 1) : 1;
                }

                if (!empty($data['school_enrollment_id'])) {
                    $enrollment = SchoolEnrollment::query()
                        ->findOrFail((int) $data['school_enrollment_id']);

                    if ((int) $enrollment->user_id !== (int) $data['user_id']) {
                        throw new \RuntimeException('Зачисление не принадлежит выбранному пользователю.');
                    }

                    if (empty($data['school_course_id']) && !empty($enrollment->school_course_id)) {
                        $data['school_course_id'] = (int) $enrollment->school_course_id;
                    }
                }

                $attempt = SchoolQuizAttempt::create([
                    'user_id' => $data['user_id'],
                    'school_quiz_id' => $data['school_quiz_id'],

                    'school_enrollment_id' => $data['school_enrollment_id'] ?? null,
                    'school_course_id' => $data['school_course_id'] ?? null,
                    'school_module_id' => $data['school_module_id'] ?? null,
                    'school_lesson_id' => $data['school_lesson_id'] ?? null,

                    'attempt_number' => $data['attempt_number'],

                    'score' => $data['score'] ?? 0,
                    'max_score' => $data['max_score'] ?? 0,
                    'status' => $data['status'] ?? 'in_progress',

                    'started_at' => $data['started_at'] ?? null,
                    'finished_at' => $data['finished_at'] ?? null,
                    'duration_seconds' => $data['duration_seconds'] ?? 0,

                    'ip_address' => $data['ip_address'] ?? null,
                    'user_agent' => $data['user_agent'] ?? null,
                ]);

                if (
                    !$attempt->duration_seconds
                    && $attempt->started_at
                    && $attempt->finished_at
                ) {
                    $attempt->duration_seconds = $attempt->finished_at
                        ->diffInSeconds($attempt->started_at);
                }

                $attempt->recalcPercent();
                $attempt->save();

                return $attempt;
            });

            return redirect()
                ->route('admin.schoolQuizAttempts.index', [
                    'school_quiz_id' => $attempt->school_quiz_id,
                    'user_id' => $attempt->user_id,
                ])
                ->with('success', 'Попытка квиза успешно создана.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school quiz attempt: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании попытки квиза.');
        }
    }

    /** Редирект на страницу редактирования. */
    public function show(int $schoolQuizAttempt): RedirectResponse
    {
        return redirect()->route('admin.schoolQuizAttempts.edit', $schoolQuizAttempt);
    }

    /** Страница редактирования попытки. */
    public function edit(int $schoolQuizAttempt): Response
    {
        $attempt = SchoolQuizAttempt::query()
            ->with([
                'user:id,name,email',
                'quiz.translation',
                'quiz.translations',
                'enrollment.user:id,name,email',
                'enrollment.course.translation',
                'course.translation',
                'course.translations',
                'module.translation',
                'module.translations',
                'lesson.translation',
                'lesson.translations',
                'items',
            ])
            ->withCount(['items'])
            ->findOrFail($schoolQuizAttempt);

        return Inertia::render('Admin/School/SchoolQuizAttempts/Edit', [
            'attempt' => new SchoolQuizAttemptResource($attempt),

            'users' => $this->usersForSelect(),
            'quizzes' => $this->quizzesForSelect(),
            'enrollments' => $this->enrollmentsForSelect(),
            'courses' => $this->coursesForSelect(),
            'modules' => $this->modulesForSelect(),
            'lessons' => $this->lessonsForSelect(),
        ]);
    }

    /** Обновление попытки. */
    public function update(SchoolQuizAttemptRequest $request, int $schoolQuizAttempt): RedirectResponse
    {
        $attempt = SchoolQuizAttempt::query()->findOrFail($schoolQuizAttempt);

        $data = $request->validated();

        try {
            $attempt->fill([
                'status' => $data['status'] ?? $attempt->status,
                'score' => $data['score'] ?? $attempt->score,
                'max_score' => $data['max_score'] ?? $attempt->max_score,
                'started_at' => $data['started_at'] ?? $attempt->started_at,
                'finished_at' => $data['finished_at'] ?? $attempt->finished_at,
                'duration_seconds' => $data['duration_seconds'] ?? $attempt->duration_seconds,
            ]);

            if (
                empty($data['duration_seconds'])
                && $attempt->started_at
                && $attempt->finished_at
            ) {
                $attempt->duration_seconds = $attempt->finished_at
                    ->diffInSeconds($attempt->started_at);
            }

            $attempt->recalcPercent();
            $attempt->save();

            return redirect()
                ->route('admin.schoolQuizAttempts.index', [
                    'school_quiz_id' => $attempt->school_quiz_id,
                    'user_id' => $attempt->user_id,
                ])
                ->with('success', 'Попытка квиза успешно обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school quiz attempt ID ' . $attempt->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении попытки квиза.');
        }
    }

    /** Удаление попытки. */
    public function destroy(int $schoolQuizAttempt): RedirectResponse
    {
        $attempt = SchoolQuizAttempt::query()->findOrFail($schoolQuizAttempt);

        $quizId = $attempt->school_quiz_id;
        $userId = $attempt->user_id;

        try {
            $attempt->delete();

            return redirect()
                ->route('admin.schoolQuizAttempts.index', [
                    'school_quiz_id' => $quizId,
                    'user_id' => $userId,
                ])
                ->with('success', 'Попытка квиза успешно удалена.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school quiz attempt ID ' . $attempt->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении попытки квиза.');
        }
    }

    /** Массовое удаление попыток. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_quiz_attempts,id'],

            'school_quiz_id' => ['nullable', 'integer', 'exists:school_quizzes,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'string'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                SchoolQuizAttempt::query()
                    ->whereIn('id', $data['ids'])
                    ->delete();
            });

            return back()->with('success', 'Выбранные попытки квиза успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка массового удаления school quiz attempts: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении попыток квиза.');
        }
    }

    /** Массовое обновление статуса попыток. */
    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_quiz_attempts,id'],
            'status' => ['required', 'string', 'in:in_progress,completed,graded'],
        ]);

        try {
            SchoolQuizAttempt::query()
                ->whereIn('id', $data['ids'])
                ->update(['status' => $data['status']]);

            return back()->with('success', 'Статус выбранных попыток успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка массового обновления статуса school quiz attempts: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом обновлении статуса попыток.');
        }
    }

    /** Пользователи для select. */
    private function usersForSelect(): Collection|array
    {
        return User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    /** Квизы для select. */
    private function quizzesForSelect(): AnonymousResourceCollection
    {
        $quizzes = SchoolQuiz::query()
            ->with([
                'translation',
                'translations',
                'course.translation',
                'module.translation',
                'lesson.translation',
            ])
            ->orderByDesc('id')
            ->get();

        return SchoolQuizSharedResource::collection($quizzes);
    }

    /** Зачисления для select. */
    private function enrollmentsForSelect(): AnonymousResourceCollection
    {
        $enrollments = SchoolEnrollment::query()
            ->with([
                'user:id,name,email',
                'course.translation',
                'course.translations',
            ])
            ->orderByDesc('id')
            ->get();

        return SchoolEnrollmentSharedResource::collection($enrollments);
    }

    /** Курсы для select. */
    private function coursesForSelect(): AnonymousResourceCollection
    {
        $courses = SchoolCourse::query()
            ->with(['translation', 'translations'])
            ->get();

        return SchoolCourseSharedResource::collection($courses);
    }

    /** Модули для select. */
    private function modulesForSelect(): AnonymousResourceCollection
    {
        $modules = SchoolModule::query()
            ->with([
                'translation',
                'translations',
                'course.translation',
            ])
            ->get();

        return SchoolModuleSharedResource::collection($modules);
    }

    /** Уроки для select. */
    private function lessonsForSelect(): AnonymousResourceCollection
    {
        $lessons = SchoolLesson::query()
            ->with([
                'translation',
                'translations',
                'module.translation',
                'module.course.translation',
            ])
            ->get();

        return SchoolLessonSharedResource::collection($lessons);
    }

    /** Базовый запрос для списка попыток квиза. */
    private function indexQuery(
        null|string|int $status = null,
        null|string|int $userId = null,
        null|string|int $quizId = null,
        null|string|int $enrollmentId = null,
    ): Builder {
        return SchoolQuizAttempt::query()
            ->when($status, fn (Builder $query) => $query
                ->where('status', $status)
            )
            ->when($userId, fn (Builder $query) => $query
                ->where('user_id', (int) $userId)
            )
            ->when($quizId, fn (Builder $query) => $query
                ->where('school_quiz_id', (int) $quizId)
            )
            ->when($enrollmentId, fn (Builder $query) => $query
                ->where('school_enrollment_id', (int) $enrollmentId)
            )
            ->with([
                'user:id,name,email',

                'quiz.translation',
                'quiz.translations',

                'enrollment.user:id,name,email',
                'enrollment.course.translation',
                'enrollment.course.translations',

                'course.translation',
                'course.translations',

                'module.translation',
                'module.translations',

                'lesson.translation',
                'lesson.translations',
            ])
            ->withCount([
                'items',
            ]);
    }

    /** Получение списка попыток по активному режиму обработки. */
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
            status: $status,
            userId: $userId,
            quizId: $quizId,
            enrollmentId: $enrollmentId,
        );

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
