<?php

namespace App\Http\Controllers\Admin\Blog\BlogArticle;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\BlogArticle\BlogArticleRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateLeftRequest;
use App\Http\Requests\Admin\System\UpdateMainRequest;
use App\Http\Requests\Admin\System\UpdateRightRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleResource;
use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricSharedResource;
use App\Http\Resources\Admin\Blog\BlogTag\BlogTagSharedResource;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoSharedResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogArticle\BlogArticleImage;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
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
 * Контроллер для управления Статьями (Blog) в админке.
 *
 * Паттерн:
 * - локали (табы)
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity/left/main/right (single + bulk)
 * - activity (single + bulk)
 * - sort + drag&drop (bulk)
 * - клонирование
 * - moderation (approve/reject) только для admin
 * - images (Spatie)
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see BlogArticle
 * @see BlogArticleRequest
 */
class BlogArticleController extends Controller
{
    /**
     * Берём все разрешённые языки из config/app.php.
     */
    private function availableLocales(): array
    {
        return config('app.available_locales', ['ru']);
    }

    /**
     * Базовый запрос:
     * - admin видит всё
     * - обычный пользователь только свои статьи
     */
    private function baseQuery(): Builder
    {
        $query = BlogArticle::query();

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Нормализация локали.
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
     * Приведение сортировки из UI к форматам модели.
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
            default => $sort ?: 'sort_asc',
        };
    }

    /**
     * Общие данные для create/edit:
     * - рубрики
     * - теги
     * - видео
     * - связанные статьи
     */
    private function sharedSelects(string $locale, ?int $excludeArticleId = null): array
    {
        $rubrics = BlogRubric::query()
            ->with(['translations'])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $tags = BlogTag::query()
            ->with(['translations'])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $videos = BlogVideo::query()
            ->with(['translations', 'images'])
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $relatedArticles = $this->baseQuery()
            ->with(['translations', 'images'])
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))
            ->when($excludeArticleId, fn (Builder $query) => $query->where('id', '<>', $excludeArticleId))
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        return [
            'rubrics' => BlogRubricSharedResource::collection($rubrics),
            'tags' => BlogTagSharedResource::collection($tags),
            'videos' => BlogVideoSharedResource::collection($videos),

            /**
             * Оставляю snake_case как в старой архитектуре.
             */
            'related_articles' => BlogArticleSharedResource::collection($relatedArticles),

            /**
             * И camelCase на будущее, если Vue-компонент будет собран в новом стиле.
             */
            'relatedArticles' => BlogArticleSharedResource::collection($relatedArticles),
        ];
    }

    /**
     * Синхронизация переводов статьи.
     */
    private function syncTranslations(BlogArticle $article, array $translations): void
    {
        $locales = array_keys($translations);

        foreach ($translations as $locale => $translationData) {
            $article->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $translationData['title'] ?? null,
                    'subtitle' => $translationData['subtitle'] ?? null,
                    'short' => $translationData['short'] ?? null,
                    'description' => $translationData['description'] ?? null,
                    'pseudonym' => $translationData['pseudonym'] ?? null,
                    'meta_title' => $translationData['meta_title'] ?? null,
                    'meta_keywords' => $translationData['meta_keywords'] ?? null,
                    'meta_desc' => $translationData['meta_desc'] ?? null,
                ]
            );
        }

        $article->translations()
            ->whereNotIn('locale', $locales)
            ->delete();
    }

    /**
     * Синхронизация видео статьи с сортировкой.
     */
    private function syncVideos(BlogArticle $article, array $videos): void
    {
        $syncData = [];

        foreach ($videos as $index => $item) {
            if (is_array($item)) {
                $id = $item['id'] ?? null;
                $sort = $item['sort'] ?? $index;
            } else {
                $id = $item;
                $sort = $index;
            }

            if (!$id) {
                continue;
            }

            $syncData[(int) $id] = [
                'sort' => (int) $sort,
            ];
        }

        $article->videos()->sync($syncData);
    }

    /**
     * Синхронизация связанных статей.
     */
    private function syncRelatedArticles(BlogArticle $article, array $relatedArticles): void
    {
        $syncData = [];

        foreach ($relatedArticles as $index => $item) {
            $id = is_array($item) ? ($item['id'] ?? null) : $item;

            if (!$id || (int) $id === (int) $article->id) {
                continue;
            }

            $syncData[(int) $id] = [
                'sort' => is_array($item)
                    ? (int) ($item['sort'] ?? $index)
                    : $index,
            ];
        }

        $article->relatedArticles()->sync($syncData);
    }

    /**
     * Синхронизация изображений статьи.
     */
    private function syncImages(
        BlogArticle $article,
        Request $request,
        array $imagesData,
        array $deletedImageIds = []
    ): void {
        if (!empty($deletedImageIds)) {
            $article->images()->detach($deletedImageIds);
            $this->deleteImages($deletedImageIds);
        }

        $syncData = [];

        foreach ($imagesData as $index => $imageData) {
            $fileKey = "images.{$index}.file";

            if (!empty($imageData['id'])) {
                $image = BlogArticleImage::find($imageData['id']);

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
                $image = BlogArticleImage::create([
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

        $article->images()->sync($syncData);
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

        $images = BlogArticleImage::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }
    }

    /**
     * Список статей.
     */
    public function index(Request $request): Response
    {
        $adminCountArticles = (int) config('site_settings.AdminCountArticles', 15);
        $adminSortArticles = (string) config('site_settings.AdminSortArticles', 'idDesc');

        $currentLocale = $this->normalizeLocale($request->query('locale'));
        $search = trim((string) $request->query('search', ''));
        $sortParam = $this->normalizeSortParam($request->query('sort', $adminSortArticles));

        try {
            $articles = $this->baseQuery()
                ->with([
                    'owner',
                    'moderator',
                    'translations',
                    'images',
                    'rubrics.translations',
                    'tags.translations',
                    'videos.translations',
                    'videos.images',
                    'relatedArticles.translations',
                    'relatedArticles.images',
                ])
                ->withCount([
                    'comments',
                    'rubrics',
                    'tags',
                    'images',
                    'videos',
                    'likes',
                    'relatedArticles',
                ])
                ->search($search, $currentLocale)
                ->sortByParam($sortParam, $currentLocale)
                ->get();

            $articlesCount = $this->baseQuery()->count();

            return Inertia::render('Admin/Blog/BlogArticles/Index', [
                'articles' => BlogArticleResource::collection($articles),
                'articlesCount' => $articlesCount,

                'adminCountArticles' => $adminCountArticles,
                'adminSortArticles' => $adminSortArticles,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'search' => $search,
                'sortParam' => $sortParam,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка blog articles: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/BlogArticles/Index', [
                'articles' => [],
                'articlesCount' => 0,

                'adminCountArticles' => $adminCountArticles,
                'adminSortArticles' => $adminSortArticles,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'search' => $search,
                'sortParam' => $sortParam,
                'error' => 'Ошибка загрузки статей.',
            ]);
        }
    }

    /**
     * Страница создания статьи.
     */
    public function create(Request $request): Response
    {
        $targetLocale = $this->normalizeLocale($request->query('locale'));

        return Inertia::render('Admin/Blog/BlogArticles/Create', array_merge([
            'targetLocale' => $targetLocale,
            'availableLocales' => $this->availableLocales(),
        ], $this->sharedSelects($targetLocale)));
    }

    /**
     * Создание статьи.
     */
    public function store(BlogArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        $rubricIds = $data['rubrics'] ?? [];
        $tagIds = $data['tags'] ?? [];
        $videos = $data['videos'] ?? [];
        $relatedArticles = $data['related_articles'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['rubrics'],
            $data['tags'],
            $data['videos'],
            $data['related_articles']
        );

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderated_by'], $data['moderated_at'], $data['moderation_note']);
        } else {
            $data['user_id'] = $data['user_id'] ?? $user?->id;
        }

        try {
            DB::transaction(function () use (&$article, $request, $data, $translations, $imagesData, $rubricIds, $tagIds, $videos, $relatedArticles) {
                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = BlogArticle::query()->max('sort');
                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $article = BlogArticle::create($data);

                $this->syncTranslations($article, $translations);

                $article->rubrics()->sync($rubricIds);
                $article->tags()->sync($tagIds);

                $this->syncVideos($article, $videos);
                $this->syncRelatedArticles($article, $relatedArticles);
                $this->syncImages($article, $request, $imagesData);
            });

            return redirect()
                ->route('admin.blogArticles.index')
                ->with('success', 'Статья успешно создана.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании blog article: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании статьи.');
        }
    }

    /**
     * Редирект на страницу редактирования.
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.blogArticles.edit', $id);
    }

    /**
     * Страница редактирования статьи.
     */
    public function edit(int $blogArticle, Request $request): Response
    {
        $article = $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'translations',
                'images',
                'rubrics.translations',
                'tags.translations',
                'videos.translations',
                'videos.images',
                'relatedArticles.translations',
                'relatedArticles.images',
            ])
            ->withCount([
                'comments',
                'rubrics',
                'tags',
                'images',
                'videos',
                'likes',
                'relatedArticles',
            ])
            ->findOrFail($blogArticle);

        $targetLocale = $this->normalizeLocale($request->query('locale'));

        return Inertia::render('Admin/Blog/BlogArticles/Edit', array_merge([
            'article' => new BlogArticleResource($article),
            'targetLocale' => $targetLocale,
            'availableLocales' => $this->availableLocales(),
        ], $this->sharedSelects($targetLocale, $article->id)));
    }

    /**
     * Обновление статьи.
     */
    public function update(BlogArticleRequest $request, int $blogArticle): RedirectResponse
    {
        $article = $this->baseQuery()->findOrFail($blogArticle);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        $rubricIds = $data['rubrics'] ?? [];
        $tagIds = $data['tags'] ?? [];
        $videos = $data['videos'] ?? [];
        $relatedArticles = $data['related_articles'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['rubrics'],
            $data['tags'],
            $data['videos'],
            $data['related_articles'],
            $data['_method']
        );

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderated_by'], $data['moderated_at'], $data['moderation_note']);
        }

        try {
            DB::transaction(function () use ($article, $request, $data, $translations, $imagesData, $deletedImageIds, $rubricIds, $tagIds, $videos, $relatedArticles) {
                $article->update($data);

                $this->syncTranslations($article, $translations);

                $article->rubrics()->sync($rubricIds);
                $article->tags()->sync($tagIds);

                $this->syncVideos($article, $videos);
                $this->syncRelatedArticles($article, $relatedArticles);
                $this->syncImages($article, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.blogArticles.index')
                ->with('success', 'Статья успешно обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка при обновлении blog article ID ' . $article->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении статьи.');
        }
    }

    /**
     * Удаление статьи.
     */
    public function destroy(int $blogArticle): RedirectResponse
    {
        $article = $this->baseQuery()->with('images')->findOrFail($blogArticle);

        try {
            DB::transaction(function () use ($article) {
                $this->deleteImages(
                    $article->images()->pluck('blog_article_images.id')->toArray()
                );

                $article->images()->detach();
                $article->rubrics()->detach();
                $article->tags()->detach();
                $article->videos()->detach();
                $article->relatedArticles()->detach();
                $article->usedInRelatedArticles()->detach();

                $article->comments()->delete();
                $article->likes()->delete();
                $article->translations()->delete();

                $article->delete();
            });

            return redirect()
                ->route('admin.blogArticles.index')
                ->with('success', 'Статья успешно удалена.');
        } catch (Throwable $e) {
            Log::error('Ошибка при удалении blog article ID ' . $article->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при удалении статьи.');
        }
    }

    /**
     * Массовое удаление статей.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_articles,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть статей недоступна для удаления.');
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                $articles = BlogArticle::query()
                    ->whereIn('id', $allowedIds)
                    ->with('images')
                    ->get();

                foreach ($articles as $article) {
                    $this->deleteImages(
                        $article->images()->pluck('blog_article_images.id')->toArray()
                    );

                    $article->images()->detach();
                    $article->rubrics()->detach();
                    $article->tags()->detach();
                    $article->videos()->detach();
                    $article->relatedArticles()->detach();
                    $article->usedInRelatedArticles()->detach();

                    $article->comments()->delete();
                    $article->likes()->delete();
                    $article->translations()->delete();

                    $article->delete();
                }
            });

            return back()->with('success', 'Выбранные статьи успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy blog articles: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', 'Ошибка при массовом удалении статей.');
        }
    }

    /**
     * Обновление активности одной статьи.
     */
    public function updateActivity(UpdateActivityRequest $request, int $blogArticle): RedirectResponse
    {
        $article = $this->baseQuery()->findOrFail($blogArticle);

        $article->update([
            'activity' => $request->validated('activity'),
        ]);

        return back()->with('success', 'Активность статьи обновлена.');
    }

    /**
     * Массовое обновление активности.
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'activity', 'Активность выбранных статей обновлена.');
    }

    /**
     * Обновление сортировки одной статьи.
     */
    public function updateSort(UpdateSortEntityRequest $request, int $blogArticle): RedirectResponse
    {
        $article = $this->baseQuery()->findOrFail($blogArticle);

        $article->update([
            'sort' => $request->validated('sort'),
        ]);

        return back()->with('success', 'Сортировка статьи обновлена.');
    }

    /**
     * Массовое обновление сортировки.
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required_without:articles', 'array'],
            'items.*.id' => ['required_with:items', 'integer', 'exists:blog_articles,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],

            'articles' => ['required_without:items', 'array'],
            'articles.*.id' => ['required_with:articles', 'integer', 'exists:blog_articles,id'],
            'articles.*.sort' => ['required_with:articles', 'integer', 'min:0'],
        ]);

        $items = $validated['items'] ?? $validated['articles'];
        $ids = array_column($items, 'id');

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            $message = 'Часть статей недоступна для изменения сортировки.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 400)
                : back()->with('error', $message);
        }

        try {
            DB::transaction(function () use ($items) {
                foreach ($items as $row) {
                    BlogArticle::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $message = 'Сортировка статей обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка updateSortBulk blog articles: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = 'Ошибка при массовом обновлении сортировки статей.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /**
     * Обновление позиции left.
     */
    public function updateLeft(UpdateLeftRequest $request, int $blogArticle): RedirectResponse
    {
        $article = $this->baseQuery()->findOrFail($blogArticle);

        $article->update([
            'left' => $request->validated('left'),
        ]);

        return back()->with('success', 'Позиция left обновлена.');
    }

    /**
     * Обновление позиции main.
     */
    public function updateMain(UpdateMainRequest $request, int $blogArticle): RedirectResponse
    {
        $article = $this->baseQuery()->findOrFail($blogArticle);

        $article->update([
            'main' => $request->validated('main'),
        ]);

        return back()->with('success', 'Позиция main обновлена.');
    }

    /**
     * Обновление позиции right.
     */
    public function updateRight(UpdateRightRequest $request, int $blogArticle): RedirectResponse
    {
        $article = $this->baseQuery()->findOrFail($blogArticle);

        $article->update([
            'right' => $request->validated('right'),
        ]);

        return back()->with('success', 'Позиция right обновлена.');
    }

    /**
     * Массовое обновление left.
     */
    public function bulkUpdateLeft(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'left', 'Позиция left выбранных статей обновлена.');
    }

    /**
     * Массовое обновление main.
     */
    public function bulkUpdateMain(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'main', 'Позиция main выбранных статей обновлена.');
    }

    /**
     * Массовое обновление right.
     */
    public function bulkUpdateRight(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'right', 'Позиция right выбранных статей обновлена.');
    }

    /**
     * Общий метод массового обновления boolean-поля.
     */
    private function bulkUpdateBooleanFlag(Request $request, string $field, string $message): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_articles,id'],
            $field => ['required', 'boolean'],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            $error = 'Часть статей недоступна для обновления.';

            return $request->expectsJson()
                ? response()->json(['message' => $error], 403)
                : back()->with('error', $error);
        }

        BlogArticle::whereIn('id', $allowedIds)->update([
            $field => $validated[$field],
        ]);

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }

    /**
     * Модерация статьи:
     * доступ только для admin.
     */
    public function approve(Request $request, int $blogArticle): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', Rule::in([0, 1, 2])],
            'moderation_note' => ['nullable', 'string', 'max:500'],
        ]);

        $article = BlogArticle::findOrFail($blogArticle);

        $article->update([
            'moderation_status' => (int) $validated['moderation_status'],
            'moderation_note' => $validated['moderation_note'] ?? null,
            'moderated_by' => $user->id,
            'moderated_at' => now(),
        ]);

        $message = 'Статус модерации статьи обновлён.';

        return $request->expectsJson()
            ? response()->json([
                'message' => $message,
                'article' => new BlogArticleResource(
                    $article
                        ->load([
                            'owner',
                            'moderator',
                            'translations',
                            'images',
                            'rubrics.translations',
                            'tags.translations',
                            'videos.translations',
                            'videos.images',
                            'relatedArticles.translations',
                            'relatedArticles.images',
                        ])
                        ->loadCount([
                            'comments',
                            'rubrics',
                            'tags',
                            'images',
                            'videos',
                            'likes',
                            'relatedArticles',
                        ])
                ),
            ])
            : back()->with('success', $message);
    }
}
