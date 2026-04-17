<?php

namespace App\Http\Controllers\Admin\School\QuizAttempt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\QuizAttempt\QuizAttemptRequest;
use App\Http\Resources\Admin\School\Quiz\QuizResource;
use App\Http\Resources\Admin\School\QuizAttempt\QuizAttemptResource;
use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Enrollment\Enrollment;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\QuizAttempt\QuizAttempt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Админ-контроллер для управления попытками прохождения квизов (QuizAttempt).
 *
 * Соответствует ресурсным маршрутам:
 *  - admin.quizAttempts.* (Route::resource('/quiz-attempts', ...))
 *
 * Доп. экшены:
 *  - actions.quizAttempts.bulkUpdateStatus
 *  - actions.quizAttempts.bulkDestroy
 */
class QuizAttemptController extends Controller
{
    /**
     * Разрешённые локали (как в Quiz / QuizQuestion / QuizAnswer).
     *
     * @var array<string>
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Список попыток
     *
     * GET /admin/quiz-attempts
     */
    public function index(): Response
    {
        $adminCountQuizAttempts = (int) config('site_settings.AdminCountQuizAttempts', 20);
        $adminSortQuizAttempts  = (string) config('site_settings.AdminSortQuizAttempts', 'idDesc');

        $attempts = collect();
        $attemptsCount = 0;

        try {
            $attempts = QuizAttempt::query()
                ->with([
                    'quiz:id,title,slug,locale',
                    'user:id,name,email',
                    'course:id,title,locale',
                    'module:id,title,locale',
                    'lesson:id,title,locale',
                ])
                ->orderByDesc('id') // базовый порядок, дальше сортируешь на фронте по adminSortAttempts/sortParam
                ->get();

            $attemptsCount = $attempts->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки попыток квиза: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/QuizAttempts/Index', [
            'attempts'           => QuizAttemptResource::collection($attempts)->resolve(),
            'attemptsCount'      => $attemptsCount,
            'adminCountAttempts' => $adminCountQuizAttempts,
            'adminSortAttempts'  => $adminSortQuizAttempts,
        ]);
    }

    /**
     * Форма создания попытки.
     * (Если в админке реально нужно создавать попытки вручную.)
     *
     * GET /admin/quiz-attempts/create
     */
    public function create(Request $request): Response
    {
        $quizId = $request->query('quiz_id');
        $userId = $request->query('user_id');

        // quizzes: обязательно включаем контекст + (опционально) короткие связи
        $quizzes = Quiz::query()
            ->select([
                'id',
                'title',
                'slug',
                'locale',
                'course_id',
                'module_id',
                'lesson_id',
            ])
            ->with([
                'course:id,title,slug,locale',
                'module:id,title,slug,locale',
                'lesson:id,title,slug,locale',
            ])
            ->orderBy('locale', 'desc')
            ->get();

        // users
        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->orderByDesc('id')
            ->get();

        // enrollments (для фильтра на фронте)
        $enrollments = Enrollment::query()
            ->select(['id', 'user_id', 'course_id'])
            ->orderByDesc('id')
            ->get();

        // courses/modules/lessons (для выбора вручную)
        $courses = Course::query()
            ->select(['id', 'title', 'locale'])
            ->orderByDesc('id')
            ->get();

        $modules = Module::query()
            ->select(['id', 'title', 'course_id', 'locale'])
            ->orderByDesc('id')
            ->get();

        $lessons = Lesson::query()
            ->select(['id', 'title', 'module_id', 'locale'])
            ->orderByDesc('id')
            ->get();

        // дефолты (если не существует — null)
        $defaultQuizId = null;
        if ($quizId) {
            $defaultQuizId = $quizzes->contains('id', (int) $quizId) ? (int) $quizId : null;
        }

        $defaultUserId = null;
        if ($userId) {
            $defaultUserId = $users->contains('id', (int) $userId) ? (int) $userId : null;
        }

        return Inertia::render('Admin/School/QuizAttempts/Create', [
            'quizzes'       => QuizResource::collection($quizzes)->resolve(),
            'users'         => $users,

            'enrollments'   => $enrollments,
            'courses'       => $courses,
            'modules'       => $modules,
            'lessons'       => $lessons,

            'defaultQuizId' => $defaultQuizId,
            'defaultUserId' => $defaultUserId,
        ]);
    }

    /**
     * Сохранение новой попытки.
     *
     * POST /admin/quiz-attempts
     */
    public function store(QuizAttemptRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $attempt = DB::transaction(function () use ($data) {

                // 1) attempt_number: следующий для (user, quiz)
                if (!isset($data['attempt_number']) || $data['attempt_number'] === null) {
                    $maxAttempt = QuizAttempt::query()
                        ->where('user_id', $data['user_id'])
                        ->where('quiz_id', $data['quiz_id'])
                        ->lockForUpdate()
                        ->max('attempt_number');

                    $data['attempt_number'] = $maxAttempt ? ((int) $maxAttempt + 1) : 1;
                }

                // 2) enrollment проверяем и подставляем course_id если нужно
                if (!empty($data['enrollment_id'])) {
                    $enrollment = Enrollment::query()
                        ->select(['id','user_id','course_id'])
                        ->findOrFail((int) $data['enrollment_id']);

                    // enrollment должен принадлежать выбранному user
                    if ((int)$enrollment->user_id !== (int)$data['user_id']) {
                        throw new \RuntimeException('Enrollment не принадлежит выбранному пользователю.');
                    }

                    // если course_id не задан — берём из enrollment
                    if (empty($data['course_id']) && !empty($enrollment->course_id)) {
                        $data['course_id'] = (int) $enrollment->course_id;
                    }
                }

                /** @var QuizAttempt $attempt */
                $attempt = QuizAttempt::create([
                    'user_id'          => $data['user_id'],
                    'quiz_id'          => $data['quiz_id'],

                    'enrollment_id'    => $data['enrollment_id'] ?? null,
                    'course_id'        => $data['course_id'] ?? null,
                    'module_id'        => $data['module_id'] ?? null,
                    'lesson_id'        => $data['lesson_id'] ?? null,

                    'attempt_number'   => $data['attempt_number'],

                    'score'            => $data['score'] ?? 0,
                    'max_score'        => $data['max_score'] ?? 0,

                    'status'           => $data['status'] ?? 'in_progress',
                    'started_at'       => $data['started_at'] ?? null,
                    'finished_at'      => $data['finished_at'] ?? null,
                    'duration_seconds' => $data['duration_seconds'] ?? null,

                    // аудитные поля в админке НЕ задаём руками
                    'ip_address'       => null,
                    'user_agent'       => null,
                ]);

                // 3) duration_seconds если не задан, но обе даты есть
                if (
                    ($attempt->duration_seconds === null)
                    && $attempt->started_at
                    && $attempt->finished_at
                ) {
                    $diff = $attempt->finished_at->diffInSeconds($attempt->started_at, false);
                    $attempt->duration_seconds = $diff >= 0 ? $diff : 0;
                }

                // 4) percent считаем всегда на бэке
                $attempt->recalcPercent();
                $attempt->save();

                return $attempt;
            });

            Log::info('Попытка квиза создана (admin)', [
                'attempt_id'     => $attempt->id,
                'quiz_id'        => $attempt->quiz_id,
                'user_id'        => $attempt->user_id,
                'attempt_number' => $attempt->attempt_number,
            ]);

            return redirect()
                ->route('admin.quizAttempts.index', [
                    'quiz_id' => $attempt->quiz_id,
                    'user_id' => $attempt->user_id,
                ])
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при создании попытки квиза (admin): " . $e->getMessage(), [
                'exception' => $e,
                'payload'   => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * show в админке — редирект на edit.
     *
     * GET /admin/quiz-attempts/{quizAttempt}
     */
    public function show(QuizAttempt $quizAttempt): RedirectResponse
    {
        return redirect()->route('admin.quizAttempts.edit', $quizAttempt);
    }

    /**
     * Форма редактирования попытки.
     *
     * GET /admin/quiz-attempts/{quizAttempt}/edit
     */
    public function edit(QuizAttempt $quizAttempt): Response
    {
        $quizAttempt->load([
            'quiz:id,title,slug,locale',
            'user:id,name,email',
            'course:id,title,locale',
            'module:id,title,locale',
            'lesson:id,title,locale',
            'enrollment:id', // если нужно
        ]);

        $quizzes = Quiz::query()->orderBy('locale', 'desc')->orderByDesc('id')->get();

        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->orderByDesc('id')
            ->get();

        $enrollments = Enrollment::query()
            ->select(['id','user_id','course_id'])
            ->orderByDesc('id')
            ->get();

        $courses = Course::query()
            ->select(['id','title','locale'])
            ->orderBy('locale', 'desc')
            ->orderByDesc('id')
            ->get();

        $modules = Module::query()
            ->select(['id','title','course_id','locale'])
            ->orderBy('locale', 'desc')
            ->orderByDesc('id')
            ->get();

        $lessons = Lesson::query()
            ->select(['id','title','module_id','locale'])
            ->orderBy('locale', 'desc')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Admin/School/QuizAttempts/Edit', [
            'attempt'     => new QuizAttemptResource($quizAttempt),
            'quizzes'     => QuizResource::collection($quizzes)->resolve(),
            'users'       => $users,

            'enrollments' => $enrollments,
            'courses'     => $courses,
            'modules'     => $modules,
            'lessons'     => $lessons,
        ]);
    }

    /**
     * Обновление попытки.
     *
     * PUT/PATCH /admin/quiz-attempts/{quizAttempt}
     */
    public function update(QuizAttemptRequest $request, QuizAttempt $quizAttempt): RedirectResponse
    {
        $validated = $request->validated();

        // ✅ Жёсткий whitelist (как форма)
        $data = collect($validated)->only([
            'status',
            'score',
            'max_score',
            'started_at',
            'finished_at',
            'duration_seconds',
        ])->toArray();

        try {
            $quizAttempt->fill($data);

            // ✅ duration_seconds (опционально): если пришли обе даты, а duration не передали — считаем
            if (
                (!array_key_exists('duration_seconds', $data) || $data['duration_seconds'] === null)
                && $quizAttempt->started_at
                && $quizAttempt->finished_at
            ) {
                $diff = $quizAttempt->finished_at->diffInSeconds($quizAttempt->started_at, false);
                $quizAttempt->duration_seconds = $diff >= 0 ? $diff : 0;
            }

            // ✅ percent всегда считаем на бэке (int 0..100)
            $quizAttempt->recalcPercent();

            $quizAttempt->save();

            Log::info('Попытка квиза обновлена', [
                'attempt_id' => $quizAttempt->id,
                'quiz_id'    => $quizAttempt->quiz_id,
                'user_id'    => $quizAttempt->user_id,
            ]);

            return redirect()
                ->route('admin.quizAttempts.index', [
                    'quiz_id' => $quizAttempt->quiz_id,
                    'user_id' => $quizAttempt->user_id,
                ])
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении попытки квиза ID {$quizAttempt->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => __('admin/controllers.updated_error'),
                ]);
        }
    }

    /**
     * Удаление одной попытки.
     *
     * DELETE /admin/quiz-attempts/{quizAttempt}
     */
    public function destroy(QuizAttempt $quizAttempt): RedirectResponse
    {
        $quizId = $quizAttempt->quiz_id;
        $userId = $quizAttempt->user_id;

        try {
            $quizAttempt->delete();

            Log::info('Попытка квиза удалена', [
                'attempt_id' => $quizAttempt->id,
                'quiz_id'    => $quizId,
                'user_id'    => $userId,
            ]);

            return redirect()
                ->route('admin.quizAttempts.index', [
                    ...( $quizId ? ['quiz_id' => $quizId] : [] ),
                    ...( $userId ? ['user_id' => $userId] : [] ),
                ])
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при удалении попытки квиза ID {$quizAttempt->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Массовое удаление попыток.
     *
     * DELETE /admin/actions/quiz-attempts/bulk-delete
     *
     * Ожидает:
     *  - ids: [1,2,3,...]
     *  + опционально фильтры для редиректа:
     *  - quiz_id
     *  - user_id
     *  - status
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:quiz_attempts,id'],

            'quiz_id' => ['nullable', 'integer', 'exists:quizzes,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'status'  => ['nullable', 'string'],
        ]);

        $ids    = $validated['ids'];
        $quizId = $validated['quiz_id'] ?? null;
        $userId = $validated['user_id'] ?? null;
        $status = $validated['status'] ?? null;
        $count  = count($ids);

        try {
            DB::beginTransaction();

            QuizAttempt::whereIn('id', $ids)->delete();

            DB::commit();

            Log::info('Массовое удаление попыток квиза', [
                'quiz_id' => $quizId,
                'user_id' => $userId,
                'status'  => $status,
                'ids'     => $ids,
            ]);

            return redirect()
                ->route('admin.quizAttempts.index', [
                    ...( $quizId ? ['quiz_id' => $quizId] : [] ),
                    ...( $userId ? ['user_id' => $userId] : [] ),
                    ...( $status ? ['status' => $status] : [] ),
                ])
                ->with('success', __('admin/controllers.bulk_deleted_success', [
                    'count' => $count,
                ]));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при массовом удалении попыток квиза: " . $e->getMessage(), [
                'ids'       => $ids,
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }

    /**
     * Массовое обновление статуса попыток.
     *
     * PUT /admin/actions/quiz-attempts/bulk-status
     *
     * Ожидает:
     *  - ids: [1,2,3,...]
     *  - status: in_progress|completed|graded
     *  + опционально фильтры для редиректа:
     *  - quiz_id
     *  - user_id
     */
    public function bulkUpdateStatus(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => ['required', 'array'],
            'ids.*'    => ['integer', 'exists:quiz_attempts,id'],
            'status'   => ['required', 'string'],

            'quiz_id'  => ['nullable', 'integer', 'exists:quizzes,id'],
            'user_id'  => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $ids    = $validated['ids'];
        $status = $validated['status'];
        $quizId = $validated['quiz_id'] ?? null;
        $userId = $validated['user_id'] ?? null;

        if (empty($ids)) {
            $message = __('admin/controllers.bulk_updated_activity_no_selection');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 400);
            }

            return back()->with('warning', $message);
        }

        try {
            $updatedCount = QuizAttempt::whereIn('id', $ids)
                ->update(['status' => $status]);

            $message = __('admin/controllers.updated_success');

            Log::info('Массовое обновление статуса попыток квиза', [
                'quiz_id' => $quizId,
                'user_id' => $userId,
                'ids'     => $ids,
                'status'  => $status,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => $message,
                    'updatedCount' => $updatedCount,
                ]);
            }

            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при массовом обновлении статуса попыток квиза: " . $e->getMessage(), [
                'ids'       => $ids,
                'exception' => $e,
            ]);

            $errorMessage = __('admin/controllers.updated_error');

            if ($request->expectsJson()) {
                return response()->json(['message' => $errorMessage], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }
}
