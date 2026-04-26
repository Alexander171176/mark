<?php

namespace App\Http\Controllers\Admin\Blog\BlogVideo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\BlogVideo\BlogVideoRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateLeftRequest;
use App\Http\Requests\Admin\System\UpdateMainRequest;
use App\Http\Requests\Admin\System\UpdateRightRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoResource;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoSharedResource;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Blog\BlogVideo\BlogVideoImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер для управления Видео (Blog) в админке.
 *
 * Паттерн:
 * - локали (табы)
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity/left/main/right (single + bulk)
 * - sort + drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 * - images (Spatie) — НЕ МЕНЯЕМ логику
 * - video file (Spatie) — НЕ МЕНЯЕМ логику
 *
 * @version 1.1
 * @author Александр
 */
class BlogVideoController extends Controller
{
    /**
     * Берём все разрешённые языки из config/app.php
     */
    private function availableLocales(): array
    {
        return config('app.available_locales', ['ru']);
    }

    /**
     * Базовый запрос:
     * - admin видит всё
     * - обычный пользователь только свои видео
     */
    private function baseQuery(): Builder
    {
        $query = BlogVideo::query();

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Нормализация локали:
     * если локаль невалидна — fallback
     */
    private function normalizeLocale(?string $locale): string
    {
        $availableLocales = $this->availableLocales();
        $fallback = config('app.fallback_locale', 'ru');

        if (!$locale || !in_array($locale, $availableLocales, true)) {
            return $fallback;
        }

        return $locale;
    }

    /**
     * Приведение сортировки из UI к форматам модели
     */
    private function normalizeSortParam(?string $sort): string
    {
        return match ($sort) {
            'idAsc' => 'date_asc',
            'idDesc' => 'date_desc',
            'sortAsc' => 'sort_asc',
            'sortDesc' => 'sort_desc',
            'titleAsc' => 'title_asc',
            'titleDesc' => 'title_desc',
            'viewsAsc' => 'views_asc',
            'viewsDesc' => 'views_desc',
            'likesAsc' => 'likes_asc',
            'likesDesc' => 'likes_desc',
            'durationAsc' => 'duration_asc',
            'durationDesc' => 'duration_desc',
            default => $sort ?: 'sort_asc',
        };
    }

    /**
     * Синхронизация переводов:
     * - создание/обновление текущих
     * - удаление отсутствующих
     */
    private function syncTranslations(BlogVideo $video, array $translations): void
    {
        $locales = array_keys($translations);

        foreach ($translations as $locale => $translationData) {
            $video->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $translationData['title'] ?? null,
                    'short' => $translationData['short'] ?? null,
                    'description' => $translationData['description'] ?? null,
                    'pseudonym' => $translationData['pseudonym'] ?? null,
                    'meta_title' => $translationData['meta_title'] ?? null,
                    'meta_keywords' => $translationData['meta_keywords'] ?? null,
                    'meta_desc' => $translationData['meta_desc'] ?? null,
                ]
            );
        }

