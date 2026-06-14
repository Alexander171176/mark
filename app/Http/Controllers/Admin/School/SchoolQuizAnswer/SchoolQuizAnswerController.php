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
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
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

    /** Список ответов квизов. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $quizId = $request->query('school_quiz_id');
        $questionId = $request->query('school_quiz_question_id');

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminSchoolQuizAnswersPerPage', 6);
        $defaultSort = $settings->string('adminSchoolQuizAnswersDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminSchoolQuizAnswersProcessingMode',
            'frontend'
        );

        $answersCount = $this->indexQuery($quizId, $questionId)->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer($processingMode, $answersCount, 300);

        try {
            $answers = $this->getIndexAnswers(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                quizId: $quizId,
                questionId: $questionId,
            );

            return Inertia::render('Admin/School/SchoolQuizAnswers/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolQuizAnswersPerPage' => $perPage,
                'adminSchoolQuizAnswersDefaultSort' => $defaultSort,
                'adminSchoolQuizAnswersProcessingMode' => $processingMode,

                'answers' => SchoolQuizAnswerResource::collection($answers),
                'answersCount' => $answersCount,

                'sortParam' => $sortParam,
                'search' => $search,

                'filters' => [
                    'school_quiz_id' => $quizId,
                    'school_quiz_question_id' => $questionId,
                ],

                'quizzes' => $this->quizzesForSelect(),
                'questions' => $this->questionsForSelect($quizId ? (int) $quizId : null),

                'currentQuizId' => $quizId ? (int) $quizId : null,
                'currentQuestionId' => $questionId ? (int) $questionId : null,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки school quiz answers: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/School/SchoolQuizAnswers/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminSchoolQuizAnswersPerPage' => $perPage,
                'adminSchoolQuizAnswersDefaultSort' => $defaultSort,
                'adminSchoolQuizAnswersProcessingMode' => $processingMode,

                'answers' => [],
                'answersCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'filters' => [
                    'school_quiz_id' => $quizId,
                    'school_quiz_question_id' => $questionId,
                ],

                'quizzes' => [],
                'questions' => [],

                'currentQuizId' => $quizId ? (int) $quizId : null,
                'currentQuestionId' => $questionId ? (int) $questionId : null,

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

        return Inertia::render('Admin/School/SchoolQuizAnswers/Create', [
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

        return Inertia::render('Admin/School/SchoolQuizAnswers/Edit', [
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

    /** Базовый запрос для списка ответов квизов. */
    private function indexQuery(
        null|string|int $quizId = null,
        null|string|int $questionId = null
    ): Builder {
        return $this->baseQuery()
            ->when($quizId, fn (Builder $query) => $query
                ->where('school_quiz_id', (int) $quizId)
            )
            ->when($questionId, fn (Builder $query) => $query
                ->where('school_quiz_question_id', (int) $questionId)
            )
            ->with([
                'translation',
                'translations',

                'quiz.translation',
                'quiz.translations',

                'question.translation',
                'question.translations',
                'question.quiz.translation',
                'question.quiz.translations',
            ])
            ->withCount([
                'attemptItems',
            ]);
    }

    /** Получение списка ответов квизов по активному режиму обработки. */
    private function getIndexAnswers(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        null|string|int $quizId = null,
        null|string|int $questionId = null,
    ) {
        $query = $this->indexQuery($quizId, $questionId);

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
