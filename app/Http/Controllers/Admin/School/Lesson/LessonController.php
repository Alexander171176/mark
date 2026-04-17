<?php

namespace App\Http\Controllers\Admin\School\Lesson;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\School\Lesson\LessonRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Blog\Article\ArticleResource;
use App\Http\Resources\Admin\Blog\Video\VideoResource;
use App\Http\Resources\Admin\School\Hashtag\HashtagResource;
use App\Http\Resources\Admin\School\Lesson\LessonResource;
use App\Http\Resources\Admin\School\Module\ModuleResource;
use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Video\Video;
use App\Models\Admin\School\Hashtag\Hashtag;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Lesson\LessonImage;
use App\Models\Admin\School\Module\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
 * - клонирование
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see Lesson
 * @see LessonRequest
 */
class LessonController extends Controller
{
    /**
     * Разрешённые локали
     *
     * @var array|string[]
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     *  Отображение списка.
     *  Загружает пагинированный список с сортировкой по настройкам.
     *  Передает данные для отображения и настройки пагинации/сортировки.
     *  Пагинация и сортировка выполняются на фронтенде.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountLearningLessons = (int) config('site_settings.AdminCountLearningLessons', 10);
        $adminSortLearningLessons  = config('site_settings.AdminSortLearningLessons', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        $lessons      = collect();
        $lessonsCount = 0;

        try {
            $lessons = Lesson::query()
                ->byLocale($currentLocale)
                ->with([
                    'module' => fn ($q) => $q
                        ->select('id', 'course_id', 'title', 'slug', 'locale')
                        ->with([
                            'course:id,title,slug,locale',
                        ]),
                    'images' => fn ($q) => $q->orderBy('lesson_has_images.order'),
                    'hashtags:id,name,slug,color',
                ])
                ->withCount([
                    'hashtags',
                ])
                ->orderBy('sort')
                ->get();

            $lessonsCount = Lesson::query()
                ->byLocale($currentLocale)
                ->count();

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки уроков (locale: {$currentLocale}): ".$e->getMessage(), [
                'exception' => $e,
            ]);
            session()->flash('error', __('admin/controllers.index_error'));
        }

        return Inertia::render('Admin/School/Lessons/Index', [
            'lessons'                   => LessonResource::collection($lessons)->resolve(),
            'lessonsCount'              => $lessonsCount,
            'adminCountLearningLessons' => $adminCountLearningLessons,
            'adminSortLearningLessons'  => $adminSortLearningLessons,
            'currentLocale'             => $currentLocale,
            'availableLocales'          => $this->availableLocales,
        ]);
    }

    /**
     * Форма создания урока.
     * Передаём списки: модули, теги.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // TODO: $this->authorize('create-lessons', Lesson::class);

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        // Модули всех локалей с курсом
        $modules = Module::query()
            ->with(['course:id,title,locale'])
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'locale', 'course_id']);

        // Хэштеги (обучающие теги) — только текущая локаль
        $hashtags = Hashtag::query()
            ->where('locale', $currentLocale)
            ->orderBy('sort')
            ->get();

        $articles = Article::query()
            ->byLocale($currentLocale)
            ->orderBy('id', 'desc')
            ->get(['id', 'title']);

        $videos = Video::query()
            ->byLocale($currentLocale)
            ->orderBy('id', 'desc')
            ->get(['id', 'title']);

        return Inertia::render('Admin/School/Lessons/Create', [
            'modules'       => ModuleResource::collection($modules),
            'hashtags'      => HashtagResource::collection($hashtags),
            'articles'      => ArticleResource::collection($articles),
            'videos'        => VideoResource::collection($videos),
            'currentLocale' => $currentLocale,
        ]);
    }

    /**
     * Сохранение нового урока.
     * - LessonRequest: валидация + нормализация полей
     * - обработка изображений (Spatie, LessonImage)
     * - синхронизация хештегов
     *
     * @param LessonRequest $request
     * @return RedirectResponse
     */
    public function store(LessonRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // изображения для урока
        $imagesData = $data['images'] ?? [];

        // хештеги забираем из инпута
        $hashtagIds = $request->input('hashtag_ids', []);

        unset(
            $data['images'],
            $data['deletedImages'],
            $data['hashtag_ids'],
        );

        try {
            DB::beginTransaction();

            /** @var Lesson $lesson */
            $lesson = Lesson::create($data);

            // 🔹 Синхронизация хештегов
            if (!empty($hashtagIds)) {
                $lesson->hashtags()->sync($hashtagIds);
            }

            // 🔹 Обработка изображений (паттерн как у курсов/модулей)
            $imageSyncData = [];
            $imageIndex    = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                if ($request->hasFile($fileKey)) {
                    $image = LessonImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    try {
                        $file = $request->file($fileKey);

                        if ($file && $file->isValid()) {
                            $image
                                ->addMedia($file)
                                ->toMediaCollection('images');

                            $imageSyncData[$image->id] = ['order' => $image->order];
                        } else {
                            Log::warning("Недопустимый файл изображения (урок ID {$lesson->id}), индекс {$imageIndex}", [
                                'fileKey' => $fileKey,
                                'error'   => $file?->getErrorMessage(),
                            ]);
                            $image->delete();
                        }
                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library для урока {$lesson->id}, индекс изображения {$imageIndex}: ".$e->getMessage(), [
                            'trace' => $e->getTraceAsString(),
                        ]);
                        $image->delete();
                    }
                }

