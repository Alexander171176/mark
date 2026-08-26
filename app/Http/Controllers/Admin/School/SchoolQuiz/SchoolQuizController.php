<?php

namespace App\Http\Controllers\Admin\School\SchoolQuiz;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolQuiz\SchoolQuizRequest;
use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use App\Http\Resources\Admin\School\SchoolQuiz\SchoolQuizResource;
use App\Http\Resources\Admin\School\SchoolQuiz\SchoolQuizSharedResource;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\Admin\School\SchoolQuiz\SchoolQuiz;
use App\Models\Admin\School\SchoolQuiz\SchoolQuizImage;
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
 * Контроллер для управления Викторинами (SchoolQuizzes) в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное и массовое)
 * - клонирование
 * - мультиязычность, изображения
 * - связи с курсами, модулями, уроками.
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolQuiz
 * @see SchoolQuizRequest
 */
class SchoolQuizController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolQuiz::class;

    /** Модель изображений */
    protected string $imageModelClass = SchoolQuizImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для уведомлений */
    protected string $entityLabel = 'квизов';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'short',
        'description',
    ];

    /** Список квизов. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $settings = app(
            AdminSettingsService::class
        );

        $perPage = $settings->int(
            'adminSchoolQuizzesPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolQuizzesDefaultSort',
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
            'adminSchoolQuizzesProcessingMode',
            'frontend'
        );

        $quizzesCount = $this->baseQuery()
            ->count();

        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $quizzesCount,
            300
        );

        try {
            /**
             * Вот здесь используется
             * getIndexQuizzes().
             */
            $quizzes = $this->getIndexQuizzes(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render(
                'Admin/School/SchoolQuizzes/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolQuizzesPerPage' =>
                        $perPage,

                    'adminSchoolQuizzesDefaultSort' =>
                        $defaultSort,

                    'adminSchoolQuizzesProcessingMode' =>
                        $processingMode,

                    /**
                     * Index использует
                     * краткий Shared Resource.
                     */
                    'quizzes' =>
                        SchoolQuizSharedResource::collection(
                            $quizzes
                        ),

                    'quizzesCount' =>
                        $quizzesCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка school quizzes: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolQuizzes/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolQuizzesPerPage' =>
                        $perPage,

                    'adminSchoolQuizzesDefaultSort' =>
                        $defaultSort,

                    'adminSchoolQuizzesProcessingMode' =>
                        $processingMode,

                    'quizzes' =>
                        [],

                    'quizzesCount' =>
                        0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'error' =>
                        'Ошибка загрузки квизов.',
                ]
            );
        }
    }

    /** Страница создания квиза. */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/School/SchoolQuizzes/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

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

    /** Сохранение нового квиза. */
    public function store(SchoolQuizRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages']
        );

        try {
            DB::transaction(function () use ($request, $data, $translations, $imagesData) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = SchoolQuiz::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                $quiz = SchoolQuiz::create($data);

                $this->syncTranslations($quiz, $translations);
                $this->syncImages($quiz, $request, $imagesData);
            });

            return redirect()
                ->route('admin.schoolQuizzes.index')
                ->with('success', 'Квиз успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка создания school quiz: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании квиза.');
        }
    }

    /** Редирект на страницу редактирования. */
    public function show(int $schoolQuiz): RedirectResponse
    {
        return redirect()->route('admin.schoolQuizzes.edit', $schoolQuiz);
    }

    /** Страница редактирования квиза. */
    public function edit(
        int $schoolQuiz,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $quiz = $this->baseQuery()
            ->with([
                /**
                 * Edit требует все переводы квиза.
                 *
                 * Отдельная relation translation
                 * больше не нужна.
                 */
                'translations',

                /**
                 * Изображения + Spatie Media
                 * одним пакетным eager loading.
                 */
                'images.media',

                /**
                 * Курс:
                 * только выбранная локаль.
                 */
                'course' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),
                ]),

                /**
                 * Модуль:
                 * только выбранная локаль.
                 */
                'module' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),
                ]),

                /**
                 * Урок:
                 * только выбранная локаль.
                 */
                'lesson' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),
                ]),

                /**
                 * Вопросы:
                 * только выбранный перевод.
                 */
                'questions' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),
                ]),

                'attempts',
            ])
            ->withCount([
                'questions',
                'attempts',
                'images',
            ])
            ->findOrFail(
                $schoolQuiz
            );

        return Inertia::render(
            'Admin/School/SchoolQuizzes/Edit',
            [
                'quiz' =>
                    new SchoolQuizResource(
                        $quiz
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

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

    /** Обновление квиза. */
    public function update(SchoolQuizRequest $request, int $schoolQuiz): RedirectResponse
    {
        $quiz = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolQuiz);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['_method']
        );

        try {
            DB::transaction(function () use (
                $request,
                $quiz,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds
            ) {
                $quiz->update($data);

                $this->syncTranslations($quiz, $translations);
                $this->syncImages($quiz, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.schoolQuizzes.index')
                ->with('success', 'Квиз успешно обновлён.');
        } catch (Throwable $e) {
            Log::error('Ошибка обновления school quiz ID ' . $quiz->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении квиза.');
        }
    }

    /** Удаление квиза. */
    public function destroy(int $schoolQuiz): RedirectResponse
    {
        $quiz = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolQuiz);

        try {
            DB::transaction(function () use ($quiz) {
                $imageIds = $quiz->images()
                    ->pluck('school_quiz_images.id')
                    ->toArray();

                if (!empty($imageIds)) {
                    $quiz->images()->detach();
                    $this->deleteImages($imageIds);
                }

                $quiz->translations()->delete();

                $quiz->delete();
            });

            return redirect()
                ->route('admin.schoolQuizzes.index')
                ->with('success', 'Квиз успешно удалён.');
        } catch (Throwable $e) {
            Log::error('Ошибка удаления school quiz ID ' . $quiz->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении квиза.');
        }
    }

    /** Массовое удаление квизов. */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:school_quizzes,id'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                $quizzes = SchoolQuiz::query()
                    ->with('images')
                    ->whereIn('id', $data['ids'])
                    ->get();

                foreach ($quizzes as $quiz) {
                    $imageIds = $quiz->images()
                        ->pluck('school_quiz_images.id')
                        ->toArray();

                    if (!empty($imageIds)) {
                        $quiz->images()->detach();
                        $this->deleteImages($imageIds);
                    }

                    $quiz->translations()->delete();
                    $quiz->delete();
                }
            });

            return back()->with('success', 'Выбранные квизы успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка массового удаления school quizzes: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении квизов.');
        }
    }

    /** Клонирование квиза. */
    public function clone(Request $request, int $schoolQuiz): RedirectResponse
    {
        $quiz = $this->baseQuery()
            ->with([
                'translations',
                'images.media',
            ])
            ->findOrFail(
                $schoolQuiz
            );

        try {
            DB::transaction(function () use ($quiz) {
                $clone = $quiz->replicate([
                    'created_at',
                    'updated_at',
                ]);

                $baseSlug = $quiz->slug;
                $newSlug = $baseSlug . '-copy';
                $counter = 2;

                while (SchoolQuiz::query()->where('slug', $newSlug)->exists()) {
                    $newSlug = $baseSlug . '-copy-' . $counter;
                    $counter++;
                }

                $clone->slug = $newSlug;
                $clone->activity = false;
                $clone->left = false;
                $clone->main = false;
                $clone->right = false;
                $clone->published_at = null;
                $clone->save();

                foreach ($quiz->translations as $translation) {
                    $clonedTranslation = $translation->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                    $clonedTranslation->school_quiz_id = $clone->id;
                    $clonedTranslation->title = $clonedTranslation->title . ' (копия)';
                    $clonedTranslation->save();
                }

                $imageSyncData = [];

                foreach ($quiz->images as $image) {
                    $clonedImage = $image->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                    $clonedImage->save();

                    $media = $image->getFirstMedia($this->imageMediaCollection);

                    if ($media) {
                        $media->copy($clonedImage, $this->imageMediaCollection);
                    }

                    $order = $image->pivot->order ?? $image->order ?? 0;
                    $imageSyncData[$clonedImage->id] = ['order' => $order];
                }

                if (!empty($imageSyncData)) {
                    $clone->images()->sync($imageSyncData);
                }
            });

            return redirect()
                ->route('admin.schoolQuizzes.index')
                ->with('success', 'Квиз успешно клонирован.');
        } catch (Throwable $e) {
            Log::error('Ошибка клонирования school quiz ID ' . $quiz->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при клонировании квиза.');
        }
    }

    /** Список курсов для select. */
    private function coursesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $courses = SchoolCourse::query()
            ->with([
                'translations' => fn ($query) =>
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

    /** Список модулей для select. */
    private function modulesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $modules = SchoolModule::query()
            ->with([
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                'course' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
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

    /** Список уроков для select. */
    private function lessonsForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $lessons = SchoolLesson::query()
            ->with([
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                'module' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $locale
                    ),

                    'course' => fn ($courseQuery) =>
                    $courseQuery->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->where(
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

    /** Базовый запрос для Admin Index квизов. */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                /**
                 * Admin Index:
                 * только выбранная локаль.
                 *
                 * Все переводы здесь не нужны.
                 */
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                /**
                 * Изображения квиза +
                 * Spatie Media пакетным запросом.
                 */
                'images.media',

                /**
                 * Курс.
                 */
                'course' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $locale
                    ),
                ]),

                /**
                 * Модуль.
                 */
                'module' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $locale
                    ),
                ]),

                /**
                 * Урок.
                 */
                'lesson' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $locale
                    ),
                ]),
            ])
            ->withCount([
                'questions',
                'attempts',
                'images',
            ]);
    }

    /** Получение списка квизов по активному режиму обработки. */
    private function getIndexQuizzes(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery(
            $locale
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
