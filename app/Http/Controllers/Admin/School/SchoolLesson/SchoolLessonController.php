<?php

namespace App\Http\Controllers\Admin\School\SchoolLesson;

use App\Http\Controllers\Admin\School\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\SchoolLesson\SchoolLessonRequest;
use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoSharedResource;
use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolLesson\SchoolLessonImage;
use App\Models\Admin\School\SchoolModule\SchoolModule;
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
 * Контроллер для управления Уроками курсов в административной панели.
 *
 * CRUD +:
 * - обновление активности (одиночное и массовое)
 * - обновление сортировки (одиночное и массовое)
 * - удаление (одиночное)
 * - мультиязычность, изображения
 * - связи с треками, хештегами и связанными курсами.
 *
 * @version 1.1 (мультиязычная архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see SchoolLesson
 * @see SchoolLessonRequest
 */
class SchoolLessonController extends BaseSchoolAdminController
{
    /** Основная модель */
    protected string $modelClass = SchoolLesson::class;

    /** Модель изображений */
    protected string $imageModelClass = SchoolLessonImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности */
    protected string $entityLabel = 'уроков';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Список уроков */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminSchoolLessonsPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminSchoolLessonsDefaultSort',
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
            'adminSchoolLessonsProcessingMode',
            'frontend'
        );

        /**
         * Общий COUNT заранее нужен
         * только режиму auto.
         *
         * server/frontend уже сами
         * однозначно определяют способ обработки.
         */
        $lessonsCount = null;

        if ($processingMode === 'auto') {
            $lessonsCount = $this->baseQuery()
                ->count();

            $useServerProcessing = app(
                ProcessingModeService::class
            )->shouldUseServer(
                $processingMode,
                $lessonsCount,
                300
            );
        } else {
            $useServerProcessing =
                $processingMode === 'server';
        }

        try {
            $lessons = $this->getIndexLessons(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            /**
             * Если COUNT ещё не был получен
             * режимом auto, определяем его
             * наиболее дешёвым способом.
             */
            if ($lessonsCount === null) {
                if (!$useServerProcessing) {
                    /**
                     * Frontend получил всю коллекцию.
                     *
                     * Отдельный SQL COUNT не нужен.
                     */
                    $lessonsCount = $lessons->count();
                } elseif ($search === '') {
                    /**
                     * Server без поиска.
                     *
                     * paginate() уже выполнил COUNT
                     * и знает общее количество.
                     */
                    $lessonsCount = $lessons->total();
                } else {
                    /**
                     * Server + поиск.
                     *
                     * paginator total содержит
                     * количество найденных записей,
                     * а lessonsCount должен оставаться
                     * общим количеством уроков.
                     */
                    $lessonsCount = $this->baseQuery()
                        ->count();
                }
            }

            return Inertia::render(
                'Admin/School/SchoolLessons/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolLessonsPerPage' =>
                        $perPage,

                    'adminSchoolLessonsDefaultSort' =>
                        $defaultSort,

                    'adminSchoolLessonsProcessingMode' =>
                        $processingMode,

                    /**
                     * Admin Index использует
                     * краткий Shared Resource.
                     */
                    'lessons' =>
                        SchoolLessonSharedResource::collection(
                            $lessons
                        ),

                    'lessonsCount' =>
                        $lessonsCount,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка school lessons: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/School/SchoolLessons/Index',
                [
                    'currentLocale' =>
                        $currentLocale,

                    'availableLocales' =>
                        $this->availableLocales(),

                    'useServerProcessing' =>
                        $useServerProcessing,

                    'adminSchoolLessonsPerPage' =>
                        $perPage,

                    'adminSchoolLessonsDefaultSort' =>
                        $defaultSort,

                    'adminSchoolLessonsProcessingMode' =>
                        $processingMode,

                    'lessons' =>
                        [],

                    'lessonsCount' =>
                        0,

                    'sortParam' =>
                        $sortParam,

                    'search' =>
                        $search,

                    'error' =>
                        'Ошибка загрузки уроков.',
                ]
            );
        }
    }

    /** Страница создания урока */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        return Inertia::render(
            'Admin/School/SchoolLessons/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'modules' =>
                    $this->modulesForSelect(
                        $currentLocale
                    ),

                'hashtags' =>
                    $this->hashtagsForSelect(
                        $currentLocale
                    ),

                'articles' =>
                    $this->articlesForSelect(
                        $currentLocale
                    ),

                'videos' =>
                    $this->videosForSelect(
                        $currentLocale
                    ),
            ]
        );
    }

    /** Сохранение урока */
    public function store(SchoolLessonRequest $request): RedirectResponse
    {

        // Валидированные данные
        $data = $request->validated();

        // Переводы
        $translations = $data['translations'] ?? [];

        // Изображения
        $imagesData = $data['images'] ?? [];

        // Хэштеги
        $hashtagIds = $data['hashtag_ids'] ?? [];

        // Удаление служебных полей
        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['hashtag_ids']
        );

        // Очистка пустой связи контента
        if (empty($data['content_type']) || empty($data['content_id'])) {
            $data['content_type'] = null;
            $data['content_id'] = null;
        }

        try {

            DB::transaction(function () use (
                $request,
                $data,
                $translations,
                $imagesData,
                $hashtagIds
            ) {

                // Автоматическая сортировка
                if (!isset($data['sort']) || is_null($data['sort'])) {

                    $maxSort = SchoolLesson::query()
                        ->where('school_module_id', $data['school_module_id'])
                        ->max('sort');

                    $data['sort'] = is_null($maxSort) ? 1 : $maxSort + 1;
                }

                // Создание урока
                $lesson = SchoolLesson::create($data);

                // Сохранение переводов
                $this->syncTranslations($lesson, $translations);

                // Сохранение изображений
                $this->syncImages($lesson, $request, $imagesData);

                // Синхронизация хэштегов
                $lesson->hashtags()->sync($hashtagIds);
            });

            return redirect()
                ->route('admin.schoolLessons.index')
                ->with('success', 'Урок успешно создан.');

        } catch (Throwable $e) {

            // Логирование ошибок
            Log::error('Ошибка создания school lesson: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании урока.');
        }
    }

    /** Редирект на редактирование */
    public function show(int $schoolLesson): RedirectResponse
    {
        return redirect()->route('admin.schoolLessons.edit', $schoolLesson);
    }

    /** Страница редактирования урока */
    public function edit(
        int $schoolLesson,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        $lesson = $this->baseQuery()
            ->with([
                /**
                 * Все переводы самого урока
                 * нужны TranslationTabs формы.
                 */
                'translations',

                /**
                 * Изображения + Spatie Media.
                 */
                'images.media',

                /**
                 * Родительский модуль.
                 */
                'module' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),

                    'course' => fn ($courseQuery) =>
                    $courseQuery->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->where(
                            'locale',
                            $currentLocale
                        ),
                    ]),
                ]),

                /**
                 * Связанные хештеги.
                 */
                'hashtags' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),
                ]),
            ])
            ->withCount([
                'images',
                'likes',
                'hashtags',
            ])
            ->findOrFail(
                $schoolLesson
            );

        return Inertia::render(
            'Admin/School/SchoolLessons/Edit',
            [
                'lesson' =>
                    new SchoolLessonResource(
                        $lesson
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'modules' =>
                    $this->modulesForSelect(
                        $currentLocale
                    ),

                'hashtags' =>
                    $this->hashtagsForSelect(
                        $currentLocale
                    ),

                'articles' =>
                    $this->articlesForSelect(
                        $currentLocale
                    ),

                'videos' =>
                    $this->videosForSelect(
                        $currentLocale
                    ),
            ]
        );
    }

    /** Обновление урока */
    public function update(SchoolLessonRequest $request, int $schoolLesson): RedirectResponse
    {

        // Текущий урок
        $lesson = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolLesson);

        // Валидированные данные
        $data = $request->validated();

        // Переводы
        $translations = $data['translations'] ?? [];

        // Изображения
        $imagesData = $data['images'] ?? [];

        // Удалённые изображения
        $deletedImageIds = $data['deletedImages'] ?? [];

        // Хэштеги
        $hashtagIds = $data['hashtag_ids'] ?? [];

        // Удаление служебных полей
        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['hashtag_ids'],
            $data['_method']
        );

        // Очистка пустой связи контента
        if (empty($data['content_type']) || empty($data['content_id'])) {
            $data['content_type'] = null;
            $data['content_id'] = null;
        }

        try {

            DB::transaction(function () use (
                $request,
                $lesson,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds,
                $hashtagIds
            ) {

                // Обновление урока
                $lesson->update($data);

                // Сохранение переводов
                $this->syncTranslations($lesson, $translations);

                // Синхронизация изображений
                $this->syncImages($lesson, $request, $imagesData, $deletedImageIds);

                // Синхронизация хэштегов
                $lesson->hashtags()->sync($hashtagIds);
            });

            return redirect()
                ->route('admin.schoolLessons.index')
                ->with('success', 'Урок успешно обновлён.');

        } catch (Throwable $e) {

            // Логирование ошибок
            Log::error('Ошибка обновления school lesson ID ' . $lesson->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении урока.');
        }
    }

    /** Удаление урока */
    public function destroy(int $schoolLesson): RedirectResponse
    {

        // Текущий урок
        $lesson = $this->baseQuery()
            ->with('images')
            ->findOrFail($schoolLesson);

        try {

            DB::transaction(function () use ($lesson) {

                // ID изображений
                $imageIds = $lesson->images()
                    ->pluck('school_lesson_images.id')
                    ->toArray();

                // Удаление изображений
                if (!empty($imageIds)) {

                    $lesson->images()->detach();

                    $this->deleteImages($imageIds);
                }

                // Удаление переводов
                $lesson->translations()->delete();

                // Удаление связей
                $lesson->hashtags()->detach();

                // Удаление лайков
                $lesson->likes()->delete();

                // Удаление урока
                $lesson->delete();
            });

            return redirect()
                ->route('admin.schoolLessons.index')
                ->with('success', 'Урок успешно удалён.');

        } catch (Throwable $e) {

            // Логирование ошибок
            Log::error('Ошибка удаления school lesson ID ' . $lesson->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении урока.');
        }
    }

    /** Модули для select */
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
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            )
            ->get();

        return SchoolModuleSharedResource::collection(
            $modules
        );
    }

    /** Хештеги для select */
    private function hashtagsForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $hashtags = SchoolHashtag::query()
            ->with([
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),
            ])
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            )
            ->get();

        return SchoolHashtagSharedResource::collection(
            $hashtags
        );
    }

    /** Статьи для select */
    private function articlesForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $articles = BlogArticle::query()
            ->with([
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),
            ])
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            )
            ->get();

        return BlogArticleSharedResource::collection(
            $articles
        );
    }

    /** Видео для select */
    private function videosForSelect(
        string $locale
    ): AnonymousResourceCollection {
        $videos = BlogVideo::query()
            ->with([
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),
            ])
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            )
            ->get();

        return BlogVideoSharedResource::collection(
            $videos
        );
    }

    /** Клонирование урока */
    public function clone(int $schoolLesson): RedirectResponse
    {
        $lesson = $this->baseQuery()
            ->with([
                'translations',
                'images',
                'hashtags',
            ])
            ->findOrFail($schoolLesson);

        try {
            DB::transaction(function () use ($lesson) {
                // Копируем основные поля урока
                $clone = $lesson->replicate([
                    'views',
                    'likes',
                    'created_at',
                    'updated_at',
                ]);

                $clone->slug = $lesson->slug . '-copy-' . time();
                $clone->sort = ((int) SchoolLesson::where('school_module_id', $lesson->school_module_id)->max('sort')) + 1;

                // Метрики лучше сбрасывать
                $clone->views = 0;
                $clone->likes = 0;
                $clone->popularity = 0;
                $clone->rating_count = 0;
                $clone->rating_avg = 0;

                $clone->save();

                // Копируем переводы
                foreach ($lesson->translations as $translation) {
                    $newTranslation = $translation->replicate([
                        'created_at',
                        'updated_at',
                    ]);

                    $newTranslation->school_lesson_id = $clone->id;
                    $newTranslation->title = $translation->title . ' copy';

                    $newTranslation->save();
                }

                // Копируем хэштеги
                $clone->hashtags()->sync(
                    $lesson->hashtags->pluck('id')->toArray()
                );

                // Копируем связи с изображениями
                foreach ($lesson->images as $image) {
                    $clone->images()->attach($image->id, [
                        'order' => $image->pivot->order ?? 0,
                    ]);
                }
            });

            return redirect()
                ->route('admin.schoolLessons.index')
                ->with('success', 'Урок успешно клонирован.');
        } catch (Throwable $e) {
            Log::error('Ошибка клонирования school lesson ID ' . $lesson->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при клонировании урока.');
        }
    }

    /** Базовый запрос для Admin Index уроков. */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                /**
                 * Admin Index:
                 * только выбранная локаль.
                 */
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                /**
                 * Изображения +
                 * Spatie Media.
                 */
                'images.media',

                /**
                 * Родительский модуль.
                 */
                'module' => fn ($query) =>
                $query->with([
                    /**
                     * Только выбранная локаль
                     * модуля.
                     */
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $locale
                    ),

                    /**
                     * Родительский курс модуля.
                     */
                    'course' => fn ($courseQuery) =>
                    $courseQuery->with([
                        'translations' => fn ($translationQuery) =>
                        $translationQuery->where(
                            'locale',
                            $locale
                        ),
                    ]),
                ]),

                /**
                 * Хештеги нужны frontend-поиску.
                 */
                'hashtags' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $locale
                    ),
                ]),
            ])
            ->withCount([
                'images',
                'likes',
                'hashtags',
            ]);
    }

    /** Получение списка уроков по активному режиму обработки. */
    private function getIndexLessons(
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
