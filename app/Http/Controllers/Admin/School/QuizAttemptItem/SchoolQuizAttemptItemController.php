<?php

namespace App\Http\Controllers\Admin\School\QuizAttemptItem;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\QuizAttemptItem\SchoolQuizAttemptItemRequest;
use App\Http\Resources\Admin\School\QuizAnswer\SchoolQuizAnswerSharedResource;
use App\Http\Resources\Admin\School\QuizAttempt\SchoolQuizAttemptResource;
use App\Http\Resources\Admin\School\QuizAttemptItem\SchoolQuizAttemptItemResource;
use App\Http\Resources\Admin\School\QuizQuestion\SchoolQuizQuestionSharedResource;
use App\Models\Admin\School\QuizAnswer\SchoolQuizAnswer;
use App\Models\Admin\School\QuizAttempt\SchoolQuizAttempt;
use App\Models\Admin\School\QuizAttemptItem\SchoolQuizAttemptItem;
use App\Models\Admin\School\QuizQuestion\SchoolQuizQuestion;
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
 * (SchoolQuizAttemptItem) в административной панели.
 * SchoolQuizAttemptItem — это ответ пользователя на один конкретный вопрос викторины
 *
 * CRUD +:
 * - удаление (одиночное и массовое)
 * - связи с попытками, с вопросами, с ответами викторин.
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolQuizAttemptItem
 * @see SchoolQuizAttemptItemRequest
 *
 */
class SchoolQuizAttemptItemController extends Controller
{
    /** Список ответов в попытках квиза. */
    public function index(Request $request): Response
    {
        $attemptId = $request->query('school_quiz_attempt_id');
        $questionId = $request->query('school_quiz_question_id');
        $isCorrect = $request->query('is_correct');

        $adminSchoolQuizAttemptItemsPerPage = (int) config('site_settings.adminSchoolQuizAttemptItemsPerPage', 20);
        $adminSchoolQuizAttemptItemsDefaultSort = (string) config('site_settings.adminSchoolQuizAttemptItemsDefaultSort', 'idDesc');

        try {
            $query = SchoolQuizAttemptItem::query()
                ->with([
                    'attempt.user:id,name,email',
                    'attempt.quiz.translation',
                    'attempt.quiz.translations',

                    'question.translation',
                    'question.translations',
                    'question.answers.translation',
                    'question.answers.translations',

                    'selectedAnswer.translation',
                    'selectedAnswer.translations',
                ]);

            if ($attemptId) {
                $query->where('school_quiz_attempt_id', (int) $attemptId);
            }

            if ($questionId) {
                $query->where('school_quiz_question_id', (int) $questionId);
            }

            if ($isCorrect !== null && $isCorrect !== '') {
                $query->where('is_correct', filter_var($isCorrect, FILTER_VALIDATE_BOOL));
            }

            match ($adminSchoolQuizAttemptItemsDefaultSort) {
                'idAsc' => $query->orderBy('id'),
                'scoreAsc' => $query->orderBy('score')->orderByDesc('id'),
                'scoreDesc' => $query->orderByDesc('score')->orderByDesc('id'),
                'maxScoreAsc' => $query->orderBy('max_score')->orderByDesc('id'),
                'maxScoreDesc' => $query->orderByDesc('max_score')->orderByDesc('id'),
                default => $query->orderByDesc('id'),
            };

            $items = $query->get();

            return Inertia::render('Admin/School/QuizAttemptItems/Index', [
                'items' => SchoolQuizAttemptItemResource::collection($items),
                'itemsCount' => $items->count(),

                'adminSchoolQuizAttemptItemsPerPage' => $adminSchoolQuizAttemptItemsPerPage,
                'adminSchoolQuizAttemptItemsDefaultSort' => $adminSchoolQuizAttemptItemsDefaultSort,

                'filters' => [
                    'school_quiz_attempt_id' => $attemptId,
                    'school_quiz_question_id' => $questionId,
                    'is_correct' => $isCorrect,
                ],

                'attempts' => $this->attemptsForSelect(),
                'questions' => $this->questionsForSelect(),
                'answers' => $this->answersForSelect(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки school quiz attempt items: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/QuizAttemptItems/Index', [
                'items' => [],
                'itemsCount' => 0,

                'adminSchoolQuizAttemptItemsPerPage' => $adminSchoolQuizAttemptItemsPerPage,
                'adminSchoolQuizAttemptItemsDefaultSort' => $adminSchoolQuizAttemptItemsDefaultSort,

                'filters' => [
                    'school_quiz_attempt_id' => $attemptId,
                    'school_quiz_question_id' => $questionId,
                    'is_correct' => $isCorrect,
                ],

                'attempts' => [],
                'questions' => [],
                'answers' => [],

                'error' => 'Ошибка загрузки ответов попыток квиза.',
            ]);
        }
    }

    /** Страница создания ответа в попытке. */
    public function create(Request $request): Response
    {
        return Inertia::render('Admin/School/QuizAttemptItems/Create', [
            'attempts' => $this->attemptsForSelect(),
            'questions' => $this->questionsForSelect(),
            'answers' => $this->answersForSelect(),

            'defaultAttemptId' => $request->query('school_quiz_attempt_id')
                ? (int) $request->query('school_quiz_attempt_id')
                : null,

            'defaultQuestionId' => $request->query('school_quiz_question_id')
                ? (int) $request->query('school_quiz_question_id')
                : null,
        ]);
    }

    /** Сохранение ответа в попытке. */
    public function store(SchoolQuizAttemptItemRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $item = DB::transaction(function () use ($data) {
                $item = SchoolQuizAttemptItem::create([
                    'school_quiz_attempt_id' => $data['school_quiz_attempt_id'],
                    'school_quiz_question_id' => $data['school_quiz_question_id'],

                    'selected_answer_id' => $data['selected_answer_id'] ?? null,
                    'selected_answer_ids' => $data['selected_answer_ids'] ?? null,
                    'free_text_answer' => $data['free_text_answer'] ?? null,

                    'is_correct' => $data['is_correct'] ?? false,
                    'score' => $data['score'] ?? 0,
                    'max_score' => $data['max_score'] ?? 0,
                    'reviewer_comment' => $data['reviewer_comment'] ?? null,
                ]);

                $this->recalculateAttempt($item->school_quiz_attempt_id);

                return $item;
            });

            return redirect()
                ->route('admin.schoolQuizAttemptItems.index')
                ->with('success', 'Ответ попытки квиза успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school quiz attempt item: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $data,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании ответа попытки квиза.');
        }
    }

