<?php

namespace App\Http\Controllers\Admin\School\SchoolQuizAttemptItem;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolQuizAttemptItem\SchoolQuizAttemptItemRequest;
use App\Http\Resources\Admin\School\SchoolQuizAnswer\SchoolQuizAnswerSharedResource;
use App\Http\Resources\Admin\School\SchoolQuizAttempt\SchoolQuizAttemptSharedResource;
use App\Http\Resources\Admin\School\SchoolQuizAttemptItem\SchoolQuizAttemptItemResource;
use App\Http\Resources\Admin\School\SchoolQuizAttemptItem\SchoolQuizAttemptItemSharedResource;
use App\Http\Resources\Admin\School\SchoolQuizQuestion\SchoolQuizQuestionSharedResource;
use App\Models\Admin\School\SchoolQuizAnswer\SchoolQuizAnswer;
use App\Models\Admin\School\SchoolQuizAttempt\SchoolQuizAttempt;
use App\Models\Admin\School\SchoolQuizAttemptItem\SchoolQuizAttemptItem;
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
 * Контроллер для управления ответами пользователя
 * внутри попыток прохождения викторин.
 *
 * SchoolQuizAttemptItem — ответ пользователя
 * на один конкретный вопрос викторины.
 *
 * CRUD +:
 * - удаление;
 * - массовое удаление;
 * - массовое изменение правильности;
 * - пересчёт результата родительской попытки.
 *
 * @version 1.2
 */
class SchoolQuizAttemptItemController extends BaseSchoolAdminController
{
    /**
     * =========================================================
     * INDEX
     * =========================================================
     */

    /** Список ответов в попытках квиза. */
    public function index(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $attemptId = $request->query(
            'school_quiz_attempt_id'
        );

        $questionId = $request->query(
            'school_quiz_question_id'
        );

        $isCorrect = $request->query(
            'is_correct'
        );

        $settings = app(
            AdminSettingsService::class
        );

        $perPage = $settings->int(
            'adminSchoolQuizAttemptItemsPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolQuizAttemptItemsDefaultSort',
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
            'adminSchoolQuizAttemptItemsProcessingMode',
            'frontend'
        );

        /**
         * Count выполняем отдельным
         * лёгким запросом.
         */
        $itemsCount = $this->countQuery(
            attemptId: $attemptId,
            questionId: $questionId,
            isCorrect: $isCorrect,
        )->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $itemsCount,
            300
        );

