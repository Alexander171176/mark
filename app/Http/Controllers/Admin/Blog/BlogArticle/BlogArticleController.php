<?php

namespace App\Http\Controllers\Admin\Blog\BlogArticle;

use App\Http\Controllers\Admin\Blog\Base\BaseBlogAdminController;
use App\Http\Requests\Admin\Blog\BlogArticle\BlogArticleRequest;
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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
class BlogArticleController extends BaseBlogAdminController
{
    protected string $modelClass = BlogArticle::class;

    protected string $imageModelClass = BlogArticleImage::class;

    protected string $imageMediaCollection = 'images';

    protected string $entityLabel = 'статей';

    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
        'description',
        'pseudonym',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /**
     * Дополнительные варианты сортировки статей
     */
    protected function extendedSortMap(): array
    {
        return [
            'viewsAsc' => 'views_asc',
            'viewsDesc' => 'views_desc',
            'likesAsc' => 'likes_asc',
            'likesDesc' => 'likes_desc',
        ];
    }

    /**
     * Общие данные для create/edit
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
            'related_articles' => BlogArticleSharedResource::collection($relatedArticles),
            'relatedArticles' => BlogArticleSharedResource::collection($relatedArticles),
        ];
    }

    /**
     * Синхронизация видео статьи
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
     * Синхронизация связанных статей
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
     * Список статей
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

            return Inertia::render('Admin/Blog/BlogArticles/Index', [
                'articles' => BlogArticleResource::collection($articles),
                'articlesCount' => $this->baseQuery()->count(),

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
     * Страница создания статьи
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
     * Создание статьи
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
     * Редирект на страницу редактирования
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.blogArticles.edit', $id);
    }

    /**
     * Страница редактирования статьи
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
     * Обновление статьи
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

            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
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
     * Удаление статьи
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
     * Массовое удаление статей
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
}
