<?php

namespace App\Http\Controllers\Admin\School\QuizAnswer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\QuizAnswer\QuizAnswerRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\School\Quiz\QuizResource;
use App\Http\Resources\Admin\School\QuizAnswer\QuizAnswerResource;
use App\Http\Resources\Admin\School\QuizQuestion\QuizQuestionResource;
use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\QuizAnswer\QuizAnswer;
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
 * Админ-контроллер для управления вариантами ответов (QuizAnswer).
 *
 * Соответствует ресурсным маршрутам:
 *  - admin.quizAnswers.* (Route::resource('/quiz-answers', ...))
 *
 * Доп. экшены:
 *  - actions.quizAnswers.updateActivity
 *  - actions.quizAnswers.bulkUpdateActivity
 *  - actions.quizAnswers.updateSortBulk
 *  - actions.quizAnswers.bulkDestroy
 */
class QuizAnswerController extends Controller
{
    /**
     * Разрешённые локали (та же логика, что и в Quiz / QuizQuestion).
     *
     * @var array<string>
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Список вариантов ответов.
     *
     * Поддерживает фильтры через query:
     *  - ?locale=ru
     *  - ?quiz_id=123
     *  - ?quiz_question_id=456
     *
     * GET /admin/quiz-answers
     */
    public function index(Request $request): Response
    {
        $adminCountQuizAnswers = (int) config('site_settings.AdminCountQuizAnswers', 20);
        $adminSortQuizAnswers  = config('site_settings.AdminSortQuizAnswers', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $quizId         = $request->query('quiz_id');
        $quizQuestionId = $request->query('quiz_question_id');

        $answers      = collect();
        $answersCount = 0;

        try {
            $query = QuizAnswer::query()
                ->with([
                    'quiz:id,title,slug,locale',
                    'question:id,quiz_id,question_type,sort,question_text',
                ])
                ->whereHas('quiz', function ($q) use ($currentLocale) {
                    $q->where('locale', $currentLocale);
                });


            if ($quizId) {
                $query->where('quiz_id', (int) $quizId);
            }

            if ($quizQuestionId) {
                $query->where('quiz_question_id', (int) $quizQuestionId);
            }

            // сортируем по sort, затем id
            $answers = $query
                ->orderBy('sort')
                ->orderBy('id')
                ->get();

            $answersCount = $answers->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки ответов квиза: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        // Список квизов текущей локали для селекта
        $quizzes = Quiz::query()
            ->where('locale', $currentLocale)
            ->orderBy('title')
            ->get();

        // Список вопросов (по желанию можно ограничить по quiz_id)
        $questionsQuery = QuizQuestion::query()
            ->with('quiz:id,title,locale')
            ->whereHas('quiz', function ($q) use ($currentLocale) {
                $q->where('locale', $currentLocale);
            })
            ->orderBy('sort')
            ->orderBy('id');

        if ($quizId) {
            $questionsQuery->where('quiz_id', (int) $quizId);
        }

        $questions = $questionsQuery->get();

        return Inertia::render('Admin/School/QuizAnswers/Index', [
            'answers'            => QuizAnswerResource::collection($answers)->resolve(),
            'answersCount'       => $answersCount,
            'adminCountAnswers'  => $adminCountQuizAnswers,
            'adminSortAnswers'   => $adminSortQuizAnswers,

            'quizzes'            => QuizResource::collection($quizzes)->resolve(),
            'questions'          => QuizQuestionResource::collection($questions)->resolve(),

            'currentLocale'      => $currentLocale,
            'availableLocales'   => $this->availableLocales,

            'currentQuizId'      => $quizId ? (int) $quizId : null,
            'currentQuestionId'  => $quizQuestionId ? (int) $quizQuestionId : null,
        ]);
    }

    /**
     * Форма создания варианта ответа.
     *
     * Можно передать:
     *  - ?quiz_id=123
     *  - ?quiz_question_id=456
     *
     * GET /admin/quiz-answers/create
     */
    public function create(Request $request): Response
    {
        $quizId         = $request->query('quiz_id');
        $quizQuestionId = $request->query('quiz_question_id');

        // Квизы — без фильтра по локали, как в QuizQuestionController@create
        $quizzes = Quiz::query()
            ->orderBy('locale', 'desc')
            ->orderByDesc('id')
            ->get();

        // Вопросы — по возможности фильтруем по quiz_id
        $questionsQuery = QuizQuestion::query()
            ->with('quiz:id,title,locale')
            ->orderBy('quiz_id')
            ->orderBy('sort')
            ->orderBy('id');

        if ($quizId) {
            $questionsQuery->where('quiz_id', (int) $quizId);
        }

        $questions = $questionsQuery->get();

        $defaultQuiz = $quizId
            ? $quizzes->firstWhere('id', (int) $quizId)
            : null;

        $defaultQuestion = $quizQuestionId
            ? $questions->firstWhere('id', (int) $quizQuestionId)
            : null;

        return Inertia::render('Admin/School/QuizAnswers/Create', [
            'quizzes'           => QuizResource::collection($quizzes)->resolve(),
            'questions'         => QuizQuestionResource::collection($questions)->resolve(),
            'defaultQuizId'     => $defaultQuiz?->id,
            'defaultQuestionId' => $defaultQuestion?->id,
        ]);
    }

    /**
     * Сохранение нового варианта ответа.
     *
     * POST /admin/quiz-answers
     */
    public function store(QuizAnswerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // sort, weight, activity, is_correct нормализуются в prepareForValidation.

        // Если sort не указан — ставим после последнего в этом вопросе
        if (!isset($data['sort']) || $data['sort'] === null) {
            $maxSort = QuizAnswer::where('quiz_question_id', $data['quiz_question_id'])->max('sort');
            $data['sort'] = $maxSort !== null ? ($maxSort + 10) : 0;
        }

        try {
            /** @var QuizAnswer $answer */
            $answer = QuizAnswer::create($data);

            Log::info('Ответ квиза создан', [
                'answer_id'       => $answer->id,
                'quiz_id'         => $answer->quiz_id,
                'quiz_question_id'=> $answer->quiz_question_id,
            ]);

            return redirect()
                ->route('admin.quizAnswers.index', [
                    'quiz_id'         => $answer->quiz_id,
                    'quiz_question_id'=> $answer->quiz_question_id,
                ])
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при создании варианта ответа квиза: " . $e->getMessage(), [
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
     * GET /admin/quiz-answers/{quizAnswer}
     */
    public function show(QuizAnswer $quizAnswer): RedirectResponse
    {
        return redirect()->route('admin.quizAnswers.edit', $quizAnswer);
    }

    /**
     * Форма редактирования варианта ответа.
     *
     * GET /admin/quiz-answers/{quizAnswer}/edit
     */
    public function edit(QuizAnswer $quizAnswer): Response
    {
        // Подгружаем связи (для отображения квиза и вопроса сверху)
        $quizAnswer->load(['quiz', 'question']);

        $quizzes = Quiz::query()
            ->orderBy('locale', 'desc')
            ->orderByDesc('id')
            ->get();

        $questions = QuizQuestion::query()
            ->with('quiz:id,title,locale')
            ->orderBy('quiz_id')
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return Inertia::render('Admin/School/QuizAnswers/Edit', [
            'answer'    => new QuizAnswerResource($quizAnswer),
            'quizzes'   => QuizResource::collection($quizzes)->resolve(),
            'questions' => QuizQuestionResource::collection($questions)->resolve(),
        ]);
    }

    /**
     * Обновление варианта ответа.
     *
     * PUT/PATCH /admin/quiz-answers/{quizAnswer}
     */
    public function update(QuizAnswerRequest $request, QuizAnswer $quizAnswer): RedirectResponse
    {
        $data = $request->validated();

        try {
            $quizAnswer->update($data);

            Log::info('Ответ квиза обновлён', [
                'answer_id'       => $quizAnswer->id,
                'quiz_id'         => $quizAnswer->quiz_id,
                'quiz_question_id'=> $quizAnswer->quiz_question_id,
            ]);

            return redirect()
                ->route('admin.quizAnswers.index', [
                    'quiz_id'         => $quizAnswer->quiz_id,
                    'quiz_question_id'=> $quizAnswer->quiz_question_id,
                ])
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении варианта ответа квиза ID {$quizAnswer->id}: " . $e->getMessage(), [
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
     * Удаление одного варианта ответа.
     *
     * DELETE /admin/quiz-answers/{quizAnswer}
     */
    public function destroy(QuizAnswer $quizAnswer): RedirectResponse
    {
        $quizId     = $quizAnswer->quiz_id;
        $questionId = $quizAnswer->quiz_question_id;

        try {
            $quizAnswer->delete();

            Log::info('Ответ квиза удалён', [
                'answer_id'       => $quizAnswer->id,
                'quiz_id'         => $quizId,
                'quiz_question_id'=> $questionId,
            ]);

            return redirect()
                ->route('admin.quizAnswers.index', [
                    'quiz_id'         => $quizId,
                    'quiz_question_id'=> $questionId,
                ])
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при удалении варианта ответа квиза ID {$quizAnswer->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Массовое удаление вариантов ответа.
     *
     * DELETE /admin/actions/quiz-answers/bulk-delete
     *
     * Ожидает:
     *  - ids: [1,2,3,...]
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:quiz_answers,id'],

            // опционально, если всё-таки будешь слать
            'quiz_id'          => ['nullable', 'integer', 'exists:quizzes,id'],
            'quiz_question_id' => ['nullable', 'integer', 'exists:quiz_questions,id'],
        ]);

        $ids        = $validated['ids'];
        $quizId     = $validated['quiz_id'] ?? null;
        $questionId = $validated['quiz_question_id'] ?? null;
        $count      = count($ids);

        try {
            DB::beginTransaction();

            QuizAnswer::whereIn('id', $ids)->delete();

            DB::commit();

            Log::info('Массовое удаление вариантов ответов квиза', [
                'quiz_id'          => $quizId,
                'quiz_question_id' => $questionId,
                'ids'              => $ids,
            ]);

            return redirect()
                ->route('admin.quizAnswers.index', [
                    // если есть — используем, если нет — просто не передаём
                    ...( $quizId ? ['quiz_id' => $quizId] : [] ),
                    ...( $questionId ? ['quiz_question_id' => $questionId] : [] ),
                ])
                ->with('success', __('admin/controllers.bulk_deleted_success', [
                    'count' => $count,
                ]));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error("Ошибка при массовом удалении вариантов ответов квиза: " . $e->getMessage(), [
                'ids'       => $ids,
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }

    /**
     * Обновление активности одного варианта ответа.
     *
     * PUT /admin/actions/quiz-answers/{quizAnswer}/activity
     */
    public function updateActivity(UpdateActivityRequest $request, QuizAnswer $quizAnswer): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $quizAnswer->activity = $validated['activity'];
            $quizAnswer->save();

            Log::info("Обновлена активность ответа квиза ID {$quizAnswer->id} ({$quizAnswer->activity})");

            return back()
                ->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении активности ответа квиза ID {$quizAnswer->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности вариантов ответа.
     *
     * PUT /admin/actions/quiz-answers/bulk-activity
     *
     * Ожидает:
     *  - ids: [1,2,3,...]
     *  - activity: bool
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => ['required', 'array'],
            'ids.*'    => ['integer', 'exists:quiz_answers,id'],
            'activity' => ['required', 'boolean'],

            'quiz_id'          => ['nullable', 'integer', 'exists:quizzes,id'],
            'quiz_question_id' => ['nullable', 'integer', 'exists:quiz_questions,id'],
        ]);

        $ids        = $validated['ids'];
        $activity   = $validated['activity'];
        $quizId     = $validated['quiz_id'] ?? null;
        $questionId = $validated['quiz_question_id'] ?? null;

        if (empty($ids)) {
            $message = __('admin/controllers.bulk_updated_activity_no_selection');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 400);
            }

            return back()->with('warning', $message);
        }

        try {
            $updatedCount = QuizAnswer::whereIn('id', $ids)
                ->update(['activity' => $activity]);

            $message = __('admin/controllers.bulk_activity_updated_success');

            Log::info('Массовое обновление активности вариантов ответов квиза', [
                'quiz_id'          => $quizId,
                'quiz_question_id' => $questionId,
                'ids'              => $ids,
                'activity'         => $activity,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => $message,
                    'updatedCount' => $updatedCount,
                ]);
            }

            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при массовом обновлении активности вариантов ответов квиза: " . $e->getMessage(), [
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
     * Обновление сортировки одного варианта ответа.
     *
     * PUT /admin/actions/quiz-answers/{quizAnswer}/sort
     */
    public function updateSort(UpdateSortEntityRequest $request, QuizAnswer $quizAnswer): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort     = $quizAnswer->sort;
            $quizAnswer->sort = (int) $validated['sort'];
            $quizAnswer->save();

            Log::info("Сортировка варианта ответа ID {$quizAnswer->id} изменена с {$originalSort} на {$quizAnswer->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки варианта ответа ID {$quizAnswer->id}: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки ответов квиза.
     * Ожидает массив: [{id: 1, sort: 10}, ...]
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        Log::info('updateSortBulk QUIZ ANSWERS called', [
            'payload' => $request->all(),
        ]);

        $validated = $request->validate([
            'answers'           => ['required', 'array'],
            'answers.*.id'      => ['required', 'integer', 'exists:quiz_answers,id'],
            'answers.*.sort'    => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['answers'] as $row) {
                    QuizAnswer::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления сортировки QUIZ ANSWERS: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

}
