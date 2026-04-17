<?php

namespace App\Http\Controllers\Admin\School\QuizQuestion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\QuizQuestion\QuizQuestionRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\Quiz\QuizResource;
use App\Http\Resources\Admin\School\QuizQuestion\QuizQuestionResource;
use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\QuizQuestion\QuizQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Админ-контроллер для управления вопросами квиза (QuizQuestion).
 *
 * Соответствует ресурсным маршрутам:
 *  - admin.quizQuestions.* (Route::resource('/quiz-questions', ...))
 *
 * Доп. экшены:
 *  - actions.quizQuestions.updateActivity
 *  - actions.quizQuestions.bulkUpdateActivity
 *  - actions.quizQuestions.updateSortBulk
 *  - actions.learningQuestions.updateSort (имя в роуте)
 *  - actions.quizQuestions.bulkDestroy
 */
class QuizQuestionController extends Controller
{

    /**
     * Разрешённые локали.
     *
     * @var array<string>
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Список вопросов.
     *
     * Поддерживает фильтр по quiz_id через ?quiz_id=123.
     *
     * GET /admin/quiz-questions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountQuizQuestions = (int) config('site_settings.AdminCountQuizQuestions', 10);
        $adminSortQuizQuestions  = config('site_settings.AdminSortQuizQuestions', 'idDesc');

        // 🔹 Берём локаль из query ?locale=ru, как у квизов
        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $quizId = $request->query('quiz_id');

        $questions      = collect();
        $questionsCount = 0;

        try {
            $query = QuizQuestion::query()
                ->with([
                    // важно добавить locale, чтобы в ресурсе/фронте он был
                    'quiz:id,title,slug,locale',
                ])
                ->withCount('answers')
                // 🔹 фильтруем вопросы по локали связанного квиза
                ->whereHas('quiz', function ($q) use ($currentLocale) {
                    $q->where('locale', $currentLocale);
                });

            if ($quizId) {
                $query->where('quiz_id', $quizId);
            }

            // пока как у квизов — сортируем по sort, затем id
            $questions = $query
                ->orderBy('sort')
                ->orderBy('id')
                ->get();

            $questionsCount = $questions->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки вопросов квиза: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        // 🔹 Список КВИЗОВ только текущей локали для селекта
        $quizzes = Quiz::query()
            ->where('locale', $currentLocale)
            ->orderBy('title')
            ->get();

        return Inertia::render('Admin/School/QuizQuestions/Index', [
            'questions'            => QuizQuestionResource::collection($questions)->resolve(),
            'questionsCount'       => $questionsCount,
            'adminCountQuestions'  => $adminCountQuizQuestions,
            'adminSortQuestions'   => $adminSortQuizQuestions,
            'quizzes'              => QuizResource::collection($quizzes)->resolve(),
            'currentQuizId'        => $quizId ? (int) $quizId : null,

            // 🔹 добавляем локали, как у QuizController
            'currentLocale'        => $currentLocale,
            'availableLocales'     => $this->availableLocales,
        ]);
    }

    /**
     * Форма создания вопроса.
     *
     * Можно передать ?quiz_id=123, чтобы предзаполнить квиз в форме.
     *
     * GET /admin/quiz-questions/create
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $quizId = $request->query('quiz_id');

        $quizzes = Quiz::query()
            ->orderBy('locale', 'desc') // kk, en, ru, ... (обратный алфавит)
            ->orderByDesc('id')         // внутри локали — от последнего к первому
            ->get();

        $defaultQuiz = $quizId
            ? $quizzes->firstWhere('id', (int) $quizId)
            : null;

        return Inertia::render('Admin/School/QuizQuestions/Create', [
            'quizzes'       => QuizResource::collection($quizzes)->resolve(),
            'defaultQuizId' => $defaultQuiz?->id,
        ]);
    }

    /**
     * Сохранение нового вопроса.
     *
     * POST /admin/quiz-questions
     *
     * @param QuizQuestionRequest $request
     * @return RedirectResponse
     */
    public function store(QuizQuestionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Гарантия типов уже сделана в prepareForValidation:
        // quiz_id, sort, points, activity

        // Если sort не указан — ставим после последнего в этом квизе
        if (!isset($data['sort']) || $data['sort'] === null) {
            $maxSort = QuizQuestion::where('quiz_id', $data['quiz_id'])->max('sort');
            $data['sort'] = $maxSort !== null ? ($maxSort + 10) : 0;
        }

        try {
            $question = QuizQuestion::create($data);

            Log::info('Вопрос квиза создан', [
                'question_id' => $question->id,
                'quiz_id'     => $question->quiz_id,
            ]);

            return redirect()
                ->route('admin.quizQuestions.index', ['quiz_id' => $question->quiz_id])
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при создании вопроса квиза: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * show в админке — редирект на edit.
     *
     * GET /admin/quiz-questions/{quizQuestion}
     *
     * @param QuizQuestion $quizQuestion
     * @return RedirectResponse
     */
    public function show(QuizQuestion $quizQuestion): RedirectResponse
    {
        return redirect()->route('admin.quizQuestions.edit', $quizQuestion);
    }

    /**
     * Форма редактирования вопроса.
     *
     * GET /admin/quiz-questions/{quizQuestion}/edit
     *
     * @param QuizQuestion $quizQuestion
     * @return Response
     */
    public function edit(QuizQuestion $quizQuestion): Response
    {
        $quizQuestion->load(['quiz', 'answers']);

        $quizzes = Quiz::query()
            ->orderBy('locale', 'desc') // локали от последних к первым
            ->orderByDesc('id')         // внутри локали — новые сверху
            ->get();

        return Inertia::render('Admin/School/QuizQuestions/Edit', [
            'question' => new QuizQuestionResource($quizQuestion),
            'quizzes'  => QuizResource::collection($quizzes)->resolve(),
        ]);
    }

    /**
     * Обновление вопроса.
     *
     * PUT/PATCH /admin/quiz-questions/{quizQuestion}
     *
     * @param QuizQuestionRequest $request
     * @param QuizQuestion $quizQuestion
     * @return RedirectResponse
     */
    public function update(QuizQuestionRequest $request, QuizQuestion $quizQuestion): RedirectResponse
    {
        $data = $request->validated();

        try {
            $quizQuestion->update($data);

            Log::info('Вопрос квиза обновлён', [
                'question_id' => $quizQuestion->id,
                'quiz_id'     => $quizQuestion->quiz_id,
            ]);

            return redirect()
                ->route('admin.quizQuestions.index', ['quiz_id' => $quizQuestion->quiz_id])
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении вопроса квиза ID {$quizQuestion->id}: " . $e->getMessage(), [
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
     * Удаление одного вопроса.
     *
     * DELETE /admin/quiz-questions/{quizQuestion}
     *
     * @param QuizQuestion $quizQuestion
     * @return RedirectResponse
     */
    public function destroy(QuizQuestion $quizQuestion): RedirectResponse
    {
        $quizId = $quizQuestion->quiz_id;

        try {
            $quizQuestion->delete();

            Log::info('Вопрос квиза удалён', [
                'question_id' => $quizQuestion->id,
                'quiz_id'     => $quizId,
            ]);

            return redirect()
                ->route('admin.quizQuestions.index', ['quiz_id' => $quizId])
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при удалении вопроса квиза ID {$quizQuestion->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Массовое удаление вопросов.
     *
     * DELETE /admin/actions/quiz-questions/bulk-delete
     *
     * Ожидает:
     *  - ids: [1,2,3,...]
     *  - (опционально) quiz_id для редиректа
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:quiz_questions,id'],

            // опционально, только для удобного редиректа
            'quiz_id' => ['nullable', 'integer', 'exists:quizzes,id'],
        ]);

        $ids    = $validated['ids'];
        $quizId = $validated['quiz_id'] ?? null;
        $count  = count($ids);

        try {
            DB::beginTransaction();

            QuizQuestion::whereIn('id', $ids)->delete();

            DB::commit();

            Log::info('Массовое удаление вопросов квиза', [
                'quiz_id' => $quizId,
                'ids'     => $ids,
            ]);

            return redirect()
                ->route('admin.quizQuestions.index', [
                    ...( $quizId ? ['quiz_id' => $quizId] : [] ),
                ])
                ->with('success', __('admin/controllers.bulk_deleted_success', [
                    'count' => $count,
                ]));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при массовом удалении вопросов квиза: " . $e->getMessage(), [
                'ids'       => $ids,
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }

    /**
     * Обновление активности одного вопроса.
     *
     * PUT /admin/actions/quiz-questions/{quizQuestion}/activity
     *
     * @param UpdateActivityRequest $request
     * @param QuizQuestion $quizQuestion
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, QuizQuestion $quizQuestion): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $quizQuestion->activity = $validated['activity'];
            $quizQuestion->save();

            Log::info("Обновлена активность вопроса квиза ID {$quizQuestion->id} ({$quizQuestion->activity})");

            return back()
                ->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении активности вопроса квиза ID {$quizQuestion->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности вопросов.
     *
     * PUT /admin/actions/quiz-questions/bulk-activity
     *
     * Ожидает:
     *  - ids: [1,2,3,...]
     *  - activity: bool
     *  - (опционально) quiz_id для логов/редиректа
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => ['required', 'array'],
            'ids.*'    => ['integer', 'exists:quiz_questions,id'],
            'activity' => ['required', 'boolean'],

            'quiz_id'  => ['nullable', 'integer', 'exists:quizzes,id'],
        ]);

        $ids      = $validated['ids'];
        $activity = $validated['activity'];
        $quizId   = $validated['quiz_id'] ?? null;

        if (empty($ids)) {
            $message = __('admin/controllers.bulk_updated_activity_no_selection');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 400);
            }

            return back()->with('warning', $message);
        }

        try {
            $updatedCount = QuizQuestion::whereIn('id', $ids)
                ->update(['activity' => $activity]);

            $message = __('admin/controllers.bulk_activity_updated_success');

            Log::info('Массовое обновление активности вопросов квиза', [
                'quiz_id'  => $quizId,
                'ids'      => $ids,
                'activity' => $activity,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => $message,
                    'updatedCount' => $updatedCount,
                ]);
            }

            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при массовом обновлении активности вопросов квиза: " . $e->getMessage(), [
                'quiz_id'   => $quizId,
                'ids'       => $ids,
                'exception' => $e,
            ]);

            $errorMessage = __('admin/controllers.bulk_activity_updated_error');

            if ($request->expectsJson()) {
                return response()->json(['message' => $errorMessage], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Обновление сортировки одного вопроса.
     *
     * PUT /admin/actions/quiz-questions/{quizQuestion}/sort
     *
     * (имя роута: admin.actions.learningQuestions.updateSort — опечатка,
     *  но в контроллере нас это не волнует)
     *
     * @param UpdateSortEntityRequest $request
     * @param QuizQuestion $quizQuestion
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, QuizQuestion $quizQuestion): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort       = $quizQuestion->sort;
            $quizQuestion->sort = (int) $validated['sort'];
            $quizQuestion->save();

            Log::info("Сортировка вопроса квиза ID {$quizQuestion->id} изменена с {$originalSort} на {$quizQuestion->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки вопроса квиза ID {$quizQuestion->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки вопросов.
     *
     * PUT /admin/actions/quiz-questions/update-sort-bulk
     *
     * Ожидает:
     *  - questions: [{id: 1, sort: 10}, ...]
     *  - (опционально) quiz_id для логов
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'questions'        => ['required', 'array'],
            'questions.*.id'   => ['required', 'integer', 'exists:quiz_questions,id'],
            'questions.*.sort' => ['required', 'integer', 'min:0'],

            'quiz_id'          => ['nullable', 'integer', 'exists:quizzes,id'],
        ]);

        $quizId = $validated['quiz_id'] ?? null;

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['questions'] as $row) {
                    QuizQuestion::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            Log::info('Массовое обновление сортировки вопросов квиза', [
                'quiz_id'   => $quizId,
                'questions' => $validated['questions'],
            ]);

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления сортировки вопросов квиза: " . $e->getMessage(), [
                'quiz_id'   => $quizId,
                'exception' => $e,
            ]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Клонирование одного вопроса квиза вместе с ответами.
     *
     * POST /admin/actions/quiz-questions/{quizQuestion}/clone
     *
     * @param QuizQuestion $quizQuestion
     * @return RedirectResponse
     */
    public function clone(QuizQuestion $quizQuestion): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Базовая копия вопроса
            $newQuestion = $quizQuestion->replicate();

            // Поставим в конец списка внутри того же квиза
            $maxSort = QuizQuestion::where('quiz_id', $quizQuestion->quiz_id)->max('sort');
            $newQuestion->sort = $maxSort !== null ? ($maxSort + 10) : 0;

            // при желании можно сразу выключать клон:
            $newQuestion->activity = false;

            $newQuestion->push(); // сохраняем модель

            // Клонируем ответы (если есть)
            $quizQuestion->loadMissing('answers');

            foreach ($quizQuestion->answers as $answer) {
                $newAnswer = $answer->replicate();
                $newAnswer->quiz_question_id = $newQuestion->id;
                $newQuestion->question_text = $quizQuestion->question_text . ' (копия)';
                $newAnswer->save();
            }

            DB::commit();

            Log::info('Вопрос квиза клонирован', [
                'source_id' => $quizQuestion->id,
                'clone_id'  => $newQuestion->id,
                'quiz_id'   => $quizQuestion->quiz_id,
            ]);

            return redirect()
                ->route('admin.quizQuestions.index', ['quiz_id' => $newQuestion->quiz_id])
                ->with('success', __('admin/controllers.cloned_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при клонировании вопроса квиза ID {$quizQuestion->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.cloned_error'));
        }
    }

}
