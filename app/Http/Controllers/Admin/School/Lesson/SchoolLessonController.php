<?php

namespace App\Http\Controllers\Admin\School\Lesson;

use App\Http\Controllers\Admin\School\Base\BaseSchoolAdminController;
use App\Http\Requests\Admin\School\Lesson\SchoolLessonRequest;
use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoSharedResource;
use App\Http\Resources\Admin\School\Hashtag\SchoolHashtagSharedResource;
use App\Http\Resources\Admin\School\Lesson\SchoolLessonResource;
use App\Http\Resources\Admin\School\Module\SchoolModuleSharedResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\School\Hashtag\SchoolHashtag;
use App\Models\Admin\School\Lesson\SchoolLesson;
use App\Models\Admin\School\Lesson\SchoolLessonImage;
use App\Models\Admin\School\Module\SchoolModule;
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

        // Текущая локаль
        $currentLocale = $this->resolveLocale($request);

        // Настройки отображения
        $adminSchoolLessonsPerPage = (int) config('site_settings.adminSchoolLessonsPerPage', 10);
        $adminSchoolLessonsDefaultSort = (string) config('site_settings.adminSchoolLessonsDefaultSort', 'idDesc');

        try {

            // Получение уроков
            $lessons = $this->baseQuery()
                ->with([
                    'translation',
                    'translations',
                    'images',
                    'module.translation',
                    'module.course.translation',
                    'hashtags.translation',
                    'content',
                ])
                ->withCount([
                    'images',
                    'likes',
                    'hashtags',
                ])
                ->get();

            // Страница списка уроков
            return Inertia::render('Admin/School/Lessons/Index', [
                'lessons' => SchoolLessonResource::collection($lessons),
                'lessonsCount' => $this->baseQuery()->count(),

                'adminSchoolLessonsPerPage' => $adminSchoolLessonsPerPage,
                'adminSchoolLessonsDefaultSort' => $adminSchoolLessonsDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
            ]);

        } catch (Throwable $e) {

            // Логирование ошибок
            Log::error('Ошибка загрузки списка school lessons: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            // Возврат пустой страницы при ошибке
            return Inertia::render('Admin/School/Lessons/Index', [
                'lessons' => [],
                'lessonsCount' => 0,

                'adminSchoolLessonsPerPage' => $adminSchoolLessonsPerPage,
                'adminSchoolLessonsDefaultSort' => $adminSchoolLessonsDefaultSort,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'error' => 'Ошибка загрузки уроков.',
            ]);
        }
    }

    /** Страница создания урока */
    public function create(Request $request): Response
    {

        $currentLocale = $this->resolveLocale($request);

        return Inertia::render('Admin/School/Lessons/Create', [

            // Локали
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            // Данные для select
            'modules' => $this->modulesForSelect(),
            'hashtags' => $this->hashtagsForSelect(),

            // Контент для привязки
            'articles' => $this->articlesForSelect(),
            'videos' => $this->videosForSelect(),
        ]);
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
    public function edit(int $schoolLesson, Request $request): Response
    {

        $currentLocale = $this->resolveLocale($request);

        // Получение урока
        $lesson = $this->baseQuery()
            ->with([
                'translation',
                'translations',
                'images',
                'module.translation',
                'module.course.translation',
                'hashtags.translation',
                'content',
            ])
            ->withCount([
                'images',
                'likes',
                'hashtags',
            ])
            ->findOrFail($schoolLesson);

        return Inertia::render('Admin/School/Lessons/Edit', [

            // Текущий урок
            'lesson' => new SchoolLessonResource($lesson),

            // Локали
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            // Данные для select
            'modules' => $this->modulesForSelect(),
            'hashtags' => $this->hashtagsForSelect(),

            // Контент для привязки
            'articles' => $this->articlesForSelect(),
            'videos' => $this->videosForSelect(),
        ]);
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
    private function modulesForSelect(): AnonymousResourceCollection
    {

        // Получение модулей
        $modules = SchoolModule::query()
            ->with([
                'translation',
                'translations',
                'images',
                'course.translation',
            ])
            ->get();

        return SchoolModuleSharedResource::collection($modules);
    }

    /** Хэштеги для select */
    private function hashtagsForSelect(): AnonymousResourceCollection
    {

        // Получение хэштегов
        $hashtags = SchoolHashtag::query()
            ->with([
                'translation',
                'translations',
            ])
            ->get();

        return SchoolHashtagSharedResource::collection($hashtags);
    }

    /** Статьи для select */
    private function articlesForSelect(): AnonymousResourceCollection
    {

        // Получение статей
        $articles = BlogArticle::query()
            ->with([
                'translations',
                'images',
            ])
            ->get();

        return BlogArticleSharedResource::collection($articles);
    }

    /** Видео для select */
    private function videosForSelect(): AnonymousResourceCollection
    {

        // Получение видео
        $videos = BlogVideo::query()
            ->with([
                'translations',
                'images',
            ])
            ->get();

        return BlogVideoSharedResource::collection($videos);
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
}