                $imageIndex++;
            }

            if (!empty($imageSyncData)) {
                $lesson->images()->sync($imageSyncData);
            }

            DB::commit();

            Log::info('Урок успешно создан', [
                'id'    => $lesson->id,
                'title' => $lesson->title,
            ]);

            return redirect()
                ->route('admin.lessons.index')
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании урока: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * show в админке — редирект на edit.
     */
    public function show(Lesson $lesson): RedirectResponse
    {
        return redirect()->route('admin.lessons.edit', $lesson);
    }

    /**
     * Форма редактирования урока.
     *
     * @param Lesson $lesson
     * @return Response
     */
    public function edit(Lesson $lesson): Response
    {
        // TODO: $this->authorize('update-lessons', $lesson);

        // Локаль урока
        $currentLocale = $lesson->locale ?? config('app.fallback_locale', 'ru');

        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_error'));
        }

        // Подгружаем нужные связи урока
        $lesson->load([
            'module' => fn ($q) => $q
                ->select('id', 'course_id', 'title', 'slug', 'locale')
                ->with([
                    'course:id,title,slug,locale',
                ]),
            'images'   => fn ($q) => $q->orderBy('lesson_has_images.order'),
            'hashtags' => fn ($q) => $q->orderBy('sort'),
        ]);

        // Модули всех локалей с курсом
        $modules = Module::query()
            ->with(['course:id,title,locale'])
            ->orderBy('locale', 'desc')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'locale', 'course_id']);

        // Хэштеги — только текущая локаль
        $hashtags = Hashtag::query()
            ->where('locale', $currentLocale)
            ->orderBy('sort')
            ->get();

        // Статьи и видео для селектора связанного контента
        $articles = Article::query()
            ->byLocale($currentLocale)
            ->orderBy('id', 'desc')
            ->get(['id', 'title']);

        $videos = Video::query()
            ->byLocale($currentLocale)
            ->orderBy('id', 'desc')
            ->get(['id', 'title']);

        return Inertia::render('Admin/School/Lessons/Edit', [
            'lesson'        => new LessonResource($lesson),
            'modules'       => ModuleResource::collection($modules),
            'hashtags'      => HashtagResource::collection($hashtags),
            'articles'      => ArticleResource::collection($articles),
            'videos'        => VideoResource::collection($videos),
            'currentLocale' => $currentLocale,
        ]);
    }

    /**
     * Обновление урока:
     * - обновление полей
     * - синхронизация хештегов
     * - обработка изображений
     *
     * @param LessonRequest $request
     * @param Lesson $lesson
     * @return RedirectResponse
     */
    public function update(LessonRequest $request, Lesson $lesson): RedirectResponse
    {
        $data = $request->validated();

        $imagesData      = $data['images']        ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        // хештеги из инпута
        $hashtagIds = $request->input('hashtag_ids', []);

        // Страховка: если один из content_* пустой — обнуляем оба
        if (empty($data['content_type']) || empty($data['content_id'])) {
            $data['content_type'] = null;
            $data['content_id']   = null;
        }

        unset(
            $data['images'],
            $data['deletedImages'],
            $data['hashtag_ids'],
            $data['_method']
        );

        try {
            DB::beginTransaction();

            // 1) Удаляем выбранные изображения (и их медиа)
            if (!empty($deletedImageIds)) {
                $lesson->images()->detach($deletedImageIds);
                $this->deleteImages($deletedImageIds);
            }

            // 2) Обновляем поля урока
            $lesson->update($data);

            // 3) Синхронизируем хештеги
            if (!empty($hashtagIds)) {
                $lesson->hashtags()->sync($hashtagIds);
            } else {
                $lesson->hashtags()->detach();
            }

            // 4) Обработка изображений
            $syncData = [];

            foreach ($imagesData as $index => $imageData) {
                $fileKey = "images.{$index}.file";

                // Обновление существующего изображения
                if (!empty($imageData['id'])) {
                    $img = LessonImage::find($imageData['id']);

                    if ($img && !in_array($img->id, $deletedImageIds, true)) {
                        $img->update([
                            'order'   => $imageData['order']   ?? $img->order,
                            'alt'     => $imageData['alt']     ?? $img->alt,
                            'caption' => $imageData['caption'] ?? $img->caption,
                        ]);

                        if ($request->hasFile($fileKey)) {
                            $img->clearMediaCollection('images');
                            $img->addMedia($request->file($fileKey))
                                ->toMediaCollection('images');
                        }

                        $syncData[$img->id] = ['order' => $img->order];
                    }

                    // Новое изображение
                } elseif ($request->hasFile($fileKey)) {
                    $new = LessonImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    $new->addMedia($request->file($fileKey))
                        ->toMediaCollection('images');

                    $syncData[$new->id] = ['order' => $new->order];
                }
            }

            // 5) Синхронизируем pivot-таблицу изображений
            $lesson->images()->sync($syncData);

            DB::commit();

            Log::info('Урок обновлён', [
                'id'    => $lesson->id,
                'title' => $lesson->title,
            ]);

            return redirect()
                ->route('admin.lessons.index')
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при обновлении урока ID {$lesson->id}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'server' => __('admin/controllers.updated_error'),
                ]);
        }
    }

    /**
     * Удаление урока вместе с изображениями.
     *
     * @param Lesson $lesson
     * @return RedirectResponse
     */
    public function destroy(Lesson $lesson): RedirectResponse
    {
        // TODO: $this->authorize('delete-lessons', $lesson);

        try {
            DB::beginTransaction();

            $imageIds = $lesson->images()->pluck('lesson_images.id')->toArray();
            $lesson->images()->detach();
            $this->deleteImages($imageIds);

            $lesson->delete();

            DB::commit();

            Log::info('Урок удалён', ['id' => $lesson->id]);

            return redirect()
                ->route('admin.lessons.index')
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении урока ID {$lesson->id}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Обновление статуса активности одного урока.
     *
     * @param UpdateActivityRequest $request
     * @param Lesson $lesson
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, Lesson $lesson): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $lesson->activity = $validated['activity'];
            $lesson->save();

            DB::commit();

            Log::info("Обновлено activity урока ID {$lesson->id} на {$lesson->activity}");

            return back()
                ->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка обновления активности урока (ID: {$lesson->id}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности уроков.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => 'required|array',
            'ids.*'    => 'integer|exists:lessons,id',
            'activity' => 'required|boolean',
        ]);

        $ids      = $validated['ids'];
        $activity = $validated['activity'];

        if (empty($ids)) {
            $message = __('admin/controllers.bulk_updated_activity_no_selection');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 400);
            }

            return back()->with('warning', $message);
        }

        try {
            $updatedCount = Lesson::whereIn('id', $ids)->update(['activity' => $activity]);
            $message      = __('admin/controllers.bulk_activity_updated_success');

            Log::info($message, ['ids' => $ids, 'activity' => $activity]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message'      => $message,
                    'updatedCount' => $updatedCount,
                ]);
            }

            return back()->with('success', $message);

        } catch (Throwable $e) {
            Log::error("Ошибка при массовом обновлении активности уроков: ".$e->getMessage(), [
                'exception' => $e,
                'ids'       => $ids,
            ]);

            $errorMessage = __('admin/controllers.bulk_activity_updated_error');

            if ($request->expectsJson()) {
                return response()->json(['message' => $errorMessage], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }

    /**
     * Обновление сортировки одного урока.
     *
     * @param UpdateSortEntityRequest $request
     * @param Lesson $lesson
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, Lesson $lesson): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $originalSort = $lesson->sort;
            $lesson->sort = (int) $validated['sort'];
            $lesson->save();

            Log::info("Сортировка урока '{$lesson->title}' (ID: {$lesson->id}) изменена с {$originalSort} на {$lesson->sort}");

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении сортировки урока (ID: {$lesson->id}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки уроков.
     * Ожидает массив:
     * lessons: [{id: 1, sort: 10}, ...]
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'lessons'        => ['required', 'array'],
            'lessons.*.id'   => ['required', 'integer', 'exists:lessons,id'],
            'lessons.*.sort' => ['required', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($validated) {
                foreach ($validated['lessons'] as $row) {
                    Lesson::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка массового обновления сортировки уроков: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            $msg = __('admin/controllers.bulk_sort_updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Клонирование урока.
     * Копирует основные поля и связи.
     * Генерирует новые уникальные title и slug.
     *
     * @param Request $request (Не используется, но нужен для сигнатуры маршрута)
     * @param int $lesson Модель урока для клонирования (через RMB).
     * @return RedirectResponse
     */
    public function clone(Request $request, int $lesson): RedirectResponse
    {
        // TODO: $this->authorize('create-lessons', Lesson::class);

        // Грузим урок вместе с soft-deleted, плюс нужные связи
        /** @var Lesson $lesson */
        $lesson = Lesson::withTrashed()
            ->with(['images', 'hashtags'])
            ->findOrFail($lesson);

        DB::beginTransaction();

        try {
            // Базовые значения для генерации title/slug
            $baseTitle = $lesson->title;
            $baseSlug  = $lesson->slug;
            $locale    = $lesson->locale ?? config('app.fallback_locale', 'ru');

            // Новый title
            $newTitle = $baseTitle . '-2';

            // Новый slug с обеспечением уникальности в рамках locale
            $suffix   = '-2';
            $newSlug  = $baseSlug . $suffix;
            $counter  = 2;

            while (
            Lesson::where('locale', $locale)
                ->where('slug', $newSlug)
                // ->whereNull('deleted_at') // раскомментируй, если нужно игнорировать soft-deleted
                ->exists()
            ) {
                $newSlug = $baseSlug . $suffix . '-' . $counter;
                $counter++;
            }

            $clonedLesson = $lesson->replicate();

            // Переопределяем то, что должно отличаться
            $clonedLesson->title         = $newTitle;
            $clonedLesson->slug          = $newSlug;
            $clonedLesson->activity      = false;
            $clonedLesson->views         = 0;
            $clonedLesson->likes         = 0;
            $clonedLesson->popularity    = 0;
            $clonedLesson->rating_count  = 0;
            $clonedLesson->rating_avg    = 0.0;
            $clonedLesson->published_at  = null;    // чтобы не считался опубликованным
            $clonedLesson->status        = 'draft'; // если у тебя такое значение используется
            $clonedLesson->created_at    = now();
            $clonedLesson->updated_at    = now();

            $clonedLesson->save();

            // 🔹 Клонируем хэштеги (polymorphic через HashtagsTrait)
            if ($lesson->relationLoaded('hashtags')) {
                $hashtagIds = $lesson->hashtags->pluck('id')->all();
            } else {
                $hashtagIds = $lesson->hashtags()->pluck('hashtags.id')->all();
            }

            if (!empty($hashtagIds)) {
                $clonedLesson->hashtags()->sync($hashtagIds);
            }

            // 🔹 Клонируем изображения
            $imageSyncData = [];

            foreach ($lesson->images as $image) {
                // Клонируем запись LessonImage
                $clonedImage = $image->replicate();
                $clonedImage->save();

                // Копируем медиафайл
                $originalMedia = $image->getFirstMedia('images');
                if ($originalMedia) {
                    try {
                        // Копируем медиа в новую запись
                        $originalMedia->copy($clonedImage, 'images');

                        // Сохраняем порядок в pivot
                        $order = $image->pivot->order ?? $image->order ?? 0;
                        $imageSyncData[$clonedImage->id] = ['order' => $order];
                    } catch (Throwable $e) {
                        Log::error(
                            "Ошибка копирования медиа при клонировании урока ID {$lesson->id}: " .
                            $e->getMessage(),
                            ['trace' => $e->getTraceAsString()]
                        );
                        // Можно просто пропустить это изображение
                    }
                }
            }

            if (!empty($imageSyncData)) {
                $clonedLesson->images()->sync($imageSyncData);
            }

            DB::commit();

            Log::info(
                "Урок ID {$lesson->id} успешно клонирован в ID {$clonedLesson->id}",
                ['source_id' => $lesson->id, 'clone_id' => $clonedLesson->id]
            );

            return redirect()
                ->route('admin.lessons.index')
                ->with('success', __('admin/controllers.cloned_success'));

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error(
                "Ошибка при клонировании урока ID {$lesson->id}: " . $e->getMessage(),
                ['trace' => $e->getTraceAsString()]
            );

            return back()
                ->withInput()
                ->with('error', __('admin/controllers.cloned_error'));
        }
    }

    /**
     * Приватный метод удаления изображений урока (Spatie MediaLibrary).
     *
     * @param array $imageIds
     * @return void
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $imagesToDelete = LessonImage::whereIn('id', $imageIds)->get();

        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }

        Log::info('Удалены записи LessonImage и их медиа', [
            'image_ids' => $imageIds,
        ]);
    }
}