        try {
            $items = $this->getIndexItems(
                locale: $currentLocale,
                useServerProcessing:
                $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                attemptId: $attemptId,
                questionId: $questionId,
                isCorrect: $isCorrect,
            );

            return Inertia::render(
                'Admin/School/SchoolQuizAttemptItems/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolQuizAttemptItemsPerPage' =>
                        $perPage,

                    'adminSchoolQuizAttemptItemsDefaultSort' =>
                        $defaultSort,

                    'adminSchoolQuizAttemptItemsProcessingMode' =>
                        $processingMode,

                    /**
                     * Index использует
                     * компактный SharedResource.
                     */
                    'items' =>
                        SchoolQuizAttemptItemSharedResource::collection(
                            $items
                        ),

                    'itemsCount' =>
                        $itemsCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' => [
                        'school_quiz_attempt_id' =>
                            $attemptId,

                        'school_quiz_question_id' =>
                            $questionId,

                        'is_correct' =>
                            $isCorrect,
                    ],

                    'attempts' =>
                        $this->attemptsForSelect(
                            $currentLocale
                        ),

                    'questions' =>
                        $this->questionsForSelect(
                            $currentLocale
                        ),

                    'answers' =>
                        $this->answersForSelect(
                            $currentLocale
                        ),
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки school quiz attempt items: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolQuizAttemptItems/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolQuizAttemptItemsPerPage' =>
                        $perPage,

                    'adminSchoolQuizAttemptItemsDefaultSort' =>
                        $defaultSort,

                    'adminSchoolQuizAttemptItemsProcessingMode' =>
                        $processingMode,

                    'items' =>
                        [],

                    'itemsCount' =>
                        0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'filters' => [
                        'school_quiz_attempt_id' =>
                            $attemptId,

                        'school_quiz_question_id' =>
                            $questionId,

                        'is_correct' =>
                            $isCorrect,
                    ],

                    'attempts' =>
                        [],

                    'questions' =>
                        [],

                    'answers' =>
                        [],

                    'error' =>
                        'Ошибка загрузки ответов попыток квиза.',
                ]
            );
        }
    }

    /**
     * =========================================================
     * CREATE / STORE
     * =========================================================
     */

    /** Страница создания ответа в попытке. */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/School/SchoolQuizAttemptItems/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'attempts' =>
                    $this->attemptsForSelect(
                        $currentLocale
                    ),

                'questions' =>
                    $this->questionsForSelect(
                        $currentLocale
                    ),

                'answers' =>
                    $this->answersForSelect(
                        $currentLocale
                    ),

                'defaultAttemptId' =>
                    $request->query(
                        'school_quiz_attempt_id'
                    )
                        ? (int) $request->query(
                        'school_quiz_attempt_id'
                    )
                        : null,

                'defaultQuestionId' =>
                    $request->query(
                        'school_quiz_question_id'
                    )
                        ? (int) $request->query(
                        'school_quiz_question_id'
                    )
                        : null,
            ]
        );
    }

    /** Сохранение ответа в попытке. */
    public function store(
        SchoolQuizAttemptItemRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        try {
            $item = DB::transaction(
                function () use ($data) {
                    $item = SchoolQuizAttemptItem::create([
                        'school_quiz_attempt_id' =>
                            $data[
                            'school_quiz_attempt_id'
                            ],

                        'school_quiz_question_id' =>
                            $data[
                            'school_quiz_question_id'
                            ],

                        'selected_answer_id' =>
                            $data[
                            'selected_answer_id'
                            ] ?? null,

                        'selected_answer_ids' =>
                            $data[
                            'selected_answer_ids'
                            ] ?? null,

                        'free_text_answer' =>
                            $data[
                            'free_text_answer'
                            ] ?? null,

                        'is_correct' =>
                            $data[
                            'is_correct'
                            ] ?? false,

                        'score' =>
                            $data['score']
                            ?? 0,

                        'max_score' =>
                            $data['max_score']
                            ?? 0,

                        'reviewer_comment' =>
                            $data[
                            'reviewer_comment'
                            ] ?? null,
                    ]);

                    /**
                     * После изменения Item
                     * пересчитываем Attempt.
                     */
                    $this->recalculateAttempt(
                        $item->school_quiz_attempt_id
                    );

                    return $item;
                }
            );

            return redirect()
                ->route(
                    'admin.schoolQuizAttemptItems.index',
                    [
                        'school_quiz_attempt_id' =>
                            $item->school_quiz_attempt_id,
                    ]
                )
                ->with(
                    'success',
                    'Ответ попытки квиза успешно создан.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка создания school quiz attempt item: '
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
                    'Ошибка при создании ответа попытки квиза.'
                );
        }
    }

    /**
     * =========================================================
     * SHOW / EDIT / UPDATE
     * =========================================================
     */

    /** Редирект на редактирование. */
    public function show(
        int $schoolQuizAttemptItem
    ): RedirectResponse {
        return redirect()->route(
            'admin.schoolQuizAttemptItems.edit',
            $schoolQuizAttemptItem
        );
    }

    /** Страница проверки/редактирования ответа. */
    public function edit(
        int $schoolQuizAttemptItem,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $item = SchoolQuizAttemptItem::query()
            ->with([
                /**
                 * Attempt:
                 * пользователь +
                 * Quiz текущей locale.
                 */
                'attempt' => fn ($query) =>
                $query->with([
                    'user:id,name,email',

                    'quiz' => fn ($quizQuery) =>
                    $quizQuery->with([
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
                 * Question:
                 * текущая locale.
                 *
                 * answers нужны для
                 * selected_answer_ids.
                 */
                'question' => fn ($query) =>
                $query->with([
                    'translations' =>
                        fn (
                            $translationQuery
                        ) =>
                        $translationQuery
                            ->where(
                                'locale',
                                $currentLocale
                            ),

                    'answers' => fn ($answerQuery) =>
                    $answerQuery->with([
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
                 * Одиночный выбранный Answer.
                 */
                'selectedAnswer' => fn ($query) =>
                $query->with([
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
            ])
            ->findOrFail(
                $schoolQuizAttemptItem
            );

        return Inertia::render(
            'Admin/School/SchoolQuizAttemptItems/Edit',
            [
                'item' =>
                    new SchoolQuizAttemptItemResource(
                        $item
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
    }

    /** Обновление проверки ответа. */
    public function update(
        SchoolQuizAttemptItemRequest $request,
        int $schoolQuizAttemptItem
    ): RedirectResponse {
        $item = SchoolQuizAttemptItem::query()
            ->with([
                'question',
            ])
            ->findOrFail(
                $schoolQuizAttemptItem
            );

        $data = $request->validated();

        try {
            DB::transaction(
                function () use (
                    $item,
                    $data
                ) {
                    $questionType =
                        (string) (
                            $item
                                ->question
                                ?->question_type
                            ?? ''
                        );

                    /**
                     * Пока сохраняем существующую
                     * бизнес-логику.
                     *
                     * open_text определяется,
                     * но общая схема проверки
                     * остаётся прежней.
                     */
                    $isOpenText =
                        $questionType
                        === 'open_text';

                    $payload = [
                        'score' =>
                            $data['score']
                            ?? $item->score,

                        'max_score' =>
                            $data['max_score']
                            ?? $item->max_score,

                        'reviewer_comment' =>
                            $data[
                            'reviewer_comment'
                            ]
                            ?? $item
                                ->reviewer_comment,
                    ];

                    if (
                        array_key_exists(
                            'is_correct',
                            $data
                        )
                    ) {
                        $payload[
                        'is_correct'
                        ] = (bool) $data[
                        'is_correct'
                        ];
                    }

                    /**
                     * Score не может превышать
                     * points вопроса.
                     */
                    if (
                        array_key_exists(
                            'score',
                            $payload
                        )
                        && $item
                            ->question
                            ?->points !== null
                    ) {
                        $payload['score'] = min(
                            (int) $payload['score'],
                            (int) $item
                                ->question
                                ->points
                        );
                    }

                    $item->update(
                        $payload
                    );

                    $this->recalculateAttempt(
                        $item
                            ->school_quiz_attempt_id
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.schoolQuizAttemptItems.index',
                    [
                        'school_quiz_attempt_id' =>
                            $item
                                ->school_quiz_attempt_id,
                    ]
                )
                ->with(
                    'success',
                    'Проверка ответа успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления school quiz attempt item ID '
                . $item->id
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
                    'Ошибка при обновлении проверки ответа.'
                );
        }
    }

    /**
     * =========================================================
     * DELETE
     * =========================================================
     */

    /** Удаление ответа из попытки. */
    public function destroy(
        int $schoolQuizAttemptItem
    ): RedirectResponse {
        $item = SchoolQuizAttemptItem::query()
            ->findOrFail(
                $schoolQuizAttemptItem
            );

        $attemptId =
            $item->school_quiz_attempt_id;

        try {
            DB::transaction(
                function () use (
                    $item,
                    $attemptId
                ) {
                    $item->delete();

                    $this->recalculateAttempt(
                        $attemptId
                    );
                }
            );

            return redirect()
                ->route(
                    'admin.schoolQuizAttemptItems.index',
                    [
                        'school_quiz_attempt_id' =>
                            $attemptId,
                    ]
                )
                ->with(
                    'success',
                    'Ответ попытки квиза успешно удалён.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления school quiz attempt item ID '
                . $item->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении ответа попытки квиза.'
            );
        }
    }

    /** Массовое удаление ответов. */
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
                'exists:school_quiz_attempt_items,id',
            ],

            'school_quiz_attempt_id' => [
                'nullable',
                'integer',
                'exists:school_quiz_attempts,id',
            ],
        ]);

        try {
            DB::transaction(
                function () use ($data) {
                    $attemptIds =
                        SchoolQuizAttemptItem::query()
                            ->whereIn(
                                'id',
                                $data['ids']
                            )
                            ->pluck(
                                'school_quiz_attempt_id'
                            )
                            ->unique()
                            ->values();

                    SchoolQuizAttemptItem::query()
                        ->whereIn(
                            'id',
                            $data['ids']
                        )
                        ->delete();

                    foreach (
                        $attemptIds
                        as $attemptId
                    ) {
                        $this->recalculateAttempt(
                            (int) $attemptId
                        );
                    }
                }
            );

            return back()->with(
                'success',
                'Выбранные ответы попыток успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового удаления school quiz attempt items: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении ответов попыток.'
            );
        }
    }

    /** Массовое обновление правильности. */
    public function bulkUpdateCorrect(
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
                'exists:school_quiz_attempt_items,id',
            ],

            'is_correct' => [
                'required',
                'boolean',
            ],
        ]);

        try {
            DB::transaction(
                function () use ($data) {
                    $items =
                        SchoolQuizAttemptItem::query()
                            ->with([
                                'question',
                            ])
                            ->whereIn(
                                'id',
                                $data['ids']
                            )
                            ->get();

                    $attemptIds = [];

                    foreach ($items as $item) {
                        $item->update([
                            'is_correct' =>
                                (bool) $data[
                                'is_correct'
                                ],
                        ]);

                        $attemptIds[] =
                            $item
                                ->school_quiz_attempt_id;
                    }

                    foreach (
                        array_unique(
                            $attemptIds
                        )
                        as $attemptId
                    ) {
                        $this->recalculateAttempt(
                            (int) $attemptId
                        );
                    }
                }
            );

            return back()->with(
                'success',
                'Правильность выбранных текстовых ответов обновлена.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового обновления правильности school quiz attempt items: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом обновлении правильности ответов.'
            );
        }
    }

    /**
     * =========================================================
     * SELECT HELPERS
     * =========================================================
     */

    /**
     * Попытки для select.
     *
     * Attempt сама не переводимая.
     * Quiz загружается только
     * текущей locale.
     */
    private function attemptsForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $attempts =
            SchoolQuizAttempt::query()
                ->with([
                    'user:id,name,email',

                    'quiz' => fn ($query) =>
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

        return SchoolQuizAttemptSharedResource::collection(
            $attempts
        );
    }

    /**
     * Вопросы для select.
     *
     * Question и его Quiz:
     * только текущая locale.
     */
    private function questionsForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $questions =
            SchoolQuizQuestion::query()
                ->with([
                    'translations' =>
                        fn ($query) =>
                        $query->where(
                            'locale',
                            $locale
                        ),

                    'quiz' => fn ($query) =>
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

        return SchoolQuizQuestionSharedResource::collection(
            $questions
        );
    }

    /**
     * Ответы для select.
     *
     * Answer и его Question:
     * только текущая locale.
     */
    private function answersForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $answers =
            SchoolQuizAnswer::query()
                ->with([
                    'translations' =>
                        fn ($query) =>
                        $query->where(
                            'locale',
                            $locale
                        ),

                    'question' => fn ($query) =>
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

        return SchoolQuizAnswerSharedResource::collection(
            $answers
        );
    }

    /**
     * =========================================================
     * ATTEMPT RECALCULATION
     * =========================================================
     */

    /** Пересчёт результата попытки. */
    private function recalculateAttempt(
        int $attemptId
    ): void {
        $attempt =
            SchoolQuizAttempt::query()
                ->with([
                    'items',
                ])
                ->find(
                    $attemptId
                );

        if (!$attempt) {
            return;
        }

        $attempt->score =
            (int) $attempt
                ->items
                ->sum('score');

        $attempt->max_score =
            (int) $attempt
                ->items
                ->sum('max_score');

        $attempt->recalcPercent();

        $attempt->save();
    }

    /**
     * =========================================================
     * INDEX QUERIES
     * =========================================================
     */

    /**
     * Лёгкий запрос для count().
     */
    private function countQuery(
        null|string|int $attemptId = null,
        null|string|int $questionId = null,
        null|string|bool $isCorrect = null,
    ): Builder {
        return SchoolQuizAttemptItem::query()
            ->when(
                $attemptId,
                fn (Builder $query) =>
                $query->where(
                    'school_quiz_attempt_id',
                    (int) $attemptId
                )
            )
            ->when(
                $questionId,
                fn (Builder $query) =>
                $query->where(
                    'school_quiz_question_id',
                    (int) $questionId
                )
            )
            ->when(
                $isCorrect !== null
                && $isCorrect !== '',
                fn (Builder $query) =>
                $query->where(
                    'is_correct',
                    filter_var(
                        $isCorrect,
                        FILTER_VALIDATE_BOOL
                    )
                )
            );
    }

    /**
     * Базовый запрос для Admin Index.
     */
    private function indexQuery(
        string $locale,
        null|string|int $attemptId = null,
        null|string|int $questionId = null,
        null|string|bool $isCorrect = null,
    ): Builder {
        return $this->countQuery(
            attemptId: $attemptId,
            questionId: $questionId,
            isCorrect: $isCorrect,
        )
            ->with([
                /**
                 * Attempt:
                 * User + Quiz locale.
                 */
                'attempt' => fn ($query) =>
                $query->with([
                    'user:id,name,email',

                    'quiz' => fn ($quizQuery) =>
                    $quizQuery->with([
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
                 * Question:
                 * locale +
                 * answers locale.
                 *
                 * answers нужны Resource
                 * для selected_answer_ids.
                 */
                'question' => fn ($query) =>
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

                    'answers' => fn ($answerQuery) =>
                    $answerQuery->with([
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
                 * Single choice Answer.
                 */
                'selectedAnswer' => fn ($query) =>
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
            ]);
    }

    /**
     * Получение списка Item
     * по активному processing mode.
     */
    private function getIndexItems(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
        null|string|int $attemptId = null,
        null|string|int $questionId = null,
        null|string|bool $isCorrect = null,
    ) {
        $query = $this->indexQuery(
            locale: $locale,
            attemptId: $attemptId,
            questionId: $questionId,
            isCorrect: $isCorrect,
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
