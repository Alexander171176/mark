<?php

namespace App\Http\Controllers\Admin\School\QuizQuestion;

use App\Http\Controllers\Admin\School\Base\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\QuizQuestion\SchoolQuizQuestionRequest;
use App\Http\Resources\Admin\School\Quiz\SchoolQuizSharedResource;
use App\Http\Resources\Admin\School\QuizQuestion\SchoolQuizQuestionResource;
use App\Models\Admin\School\Quiz\SchoolQuiz;
use App\Models\Admin\School\QuizQuestion\SchoolQuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SchoolQuizQuestionController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolQuizQuestion::class;

    /** Название сущности */
    protected string $entityLabel = 'вопросов квиза';

    /** Поля переводов */
    protected array $translationFields = [
        'question_text',
        'explanation',
    ];

    /** Список вопросов квизов. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $quizId = $request->query('school_quiz_id');

        $adminSchoolQuizQuestionsPerPage = (int) config('site_settings.adminSchoolQuizQuestionsPerPage', 10);
        $adminSchoolQuizQuestionsDefaultSort = (string) config('site_settings.adminSchoolQuizQuestionsDefaultSort', 'idDesc');

        try {
            $query = $this->baseQuery()
                ->with([
                    'translation',
                    'translations',
                    'quiz.translation',
                    'quiz.translations',
                    'answers.translation',
                    'answers.translations',
                ])
                ->withCount([
                    'answers',
                    'attemptItems',
                ]);

            if ($quizId) {
                $query->where('school_quiz_id', (int) $quizId);
            }

            match ($adminSchoolQuizQuestionsDefaultSort) {
                'idAsc' => $query->orderBy('id'),
                'sortAsc' => $query->orderBy('sort')->orderByDesc('id'),
                'sortDesc' => $query->orderByDesc('sort')->orderByDesc('id'),
                'pointsAsc' => $query->orderBy('points')->orderByDesc('id'),
                'pointsDesc' => $query->orderByDesc('points')->orderByDesc('id'),
                'typeAsc' => $query->orderBy('question_type')->orderByDesc('id'),
                'typeDesc' => $query->orderByDesc('question_type')->orderByDesc('id'),
                default => $query->orderByDesc('id'),
            };

            $questions = $query->get();

            return Inertia::render('Admin/School/QuizQuestions/Index', [
                'questions' => SchoolQuizQuestionResource::collection($questions),
                'questionsCount' => $questions->count(),

                'adminSchoolQuizQuestionsPerPage' => $adminSchoolQuizQuestionsPerPage,
                'adminSchoolQuizQuestionsDefaultSort' => $adminSchoolQuizQuestionsDefaultSort,

                'filters' => [
                    'school_quiz_id' => $quizId,
                ],

                'quizzes' => $this->quizzesForSelect(),
                'currentQuizId' => $quizId ? (int) $quizId : null,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки school quiz questions: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/QuizQuestions/Index', [
                'questions' => [],
                'questionsCount' => 0,

                'adminSchoolQuizQuestionsPerPage' => $adminSchoolQuizQuestionsPerPage,
                'adminSchoolQuizQuestionsDefaultSort' => $adminSchoolQuizQuestionsDefaultSort,

                'filters' => [
                    'school_quiz_id' => $quizId,
                ],

                'quizzes' => [],
                'currentQuizId' => $quizId ? (int) $quizId : null,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'error' => 'Ошибка загрузки вопросов квизов.',
            ]);
        }
    }

    /** Страница создания вопроса. */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $quizId = $request->query('school_quiz_id');

        return Inertia::render('Admin/School/QuizQuestions/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'quizzes' => $this->quizzesForSelect(),
            'defaultQuizId' => $quizId ? (int) $quizId : null,
        ]);
    }

    /** Сохранение нового вопроса. */
    public function store(SchoolQuizQuestionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset($data['translations']);

        try {
            DB::transaction(function () use (&$question, $data, $translations) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = SchoolQuizQuestion::query()
                        ->where('school_quiz_id', $data['school_quiz_id'])
                        ->max('sort');

                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                $question = SchoolQuizQuestion::create($data);

                $this->syncTranslations($question, $translations);
            });

            return redirect()
                ->route('admin.schoolQuizQuestions.index', [
                    'school_quiz_id' => $question->school_quiz_id,
                ])
                ->with('success', 'Вопрос квиза успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school quiz question: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании вопроса квиза.');
        }
    }

    /** Редирект на страницу редактирования. */
    public function show(int $schoolQuizQuestion): RedirectResponse
    {
        return redirect()->route('admin.schoolQuizQuestions.edit', $schoolQuizQuestion);
    }

    /** Страница редактирования вопроса. */
    public function edit(int $schoolQuizQuestion, Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $question = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'quiz.translation',
                'quiz.translations',
                'answers.translation',
                'answers.translations',
            ])
            ->withCount([
                'answers',
                'attemptItems',
            ])
            ->findOrFail($schoolQuizQuestion);

        return Inertia::render('Admin/School/QuizQuestions/Edit', [
            'question' => new SchoolQuizQuestionResource($question),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'quizzes' => $this->quizzesForSelect(),
        ]);
    }

    /** Обновление вопроса. */
    public function update(SchoolQuizQuestionRequest $request, int $schoolQuizQuestion): RedirectResponse
    {
        $question = $this->baseQuery()->findOrFail($schoolQuizQuestion);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];

        unset(
            $data['translations'],
            $data['_method']
        );

        try {
            DB::transaction(function () use ($question, $data, $translations) {
                $question->update($data);

                $this->syncTranslations($question, $translations);
            });

            return redirect()
                ->route('admin.schoolQuizQuestions.index', [
                    'school_quiz_id' => $question->school_quiz_id,
                ])
                ->with('success', 'Вопрос квиза успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school quiz question ID ' . $question->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении вопроса квиза.');
        }
    }

    /** Удаление вопроса. */
    public function destroy(int $schoolQuizQuestion): RedirectResponse
    {
        $question = $this->baseQuery()->findOrFail($schoolQuizQuestion);
        $quizId = $question->school_quiz_id;

        try {
            $question->delete();

            return redirect()
                ->route('admin.schoolQuizQuestions.index', [
                    'school_quiz_id' => $quizId,
                ])
                ->with('success', 'Вопрос квиза успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school quiz question ID ' . $question->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении вопроса квиза.');
        }
    }

    /** Массовое удаление вопросов. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_quiz_questions,id'],
            'school_quiz_id' => ['nullable', 'integer', 'exists:school_quizzes,id'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                SchoolQuizQuestion::query()
                    ->whereIn('id', $data['ids'])
                    ->delete();
            });

            return back()->with('success', 'Выбранные вопросы успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка массового удаления school quiz questions: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении вопросов квиза.');
        }
    }

    /** Список квизов для select. */
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
}