    /** Редирект на редактирование. */
    public function show(int $schoolQuizAttemptItem): RedirectResponse
    {
        return redirect()->route('admin.schoolQuizAttemptItems.edit', $schoolQuizAttemptItem);
    }

    /** Страница проверки/редактирования ответа. */
    public function edit(int $schoolQuizAttemptItem): Response
    {
        $item = SchoolQuizAttemptItem::query()
            ->with([
                'attempt.user:id,name,email',
                'attempt.quiz.translation',
                'attempt.quiz.translations',

                'question.translation',
                'question.translations',
                'question.answers.translation',
                'question.answers.translations',

                'selectedAnswer.translation',
                'selectedAnswer.translations',
            ])
            ->findOrFail($schoolQuizAttemptItem);

        return Inertia::render('Admin/School/QuizAttemptItems/Edit', [
            'item' => new SchoolQuizAttemptItemResource($item),
        ]);
    }

    /** Обновление проверки ответа. */
    public function update(
        SchoolQuizAttemptItemRequest $request,
        int $schoolQuizAttemptItem
    ): RedirectResponse {
        $item = SchoolQuizAttemptItem::query()
            ->with(['question'])
            ->findOrFail($schoolQuizAttemptItem);

        $data = $request->validated();

        try {
            DB::transaction(function () use ($item, $data) {
                $questionType = (string) ($item->question?->question_type ?? '');
                $isOpenText = $questionType === 'open_text';

                $payload = [
                    'score' => $data['score'] ?? $item->score,
                    'max_score' => $data['max_score'] ?? $item->max_score,
                    'reviewer_comment' => $data['reviewer_comment'] ?? $item->reviewer_comment,
                ];

                if (array_key_exists('is_correct', $data)) {
                    $payload['is_correct'] = (bool) $data['is_correct'];
                }

                if (array_key_exists('score', $payload) && $item->question?->points !== null) {
                    $payload['score'] = min((int) $payload['score'], (int) $item->question->points);
                }

                $item->update($payload);

                $this->recalculateAttempt($item->school_quiz_attempt_id);
            });

            return redirect()
                ->route('admin.schoolQuizAttemptItems.index')
                ->with('success', 'Проверка ответа успешно обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school quiz attempt item ID ' . $item->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении проверки ответа.');
        }
    }

    /** Удаление ответа из попытки. */
    public function destroy(int $schoolQuizAttemptItem): RedirectResponse
    {
        $item = SchoolQuizAttemptItem::query()->findOrFail($schoolQuizAttemptItem);
        $attemptId = $item->school_quiz_attempt_id;

        try {
            DB::transaction(function () use ($item, $attemptId) {
                $item->delete();

                $this->recalculateAttempt($attemptId);
            });

            return redirect()
                ->route('admin.schoolQuizAttemptItems.index', [
                    'school_quiz_attempt_id' => $attemptId,
                ])
                ->with('success', 'Ответ попытки квиза успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school quiz attempt item ID ' . $item->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении ответа попытки квиза.');
        }
    }

    /** Массовое удаление ответов. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_quiz_attempt_items,id'],
            'school_quiz_attempt_id' => ['nullable', 'integer', 'exists:school_quiz_attempts,id'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                $attemptIds = SchoolQuizAttemptItem::query()
                    ->whereIn('id', $data['ids'])
                    ->pluck('school_quiz_attempt_id')
                    ->unique()
                    ->values();

                SchoolQuizAttemptItem::query()
                    ->whereIn('id', $data['ids'])
                    ->delete();

                foreach ($attemptIds as $attemptId) {
                    $this->recalculateAttempt((int) $attemptId);
                }
            });

            return back()->with('success', 'Выбранные ответы попыток успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка массового удаления school quiz attempt items: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении ответов попыток.');
        }
    }

    /** Массовое обновление правильности только для open_text. */
    public function bulkUpdateCorrect(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_quiz_attempt_items,id'],
            'is_correct' => ['required', 'boolean'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                $items = SchoolQuizAttemptItem::query()
                    ->with(['question'])
                    ->whereIn('id', $data['ids'])
                    ->get();

                $attemptIds = [];

                foreach ($items as $item) {
                    $item->update([
                        'is_correct' => (bool) $data['is_correct'],
                    ]);

                    $attemptIds[] = $item->school_quiz_attempt_id;
                }

                foreach (array_unique($attemptIds) as $attemptId) {
                    $this->recalculateAttempt((int) $attemptId);
                }
            });

            return back()->with('success', 'Правильность выбранных текстовых ответов обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка массового обновления правильности school quiz attempt items: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом обновлении правильности ответов.');
        }
    }

