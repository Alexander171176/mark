<?php

namespace App\Http\Controllers\Admin\Blog\BlogVideo;

use App\Http\Controllers\Admin\Blog\BaseBlogAdminController;
use App\Http\Requests\Admin\Blog\BlogVideo\BlogVideoRequest;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoResource;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoSharedResource;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Blog\BlogVideo\BlogVideoImage;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
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
    /** Основная модель */
    protected string $modelClass = BlogVideo::class;

    /** Модель изображений */
    protected string $imageModelClass = BlogVideoImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для уведомлений */
    protected string $entityLabel = 'видео';

    /** Поля переводов */
    protected array $translationFields = [
        'title',
        'short',
        'description',
        'pseudonym',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Синхронизация связанных видео */
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

    /** Список видео */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminBlogVideosPerPage', 6);
        $defaultSort = $settings->string('adminBlogVideosDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string('adminBlogVideosProcessingMode', 'frontend');

        $videosCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $videosCount,
                300
            );

        try {
            $videos = $this->getIndexVideos(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
            );

            return Inertia::render('Admin/Blog/BlogVideos/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminBlogVideosPerPage' => $perPage,
                'adminBlogVideosDefaultSort' => $defaultSort,
                'adminBlogVideosProcessingMode' => $processingMode,

                'videos' => BlogVideoSharedResource::collection($videos),
                'videosCount' => $videosCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка blog videos: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/BlogVideos/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminBlogVideosPerPage' => $perPage,
                'adminBlogVideosDefaultSort' => $defaultSort,
                'adminBlogVideosProcessingMode' => $processingMode,

                'videos' => [],
                'videosCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'error' => 'Ошибка загрузки видео.',
            ]);
        }
    }

    /** Страница создания видео */
    public function create(
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        /**
         * Видео для multiselect.
         *
         * Нужен только перевод выбранной локали.
         */
        $relatedVideos = $this->baseQuery()
            ->whereHas(
                'translations',
                fn (Builder $query) =>
                $query->where(
                    'locale',
                    $currentLocale
                )
            )
            ->with([
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $currentLocale
                ),
            ])
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            )
            ->get();

        return Inertia::render(
            'Admin/Blog/BlogVideos/Create',
            [
                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),

                'relatedVideos' =>
                    BlogVideoSharedResource::collection(
                        $relatedVideos
                    ),
            ]
        );
    }

    /** Создание видео */
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

    /** Редирект на страницу редактирования */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.blogVideos.edit', $id);
    }

    /** Страница редактирования видео */
    public function edit(
        int $blogVideo,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale(
            $request
        );

        /**
         * Основное видео.
         *
         * Для Edit нужны:
         * - все переводы для TranslationTabs;
         * - изображения + media;
         * - выбранные связанные видео;
         * - media локального видео.
         */
        $video = $this->baseQuery()
            ->with([
                /**
                 * Все переводы основной сущности.
                 *
                 * Ограничивать locale здесь нельзя:
                 * TranslationTabs позволяет добавлять,
                 * удалять и редактировать языки.
                 */
                'translations',

                /**
                 * Изображения + Spatie Media
                 * загружаются пакетно.
                 */
                'images.media',

                /**
                 * Собственный Spatie Media
                 * для source_type = local.
                 */
                'media',

                /**
                 * Выбранные связанные видео.
                 *
                 * Для multiselect нужен только
                 * перевод текущей локали.
                 */
                'relatedVideos' => fn ($query) =>
                $query->with([
                    'translations' => fn ($translationQuery) =>
                    $translationQuery->where(
                        'locale',
                        $currentLocale
                    ),
                ]),
            ])
            ->findOrFail(
                $blogVideo
            );

        /**
         * Все доступные видео для multiselect.
         *
         * Нужен только перевод текущей локали.
         * Изображения multiselect не использует.
         */
        $relatedVideos = $this->baseQuery()
            ->where(
                'id',
                '<>',
                $video->id
            )
            ->whereHas(
                'translations',
                fn (Builder $query) =>
                $query->where(
                    'locale',
                    $currentLocale
                )
            )
            ->with([
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $currentLocale
                ),
            ])
            ->orderBy(
                'sort'
            )
            ->orderByDesc(
                'id'
            )
            ->get();

        return Inertia::render(
            'Admin/Blog/BlogVideos/Edit',
            [
                'video' => new BlogVideoResource(
                    $video
                ),

                /**
                 * URL локального видео.
                 *
                 * Relation media уже eager-loaded.
                 */
                'videoUrl' =>
                    $video->getFirstMediaUrl('videos')
                        ?: null,

                'relatedVideos' =>
                    BlogVideoSharedResource::collection(
                        $relatedVideos
                    ),

                'currentLocale' =>
                    $currentLocale,

                'availableLocales' =>
                    $this->availableLocales(),
            ]
        );
    }

    /** Обновление видео */
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

    /** Удаление видео */
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

    /** Массовое удаление видео */
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

    /** Базовый запрос списка видео. */
    private function indexVideosQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                /**
                 * Для Admin Index нужен только
                 * перевод выбранной локали.
                 */
                'translations' => fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),

                /**
                 * Владелец нужен отображению,
                 * поиску и сортировке.
                 */
                'owner',

                /**
                 * Модератор нужен
                 * frontend-поиску.
                 */
                'moderator',

                /**
                 * Изображения + Spatie Media
                 * загружаются пакетно.
                 */
                'images.media',

                /**
                 * Сам BlogVideo тоже использует
                 * Spatie Media для локального видео.
                 *
                 * Это защищает video_url
                 * от потенциального N+1.
                 */
                'media',
            ])
            ->withCount([
                'images',
                'comments',
                'likes',
                'articles',
                'relatedVideos',
            ]);
    }

    /** Список видео по режиму обработки */
    private function getIndexVideos(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = '',
    ) {
        $query = $this->indexVideosQuery(
            $locale
        );

        /**
         * Server mode:
         * поиск, сортировка и пагинация
         * выполняются SQL.
         */
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

        /**
         * Frontend mode:
         *
         * backend отдаёт полную коллекцию,
         * Vue сам выполняет поиск,
         * фильтрацию, сортировку
         * и локальную пагинацию.
         */
        return $query
            ->orderBy(
                'sort',
                'asc'
            )
            ->orderByDesc(
                'id'
            )
            ->get();
    }
}
