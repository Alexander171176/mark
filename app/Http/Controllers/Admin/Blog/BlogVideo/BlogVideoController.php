<?php

namespace App\Http\Controllers\Admin\Blog\BlogVideo;

use App\Http\Controllers\Admin\Blog\Base\BaseBlogAdminController;
use App\Http\Requests\Admin\Blog\BlogVideo\BlogVideoRequest;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoResource;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoSharedResource;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Blog\BlogVideo\BlogVideoImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
class BlogVideoController extends BaseBlogAdminController
{
    protected string $modelClass = BlogVideo::class;

    protected string $imageModelClass = BlogVideoImage::class;

    protected string $imageMediaCollection = 'images';

    protected string $entityLabel = 'видео';

    protected array $translationFields = [
        'title',
        'short',
        'description',
        'pseudonym',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /**
     * Расширенная сортировка от базовой
     */
    protected function extendedSortMap(): array
    {
        return [
            'viewsAsc' => 'views_asc',
            'viewsDesc' => 'views_desc',

            'likesAsc' => 'likes_asc',
            'likesDesc' => 'likes_desc',

            'durationAsc' => 'duration_asc',
            'durationDesc' => 'duration_desc',
        ];
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

            return Inertia::render('Admin/Blog/BlogVideos/Index', [
                'videos' => BlogVideoResource::collection($videos),
                'videosCount' => $this->baseQuery()->count(),

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

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
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

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
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
}