        $video->translations()
            ->whereNotIn('locale', $locales)
            ->delete();
    }

    /**
     * Синхронизация связанных видео
     */
    private function syncRelatedVideos(BlogVideo $video, array $relatedVideos): void
    {
        $syncData = [];

        foreach ($relatedVideos as $index => $item) {
            $id = is_array($item) ? ($item['id'] ?? null) : $item;

            if (!$id || (int) $id === (int) $video->id) {
                continue;
            }

            $syncData[(int) $id] = [
                'sort' => is_array($item)
                    ? (int) ($item['sort'] ?? $index)
                    : $index,
            ];
        }

        $video->relatedVideos()->sync($syncData);
    }

    /**
     * Синхронизация превью-изображений видео
     */
    private function syncImages(BlogVideo $video, Request $request, array $imagesData, array $deletedImageIds = []): void
    {
        if (!empty($deletedImageIds)) {
            $video->images()->detach($deletedImageIds);
            $this->deleteImages($deletedImageIds);
        }

        $syncData = [];

        foreach ($imagesData as $index => $imageData) {
            $fileKey = "images.{$index}.file";

            if (!empty($imageData['id'])) {
                $image = BlogVideoImage::find($imageData['id']);

                if (!$image || in_array($image->id, $deletedImageIds, true)) {
                    continue;
                }

                $image->update([
                    'order' => $imageData['order'] ?? $image->order,
                    'alt' => $imageData['alt'] ?? $image->alt,
                    'caption' => $imageData['caption'] ?? $image->caption,
                ]);

                if ($request->hasFile($fileKey)) {
                    $image->clearMediaCollection('images');
                    $image->addMedia($request->file($fileKey))->toMediaCollection('images');
                }

                $syncData[$image->id] = [
                    'order' => $image->order,
                ];

                continue;
            }

            if ($request->hasFile($fileKey)) {
                $image = BlogVideoImage::create([
                    'order' => $imageData['order'] ?? 0,
                    'alt' => $imageData['alt'] ?? '',
                    'caption' => $imageData['caption'] ?? '',
                ]);

                $image->addMedia($request->file($fileKey))->toMediaCollection('images');

                $syncData[$image->id] = [
                    'order' => $image->order,
                ];
            }
        }

        $video->images()->sync($syncData);
    }

    /**
     * Полное удаление изображений:
     * - media Spatie
     * - записи из БД
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $images = BlogVideoImage::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }
    }

    /**
     * Список видео:
     * - поиск
     * - сортировка
     * - текущая локаль
     */
    public function index(Request $request): Response
    {
        $adminCountVideos = (int) config('site_settings.AdminCountVideos', 15);
        $adminSortVideos = (string) config('site_settings.AdminSortVideos', 'idDesc');

        $currentLocale = $this->normalizeLocale($request->query('locale'));
        $search = trim((string) $request->query('search', ''));
        $sortParam = $this->normalizeSortParam($request->query('sort', $adminSortVideos));

        try {
            $videos = $this->baseQuery()
                ->with([
                    'owner',
                    'moderator',
                    'translations',
                    'images',
                    'relatedVideos.translations',
                    'relatedVideos.images',
                ])
                ->withCount(['images', 'comments', 'likes'])
                ->search($search, $currentLocale)
                ->sortByParam($sortParam, $currentLocale)
                ->get();

            $videosCount = $this->baseQuery()->count();

            return Inertia::render('Admin/Blog/BlogVideos/Index', [
                'videos' => BlogVideoResource::collection($videos),
                'videosCount' => $videosCount,

                'adminCountVideos' => $adminCountVideos,
                'adminSortVideos' => $adminSortVideos,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'search' => $search,
                'sortParam' => $sortParam,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка blog videos: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/BlogVideos/Index', [
                'videos' => [],
                'videosCount' => 0,

                'adminCountVideos' => $adminCountVideos,
                'adminSortVideos' => $adminSortVideos,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'search' => $search,
                'sortParam' => $sortParam,
                'error' => 'Ошибка загрузки видео.',
            ]);
        }
    }

    /**
     * Страница создания видео
     */
    public function create(Request $request): Response
    {
        $targetLocale = $this->normalizeLocale($request->query('locale'));

        $relatedVideos = $this->baseQuery()
            ->with(['translations', 'images'])
            ->whereHas('translations', fn (Builder $q) => $q->where('locale', $targetLocale))
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Admin/Blog/BlogVideos/Create', [
            'targetLocale' => $targetLocale,
            'availableLocales' => $this->availableLocales(),
            'relatedVideos' => BlogVideoSharedResource::collection($relatedVideos),
        ]);
    }

    /**
     * Создание видео:
     * - основная запись
     * - переводы
     * - local video file
     * - превью-изображения
     * - связанные видео
     */
    public function store(BlogVideoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $relatedVideos = $data['related_videos'] ?? [];

        unset($data['translations'], $data['images'], $data['deletedImages'], $data['related_videos']);

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderated_by'], $data['moderated_at'], $data['moderation_note']);
        } else {
            $data['user_id'] = $data['user_id'] ?? $user?->id;
        }

        try {
            DB::transaction(function () use (&$video, $request, $data, $translations, $imagesData, $relatedVideos) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = BlogVideo::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $video = BlogVideo::create($data);

                $this->syncTranslations($video, $translations);

                if ($video->source_type === 'local' && $request->hasFile('video_file')) {
                    $video->addMediaFromRequest('video_file')->toMediaCollection('videos');
                }

                $this->syncRelatedVideos($video, $relatedVideos);
                $this->syncImages($video, $request, $imagesData);
            });

            return redirect()
                ->route('admin.blogVideos.index')
                ->with('success', 'Видео успешно создано.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании blog video: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании видео.');
        }
    }

    /**
     * Редирект на страницу редактирования
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.blogVideos.edit', $id);
    }

    /**
     * Страница редактирования видео
     */
    public function edit(int $blogVideo, Request $request): Response
    {
        $video = $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'translations',
                'images',
                'relatedVideos.translations',
                'relatedVideos.images',
            ])
            ->withCount(['images', 'comments', 'likes'])
            ->findOrFail($blogVideo);

        $targetLocale = $this->normalizeLocale($request->query('locale'));

        $relatedVideos = $this->baseQuery()
            ->with(['translations', 'images'])
            ->where('id', '<>', $video->id)
            ->whereHas('translations', fn (Builder $q) => $q->where('locale', $targetLocale))
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Admin/Blog/BlogVideos/Edit', [
            'video' => new BlogVideoResource($video),
            'videoUrl' => $video->getFirstMediaUrl('videos') ?: null,
            'relatedVideos' => BlogVideoSharedResource::collection($relatedVideos),
            'targetLocale' => $targetLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /**
     * Обновление видео:
     * - основная запись
     * - переводы
     * - local video file
     * - превью-изображения
     * - связанные видео
     */
    public function update(BlogVideoRequest $request, int $blogVideo): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($blogVideo);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];
        $relatedVideos = $data['related_videos'] ?? [];

        unset($data['translations'], $data['images'], $data['deletedImages'], $data['related_videos']);

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderated_by'], $data['moderated_at'], $data['moderation_note']);
        }

        try {
            DB::transaction(function () use ($video, $request, $data, $translations, $imagesData, $deletedImageIds, $relatedVideos) {
                $video->update($data);

                $this->syncTranslations($video, $translations);

                if ($video->source_type === 'local' && $request->hasFile('video_file')) {
                    $video->clearMediaCollection('videos');
                    $video->addMediaFromRequest('video_file')->toMediaCollection('videos');
                }

                if ($video->source_type !== 'local') {
                    $video->clearMediaCollection('videos');
                }

                $this->syncRelatedVideos($video, $relatedVideos);
                $this->syncImages($video, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.blogVideos.index')
                ->with('success', 'Видео успешно обновлено.');
        } catch (Throwable $e) {
            Log::error('Ошибка при обновлении blog video ID ' . $video->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении видео.');
        }
    }

    /**
     * Удаление видео:
     * - превью
     * - видеофайл
     * - связи
     * - переводы
     */
    public function destroy(int $blogVideo): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($blogVideo);

        try {
            DB::transaction(function () use ($video) {
                $this->deleteImages($video->images()->pluck('blog_video_images.id')->toArray());

                $video->images()->detach();
                $video->relatedVideos()->detach();
                $video->usedInRelatedVideos()->detach();
                $video->articles()->detach();
                $video->likes()->delete();
                $video->translations()->delete();
                $video->clearMediaCollection('videos');

                $video->delete();
            });

            return redirect()
                ->route('admin.blogVideos.index')
                ->with('success', 'Видео успешно удалено.');
        } catch (Throwable $e) {
            Log::error('Ошибка при удалении blog video ID ' . $video->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении видео.');
        }
    }

    /**
     * Массовое удаление видео
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_videos,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть видео недоступна для удаления.');
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                $videos = BlogVideo::whereIn('id', $allowedIds)
                    ->with('images')
                    ->get();

                foreach ($videos as $video) {
                    $this->deleteImages($video->images()->pluck('blog_video_images.id')->toArray());

                    $video->images()->detach();
                    $video->relatedVideos()->detach();
                    $video->usedInRelatedVideos()->detach();
                    $video->articles()->detach();
                    $video->likes()->delete();
                    $video->translations()->delete();
                    $video->clearMediaCollection('videos');

                    $video->delete();
                }
            });

            return back()->with('success', 'Выбранные видео успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy blog videos: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении видео.');
        }
    }

    /**
     * Обновление активности одного видео
     */
    public function updateActivity(UpdateActivityRequest $request, int $blogVideo): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($blogVideo);

        $video->update([
            'activity' => $request->validated('activity'),
        ]);

        return back()->with('success', 'Активность видео обновлена.');
    }

    /**
     * Массовое обновление активности
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_videos,id'],
            'activity' => ['required', 'boolean'],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            return back()->with('error', 'Часть видео недоступна для обновления активности.');
        }

        BlogVideo::whereIn('id', $allowedIds)->update([
            'activity' => $validated['activity'],
        ]);

        $message = 'Активность выбранных видео обновлена.';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }

    /**
     * Обновление сортировки одного видео
     */
    public function updateSort(UpdateSortEntityRequest $request, int $blogVideo): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($blogVideo);

        $video->update([
            'sort' => $request->validated('sort'),
        ]);

        return back()->with('success', 'Сортировка видео обновлена.');
    }

    /**
     * Массовое обновление сортировки
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required_without:videos', 'array'],
            'items.*.id' => ['required_with:items', 'integer', 'exists:blog_videos,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],

            'videos' => ['required_without:items', 'array'],
            'videos.*.id' => ['required_with:videos', 'integer', 'exists:blog_videos,id'],
            'videos.*.sort' => ['required_with:videos', 'integer', 'min:0'],
        ]);

        $items = $validated['items'] ?? $validated['videos'];
        $ids = array_column($items, 'id');

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            $message = 'Часть видео недоступна для изменения сортировки.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 400)
                : back()->with('error', $message);
        }

        try {
            DB::transaction(function () use ($items) {
                foreach ($items as $row) {
                    BlogVideo::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $message = 'Сортировка видео обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка updateSortBulk blog videos: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при массовом обновлении сортировки видео.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /**
     * Обновление позиции left
     */
    public function updateLeft(UpdateLeftRequest $request, int $blogVideo): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($blogVideo);

        $video->update([
            'left' => $request->validated('left'),
        ]);

        return back()->with('success', 'Позиция left обновлена.');
    }

    /**
     * Обновление позиции main
     */
    public function updateMain(UpdateMainRequest $request, int $blogVideo): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($blogVideo);

        $video->update([
            'main' => $request->validated('main'),
        ]);

        return back()->with('success', 'Позиция main обновлена.');
    }

    /**
     * Обновление позиции right
     */
    public function updateRight(UpdateRightRequest $request, int $blogVideo): RedirectResponse
    {
        $video = $this->baseQuery()->findOrFail($blogVideo);

        $video->update([
            'right' => $request->validated('right'),
        ]);

        return back()->with('success', 'Позиция right обновлена.');
    }

    /**
     * Массовое обновление позиции left
     */
    public function bulkUpdateLeft(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'left', 'Позиция left выбранных видео обновлена.');
    }

    /**
     * Массовое обновление позиции main
     */
    public function bulkUpdateMain(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'main', 'Позиция main выбранных видео обновлена.');
    }

    /**
     * Массовое обновление позиции right
     */
    public function bulkUpdateRight(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'right', 'Позиция right выбранных видео обновлена.');
    }

    /**
     * Общий метод массового обновления boolean-флага
     */
    private function bulkUpdateBooleanFlag(Request $request, string $field, string $message): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_videos,id'],
            $field => ['required', 'boolean'],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            $error = 'Часть видео недоступна для обновления.';

            return $request->expectsJson()
                ? response()->json(['message' => $error], 403)
                : back()->with('error', $error);
        }

        BlogVideo::whereIn('id', $allowedIds)->update([
            $field => $validated[$field],
        ]);

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }

    /**
     * Модерация видео:
     * доступ только для admin
     */
    public function approve(Request $request, int $blogVideo): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', Rule::in([0, 1, 2])],
            'moderation_note' => ['nullable', 'string', 'max:500'],
        ]);

        $video = BlogVideo::findOrFail($blogVideo);

        $video->update([
            'moderation_status' => (int) $validated['moderation_status'],
            'moderation_note' => $validated['moderation_note'] ?? null,
            'moderated_by' => $user->id,
            'moderated_at' => now(),
        ]);

        $message = 'Статус модерации видео обновлён.';

        return $request->expectsJson()
            ? response()->json([
                'message' => $message,
                'video' => new BlogVideoResource(
                    $video
                        ->load(['owner', 'moderator', 'translations', 'images', 'relatedVideos.translations', 'relatedVideos.images'])
                        ->loadCount(['images', 'comments', 'likes'])
                ),
            ])
            : back()->with('success', $message);
    }
}