    /** Попытки для select. */
    private function attemptsForSelect(): AnonymousResourceCollection
    {
        $attempts = SchoolQuizAttempt::query()
            ->with([
                'user:id,name,email',
                'quiz.translation',
                'quiz.translations',
            ])
            ->orderByDesc('id')
            ->get();

        return SchoolQuizAttemptResource::collection($attempts);
    }

    /** Вопросы для select. */
    private function questionsForSelect(): AnonymousResourceCollection
    {
        $questions = SchoolQuizQuestion::query()
            ->with([
                'translation',
                'translations',
                'quiz.translation',
            ])
            ->orderByDesc('id')
            ->get();

        return SchoolQuizQuestionSharedResource::collection($questions);
    }

    /** Ответы для select. */
    private function answersForSelect(): AnonymousResourceCollection
    {
        $answers = SchoolQuizAnswer::query()
            ->with([
                'translation',
                'translations',
                'question.translation',
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return SchoolQuizAnswerSharedResource::collection($answers);
    }

    /** Пересчёт результата попытки. */
    private function recalculateAttempt(int $attemptId): void
    {
        $attempt = SchoolQuizAttempt::query()
            ->with(['items'])
            ->find($attemptId);

        if (!$attempt) {
            return;
        }

        $attempt->score = (int) $attempt->items->sum('score');
        $attempt->max_score = (int) $attempt->items->sum('max_score');

        $attempt->recalcPercent();
        $attempt->save();
    }
}
