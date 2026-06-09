<?php

namespace App\Http\Controllers\Admin\School\SchoolQuizAnswer;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolQuizAnswer\SchoolQuizAnswerRequest;
use App\Http\Resources\Admin\School\SchoolQuiz\SchoolQuizSharedResource;
use App\Http\Resources\Admin\School\SchoolQuizAnswer\SchoolQuizAnswerResource;
use App\Http\Resources\Admin\School\SchoolQuizQuestion\SchoolQuizQuestionResource;
use App\Models\Admin\School\SchoolQuiz\SchoolQuiz;
use App\Models\Admin\School\SchoolQuizAnswer\SchoolQuizAnswer;
use App\Models\Admin\School\SchoolQuizQuestion\SchoolQuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления ответами викторин
 * (SchoolQuizAnswer) в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 * - мультиязычность, изображения
 * - связи с викторинами и вопросами викторин.
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolQuizAnswer
 * @see SchoolQuizAnswerRequest
 *
 */
class SchoolQuizAnswerController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolQuizAnswer::class;

    /** Название сущности */
    protected string $entityLabel = 'ответов квиза';

    /** Поля переводов */
    protected array $translationFields = [
        'text',
        'explanation',
    ];

    /** Список ответов квизов */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $quizId = $request->query('school_quiz_id');
        $questionId = $request->query('school_quiz_question_id');

        $adminSchoolQuizAnswersPerPage = (int) config('site_settings.adminSchoolQuizAnswersPerPage', 6);
        $adminSchoolQuizAnswersDefaultSort = (string) config('site_settings.adminSchoolQuizAnswersDefaultSort', 'idDesc');
        $sort = (string) $request->query('sort', $adminSchoolQuizAnswersDefaultSort);

        try {
            $query = $this->baseQuery()
                ->with([
                    'translation',
                    'translations',
                    'quiz.translation',
                    'quiz.translations',
                    'question.translation',
                    'question.translations',
                    'question.quiz.translation',
                ])
                ->withCount([
                    'attemptItems',
                ]);

            if ($quizId) {
                $query->where('school_quiz_id', (int) $quizId);
            }

            if ($questionId) {
                $query->where('school_quiz_question_id', (int) $questionId);
            }

            match ($sort) {
                'sort_asc' => $query->orderBy('sort')->orderByDesc('id'),
                'sort_desc' => $query->orderByDesc('sort')->orderByDesc('id'),

                'weight_asc' => $query->orderBy('weight')->orderByDesc('id'),
                'weight_desc' => $query->orderByDesc('weight')->orderByDesc('id'),

                'correct' => $query->where('is_correct', true),
                'incorrect' => $query->where('is_correct', false),

                'activity' => $query->where('activity', true),
                'inactive' => $query->where('activity', false),

                'date_asc' => $query->orderBy('id'),
                default => $query->orderByDesc('id'),
            };

            $answers = $query->get();

            return Inertia::render('Admin/School/QuizAnswers/Index', [
                'answers' => SchoolQuizAnswerResource::collection($answers),
                'answersCount' => $answers->count(),

                'adminSchoolQuizAnswersPerPage' => $adminSchoolQuizAnswersPerPage,
                'adminSchoolQuizAnswersDefaultSort' => $adminSchoolQuizAnswersDefaultSort,

                'filters' => [
                    'school_quiz_id' => $quizId,
                    'school_quiz_question_id' => $questionId,
                ],

                'quizzes' => $this->quizzesForSelect(),
                'questions' => $this->questionsForSelect($quizId ? (int) $quizId : null),

                'currentQuizId' => $quizId ? (int) $quizId : null,
                'currentQuestionId' => $questionId ? (int) $questionId : null,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки school quiz answers: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/QuizAnswers/Index', [
                'answers' => [],
                'answersCount' => 0,

                'adminSchoolQuizAnswersPerPage' => $adminSchoolQuizAnswersPerPage,
                'adminSchoolQuizAnswersDefaultSort' => $adminSchoolQuizAnswersDefaultSort,

                'filters' => [
                    'school_quiz_id' => $quizId,
                    'school_quiz_question_id' => $questionId,
                ],

                'quizzes' => [],
                'questions' => [],

                'currentQuizId' => $quizId ? (int) $quizId : null,
                'currentQuestionId' => $questionId ? (int) $questionId : null,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'error' => 'Ошибка загрузки ответов квизов.',
            ]);
        }
    }

    /** Страница создания ответа */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $quizId = $request->query('school_quiz_id');
        $questionId = $request->query('school_quiz_question_id');

        return Inertia::render('Admin/School/QuizAnswers/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'quizzes' => $this->quizzesForSelect(),
            'questions' => $this->questionsForSelect($quizId ? (int) $quizId : null),

            'defaultQuizId' => $quizId ? (int) $quizId : null,
            'defaultQuestionId' => $questionId ? (int) $questionId : null,
        ]);
    }

    /** Создание ответа */
    public function store(SchoolQuizAnswerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset($data['translations']);

        $answer = null;

        try {
            DB::transaction(function () use (&$answer, $data, $translations) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = SchoolQuizAnswer::query()
                        ->where('school_quiz_question_id', $data['school_quiz_question_id'])
                        ->max('sort');

                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                $answer = SchoolQuizAnswer::create($data);

                $this->syncTranslations($answer, $translations);
            });

            return redirect()
                ->route('admin.schoolQuizAnswers.index', [
                    'school_quiz_id' => $answer->school_quiz_id,
                    'school_quiz_question_id' => $answer->school_quiz_question_id,
                ])
                ->with('success', 'Ответ квиза успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school quiz answer: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании ответа квиза.');
        }
    }

    /** Редирект на страницу редактирования */
    public function show(int $schoolQuizAnswer): RedirectResponse
    {
        return redirect()->route('admin.schoolQuizAnswers.edit', $schoolQuizAnswer);
    }

    /** Страница редактирования ответа */
    public function edit(int $schoolQuizAnswer, Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $answer = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'quiz.translation',
                'quiz.translations',
                'question.translation',
                'question.translations',
                'question.quiz.translation',
            ])
            ->withCount([
                'attemptItems',
            ])
            ->findOrFail($schoolQuizAnswer);

        return Inertia::render('Admin/School/QuizAnswers/Edit', [
            'answer' => new SchoolQuizAnswerResource($answer),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'quizzes' => $this->quizzesForSelect(),
            'questions' => $this->questionsForSelect(),
        ]);
    }

    /** Обновление ответа */
    public function update(SchoolQuizAnswerRequest $request, int $schoolQuizAnswer): RedirectResponse
    {
        $answer = $this->baseQuery()->findOrFail($schoolQuizAnswer);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset(
            $data['translations'],
            $data['_method']
        );

        try {
            DB::transaction(function () use ($answer, $data, $translations) {
                $answer->update($data);

                $this->syncTranslations($answer, $translations);
            });

            return redirect()
                ->route('admin.schoolQuizAnswers.index', [
                    'school_quiz_id' => $answer->school_quiz_id,
                    'school_quiz_question_id' => $answer->school_quiz_question_id,
                ])
                ->with('success', 'Ответ квиза успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school quiz answer ID ' . $answer->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении ответа квиза.');
        }
    }

    /** Удаление ответа */
    public function destroy(int $schoolQuizAnswer): RedirectResponse
    {
        $answer = $this->baseQuery()->findOrFail($schoolQuizAnswer);

        $quizId = $answer->school_quiz_id;
        $questionId = $answer->school_quiz_question_id;

        try {
            $answer->delete();

            return redirect()
                ->route('admin.schoolQuizAnswers.index', [
                    'school_quiz_id' => $quizId,
                    'school_quiz_question_id' => $questionId,
                ])
                ->with('success', 'Ответ квиза успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school quiz answer ID ' . $answer->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении ответа квиза.');
        }
    }

    /** Массовое удаление ответов */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_quiz_answers,id'],
            'school_quiz_id' => ['nullable', 'integer', 'exists:school_quizzes,id'],
            'school_quiz_question_id' => ['nullable', 'integer', 'exists:school_quiz_questions,id'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                SchoolQuizAnswer::query()
                    ->whereIn('id', $data['ids'])
                    ->delete();
            });

            return back()->with('success', 'Выбранные ответы успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка массового удаления school quiz answers: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении ответов квиза.');
        }
    }

    /** Список квизов для select */
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

    /** Список вопросов для select */
    private function questionsForSelect(?int $quizId = null): AnonymousResourceCollection
    {
        $query = SchoolQuizQuestion::query()
            ->with([
                'translation',
                'translations',
                'quiz.translation',
                'quiz.translations',
            ])
            ->orderBy('school_quiz_id')
            ->orderBy('sort')
            ->orderBy('id');

        if ($quizId) {
            $query->where('school_quiz_id', $quizId);
        }

        return SchoolQuizQuestionResource::collection($query->get());
    }
}
