<?php

namespace App\Http\Controllers\Admin\Blog\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\Article\ArticleRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateLeftRequest;
use App\Http\Requests\Admin\System\UpdateMainRequest;
use App\Http\Requests\Admin\System\UpdateRightRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Blog\Article\ArticleResource;
use App\Http\Resources\Admin\Blog\Rubric\RubricResource;
use App\Http\Resources\Admin\Blog\Tag\TagResource;
use App\Http\Resources\Admin\Blog\Video\VideoResource;
use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Article\ArticleImage;
use App\Models\Admin\Blog\Rubric\Rubric;
use App\Models\Admin\Blog\Tag\Tag;
use App\Models\Admin\Blog\Video\Video;
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
 * @see Article
 * @see ArticleRequest
 */
class ArticleController extends Controller
{
    /**
     * Разрешённые локали.
     *
     * @var array|string[]
     */
    protected array $availableLocales = ['ru', 'en', 'kk'];

    /**
     * Базовый query с ограничением “владелец/админ”.
     * Автор видит только свои статьи. Admin — все.
     *
     * @return Builder
     */
    private function baseQuery(): Builder
    {
        $q = Article::query();

        $user = auth()->user();
        if ($user && ! $user->hasRole('admin')) {
            $q->where('user_id', $user->id);
        }

        return $q;
    }

    /**
     * Общие селекты для create/edit:
     * - rubrics (привязка к статье)
     * - tags
     * - videos (привязка к статье)
     * - related_articles (привязка к статье)
     *
     * + locale-фильтр.
     *
     * @param string|null $locale
     * @param int|null $excludeArticleId
     * @return array
     */
    private function sharedSelects(?string $locale = null, ?int $excludeArticleId = null): array
    {
        $user = auth()->user();
        $isAdmin = $user && $user->hasRole('admin');

        $locale = $locale ?: config('app.fallback_locale', 'ru');
        if (!in_array($locale, $this->availableLocales, true)) {
            $locale = config('app.fallback_locale', 'ru');
        }

        $rubrics = Rubric::query()
            ->select('id', 'title', 'locale', 'user_id')
            ->with(['owner:id,name,email'])
            ->where('locale', $locale)
            ->when(! $isAdmin, fn ($q) => $q->where('user_id', $user->id))
            ->orderBy('id', 'asc')
            ->get();

        $tags = Tag::query()
            ->select('id', 'name', 'locale', 'user_id')
            ->with(['owner:id,name,email'])
            ->where('locale', $locale)
            ->when(! $isAdmin, fn ($q) => $q->where('user_id', $user->id))
            ->orderBy('id', 'asc')
            ->get();

        $videos = Video::query()
            ->select('id', 'title', 'locale', 'url', 'user_id')
            ->with(['owner:id,name,email'])
            ->where('locale', $locale)
            ->when(! $isAdmin, fn ($q) => $q->where('user_id', $user->id))
            ->orderBy('id', 'desc')
            ->get();

        $articles = $this->baseQuery()
            ->select('id', 'title', 'locale', 'url', 'img', 'activity', 'moderation_status', 'published_at', 'views', 'user_id')
            ->with(['owner:id,name,email'])
            ->where('locale', $locale)
            ->when($excludeArticleId, fn ($q) => $q->where('id', '<>', $excludeArticleId))
            ->orderBy('id', 'desc')
            ->get();

        return [
            'currentLocale'    => $locale,
            'availableLocales' => $this->availableLocales,

            'rubrics' => RubricResource::collection($rubrics),
            'tags'    => TagResource::collection($tags),

            'videos' => class_exists(VideoResource::class)
                ? VideoResource::collection($videos)
                : $videos,

            'related_articles' => ArticleResource::collection($articles),
        ];
    }

    /**
     * Список статей + локали.
     * GET /admin/articles?locale=ru
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $adminCountArticles = (int) config('site_settings.AdminCountArticles', 15);
        $adminSortArticles  = (string) config('site_settings.AdminSortArticles', 'idDesc');

        $currentLocale = $request->query('locale', config('app.fallback_locale', 'ru'));
        if (!in_array($currentLocale, $this->availableLocales, true)) {
            $currentLocale = config('app.fallback_locale', 'ru');
            session()->flash('warning', __('admin/controllers.index_locale_error'));
        }

        try {
            $articles = $this->baseQuery()
                ->where('locale', $currentLocale)
                ->withCount(['rubrics', 'tags', 'images', 'comments', 'likes', 'videos'])
                ->with([
                    'owner',
                    'images'  => fn ($q) => $q->orderBy('order', 'asc'),
                    'rubrics' => fn ($q) => $q->select('rubrics.id', 'title', 'locale'),
                ])
                ->orderBy('sort')
                ->get();

            $articlesCount = $articles->count();

            return Inertia::render('Admin/Blog/Articles/Index', [
                'articles'      => ArticleResource::collection($articles),
                'articlesCount' => $articlesCount,

                'adminCountArticles' => $adminCountArticles,
                'adminSortArticles'  => $adminSortArticles,

                'currentLocale'    => $currentLocale,
                'availableLocales' => $this->availableLocales,
            ]);

        } catch (Throwable $e) {
            Log::error("Ошибка загрузки статей для Index (locale: {$currentLocale}): ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/Articles/Index', [
                'articles'      => [],
                'articlesCount' => 0,

                'adminCountArticles' => $adminCountArticles,
                'adminSortArticles'  => $adminSortArticles,

                'currentLocale'    => $currentLocale,
                'availableLocales' => $this->availableLocales,

                'error' => __('admin/controllers.index_error'),
            ]);
        }
    }

    /**
     * Создание Статьи
     * GET /admin/articles/create?locale=ru
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $targetLocale = $request->query('locale', config('app.fallback_locale', 'ru'));
        if (!in_array($targetLocale, $this->availableLocales, true)) {
            $targetLocale = config('app.fallback_locale', 'ru');
        }

        return Inertia::render('Admin/Blog/Articles/Create', array_merge(
            ['targetLocale' => $targetLocale],
            $this->sharedSelects($targetLocale)
        ));
    }

    /**
     * Сохранение новой Статьи
     * POST /admin/articles
     *
     * @param ArticleRequest $request
     * @return RedirectResponse
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // --- связи (sections НЕ используем) ---
        $imagesData  = $data['images'] ?? [];
        $rubricIds   = collect($data['rubrics'] ?? [])->pluck('id')->toArray();
        $tagIds      = collect($data['tags'] ?? [])->pluck('id')->toArray();
        $videoIds    = collect($data['videos'] ?? [])->pluck('id')->toArray();
        $relatedIds  = collect($data['related_articles'] ?? [])->pluck('id')->toArray();

        unset($data['images'], $data['rubrics'], $data['tags'], $data['videos'], $data['related_articles']);

        // owner: принудительно (если не admin)
        $user = auth()->user();
        if ($user && ! $user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderation_note']);
        } else {
            $data['user_id'] = $data['user_id'] ?? ($user?->id);
        }

        try {
            DB::beginTransaction();

            // sort по умолчанию: в конец выбранной локали
            if (!isset($data['sort']) || is_null($data['sort'])) {
                $maxSort = Article::query()->where('locale', $data['locale'])->max('sort');
                $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
            }

            $article = Article::create($data);

            // связи (✅ видео привязываем к статье)
            $article->rubrics()->sync($rubricIds);
            $article->tags()->sync($tagIds);
            $article->videos()->sync($videoIds);
            $article->relatedArticles()->sync($relatedIds);

            // изображения (НЕ меняю твою рабочую логику)
            $imageSyncData = [];
            $imageIndex    = 0;

            foreach ($imagesData as $imageData) {
                $fileKey = "images.{$imageIndex}.file";

                if ($request->hasFile($fileKey)) {
                    $image = ArticleImage::create([
                        'order'   => $imageData['order']   ?? 0,
                        'alt'     => $imageData['alt']     ?? '',
                        'caption' => $imageData['caption'] ?? '',
                    ]);

                    try {
                        $file = $request->file($fileKey);

                        if ($file->isValid()) {
                            $image->addMedia($file)->toMediaCollection('images');
                            $imageSyncData[$image->id] = ['order' => $image->order];
                        } else {
                            Log::warning("Недопустимый файл изображения {$fileKey} для статьи {$article->id}", [
                                'error' => $file->getErrorMessage(),
                            ]);
                            $image->delete();
                            $imageIndex++;
                            continue;
                        }

                    } catch (Throwable $e) {
                        Log::error("Ошибка Spatie media-library в статье {$article->id}, индекс {$imageIndex}: ".$e->getMessage(), [
                            'exception' => $e,
                        ]);
                        $image->delete();
                        $imageIndex++;
                        continue;
                    }
                }

                $imageIndex++;
            }

            $article->images()->sync($imageSyncData);

            DB::commit();

            return redirect()
                ->route('admin.articles.index', ['locale' => $article->locale])
                ->with('success', __('admin/controllers.created_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при создании статьи: ".$e->getMessage(), ['exception' => $e]);

            return back()->withInput()->with('error', __('admin/controllers.created_error'));
        }
    }

    /**
     * Редактирование Статьи
     * GET /admin/articles/{article}/edit?locale=ru
     *
     * @param Request $request
     * @param int $article
     * @return Response
     */
    public function edit(Request $request, int $article): Response
    {
        $articleModel = $this->baseQuery()
            ->with([
                'rubrics',
                'tags',
                'videos',
                'relatedArticles',
                'images' => fn ($q) => $q->orderBy('order', 'asc'),
            ])
            ->findOrFail($article);

        $targetLocale = $request->query('locale', $articleModel->locale);
        if (!in_array($targetLocale, $this->availableLocales, true)) {
            $targetLocale = $articleModel->locale;
        }

        return Inertia::render('Admin/Blog/Articles/Edit', array_merge(
            ['article' => new ArticleResource($articleModel), 'targetLocale' => $targetLocale],
            $this->sharedSelects($articleModel->locale, $articleModel->id)
        ));
    }

    /**
     * Обновление Статьи
     * PUT/PATCH /admin/articles/{article}
     *
     * @param ArticleRequest $request
     * @param int $article
     * @return RedirectResponse
     */
    public function update(ArticleRequest $request, int $article): RedirectResponse
    {
        $articleModel = $this->baseQuery()->with('images')->findOrFail($article);

        $data = $request->validated();

        $imagesData      = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        $rubricIds       = collect($data['rubrics'] ?? [])->pluck('id')->toArray();
        $tagIds          = collect($data['tags'] ?? [])->pluck('id')->toArray();
        $videoIds        = collect($data['videos'] ?? [])->pluck('id')->toArray();
        $relatedIds      = collect($data['related_articles'] ?? [])->pluck('id')->toArray();

        unset(
            $data['images'],
            $data['deletedImages'],
            $data['rubrics'],
            $data['tags'],
            $data['videos'],
            $data['related_articles'],
            $data['_method']
        );

        // owner: принудительно и без модерации
        $user = auth()->user();
        if ($user && ! $user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderation_note']);
        }

        try {
            DB::transaction(function () use ($articleModel, $request, $data, $imagesData, $deletedImageIds, $rubricIds, $tagIds, $videoIds, $relatedIds) {

                // 1) удалить выбранные изображения
                if (!empty($deletedImageIds)) {
                    $articleModel->images()->detach($deletedImageIds);
                    $this->deleteImages($deletedImageIds);
                }

                // 2) обновить поля
                $articleModel->update($data);

                // 3) sync связей
                $articleModel->rubrics()->sync($rubricIds);
                $articleModel->tags()->sync($tagIds);
                $articleModel->videos()->sync($videoIds);
                $articleModel->relatedArticles()->sync($relatedIds);

                // 4) sync images (твоя логика)
                $syncData = [];

                foreach ($imagesData as $index => $imageData) {
                    $fileKey = "images.{$index}.file";

                    if (!empty($imageData['id'])) {
                        $img = ArticleImage::find($imageData['id']);

                        if ($img && !in_array($img->id, $deletedImageIds, true)) {
                            $img->update([
                                'order'   => $imageData['order']   ?? $img->order,
                                'alt'     => $imageData['alt']     ?? $img->alt,
                                'caption' => $imageData['caption'] ?? $img->caption,
                            ]);

                            if ($request->hasFile($fileKey)) {
                                $img->clearMediaCollection('images');
                                $img->addMedia($request->file($fileKey))->toMediaCollection('images');
                            }

                            $syncData[$img->id] = ['order' => $img->order];
                        }
                    } elseif ($request->hasFile($fileKey)) {
                        $new = ArticleImage::create([
                            'order'   => $imageData['order']   ?? 0,
                            'alt'     => $imageData['alt']     ?? '',
                            'caption' => $imageData['caption'] ?? '',
                        ]);

                        $new->addMedia($request->file($fileKey))->toMediaCollection('images');
                        $syncData[$new->id] = ['order' => $new->order];
                    }
                }

                $articleModel->images()->sync($syncData);
            });

            return redirect()
                ->route('admin.articles.index', ['locale' => $articleModel->locale])
                ->with('success', __('admin/controllers.updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка при обновлении статьи ID {$articleModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('error', __('admin/controllers.updated_error'));
        }
    }

    /**
     * Удаление Статьи
     * DELETE /admin/articles/{article}
     *
     * @param int $article
     * @return RedirectResponse
     */
    public function destroy(int $article): RedirectResponse
    {
        $articleModel = $this->baseQuery()->with('images')->findOrFail($article);

        try {
            DB::beginTransaction();

            $imageIds = $articleModel->images()->pluck('id')->toArray();
            if (!empty($imageIds)) {
                DB::table('article_has_images')->where('article_id', $articleModel->id)->delete();
                $this->deleteImages($imageIds);
            }

            $articleModel->delete();

            DB::commit();

            return redirect()
                ->route('admin.articles.index', ['locale' => $articleModel->locale])
                ->with('success', __('admin/controllers.deleted_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при удалении статьи ID {$articleModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.deleted_error'));
        }
    }

    /**
     * Массовое удаление
     * DELETE /admin/actions/articles/bulk-destroy
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required','array'],
            'ids.*' => ['required','integer','exists:articles,id'],
        ]);

        $ids = $validated['ids'];
        $count = count($ids);

        $allowedIds = $this->baseQuery()->whereIn('id', $ids)->pluck('id')->toArray();
        if (count($allowedIds) !== $count) {
            return back()->with('error', __('admin/controllers.bulk_deleted_error'));
        }

        try {
            DB::beginTransaction();

            $allImageIds = ArticleImage::whereHas('articles', fn ($q) => $q->whereIn('articles.id', $allowedIds))
                ->pluck('id')->toArray();

            if (!empty($allImageIds)) {
                DB::table('article_has_images')->whereIn('article_id', $allowedIds)->delete();
                $this->deleteImages($allImageIds);
            }

            Article::whereIn('id', $allowedIds)->delete();

            DB::commit();

            return redirect()
                ->route('admin.articles.index')
                ->with('success', __('admin/controllers.bulk_deleted_success', ['count' => $count]));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка массового удаления статей: ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.bulk_deleted_error'));
        }
    }

    /**
     * Обновление в левой колонке
     * PUT /admin/actions/articles/{article}/left
     *
     * @param UpdateLeftRequest $request
     * @param int $article
     * @return RedirectResponse
     */
    public function updateLeft(UpdateLeftRequest $request, int $article): RedirectResponse
    {
        $articleModel = $this->baseQuery()->findOrFail($article);
        $validated = $request->validated();

        try {
            $articleModel->left = $validated['left'];
            $articleModel->save();

            return redirect()
                ->route('admin.articles.index', ['locale' => $articleModel->locale])
                ->with('success', __('admin/controllers.left_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка updateLeft статьи {$articleModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.left_updated_error'));
        }
    }

    /**
     * Массовое обновление в левой колонке
     * PUT /admin/actions/articles/bulk-left
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkUpdateLeft(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids'   => ['required','array'],
            'ids.*' => ['required','integer','exists:articles,id'],
            'left'  => ['required','boolean'],
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $validated['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($validated['ids'])) {
            return response()->json(['success' => false, 'message' => 'forbidden'], 403);
        }

        try {
            $updatedCount = Article::whereIn('id', $allowedIds)->update(['left' => $validated['left']]);
            return response()->json(['success' => true, 'updatedCount' => $updatedCount]);

        } catch (Throwable $e) {
            Log::error('bulkUpdateLeft error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_left_updated_error')], 500);
        }
    }

    /**
     * Обновление в главном
     * PUT /admin/actions/articles/{article}/main
     *
     * @param UpdateMainRequest $request
     * @param int $article
     * @return RedirectResponse
     */
    public function updateMain(UpdateMainRequest $request, int $article): RedirectResponse
    {
        $articleModel = $this->baseQuery()->findOrFail($article);
        $validated = $request->validated();

        try {
            $articleModel->main = $validated['main'];
            $articleModel->save();

            return redirect()
                ->route('admin.articles.index', ['locale' => $articleModel->locale])
                ->with('success', __('admin/controllers.main_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка updateMain статьи {$articleModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.main_updated_error'));
        }
    }

    /**
     * Массовое обновление в главном
     * PUT /admin/actions/articles/bulk-main
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkUpdateMain(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids'   => ['required','array'],
            'ids.*' => ['required','integer','exists:articles,id'],
            'main'  => ['required','boolean'],
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $validated['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($validated['ids'])) {
            return response()->json(['success' => false, 'message' => 'forbidden'], 403);
        }

        try {
            $updatedCount = Article::whereIn('id', $allowedIds)->update(['main' => $validated['main']]);
            return response()->json(['success' => true, 'updatedCount' => $updatedCount]);

        } catch (Throwable $e) {
            Log::error('bulkUpdateMain error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_main_updated_error')], 500);
        }
    }

    /**
     * Обновление в правой колонке
     * PUT /admin/actions/articles/{article}/right
     *
     * @param UpdateRightRequest $request
     * @param int $article
     * @return RedirectResponse
     */
    public function updateRight(UpdateRightRequest $request, int $article): RedirectResponse
    {
        $articleModel = $this->baseQuery()->findOrFail($article);
        $validated = $request->validated();

        try {
            $articleModel->right = $validated['right'];
            $articleModel->save();

            return redirect()
                ->route('admin.articles.index', ['locale' => $articleModel->locale])
                ->with('success', __('admin/controllers.right_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка updateRight статьи {$articleModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.right_updated_error'));
        }
    }

    /**
     * Массовое обновление в правой колонке
     *  PUT /admin/actions/articles/bulk-right
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkUpdateRight(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids'   => ['required','array'],
            'ids.*' => ['required','integer','exists:articles,id'],
            'right' => ['required','boolean'],
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $validated['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($validated['ids'])) {
            return response()->json(['success' => false, 'message' => 'forbidden'], 403);
        }

        try {
            $updatedCount = Article::whereIn('id', $allowedIds)->update(['right' => $validated['right']]);
            return response()->json(['success' => true, 'updatedCount' => $updatedCount]);

        } catch (Throwable $e) {
            Log::error('bulkUpdateRight error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => __('admin/controllers.bulk_right_updated_error')], 500);
        }
    }

    /**
     * Обновление активности
     * PUT /admin/actions/articles/{article}/activity
     *
     * @param UpdateActivityRequest $request
     * @param int $article
     * @return RedirectResponse
     */
    public function updateActivity(UpdateActivityRequest $request, int $article): RedirectResponse
    {
        $articleModel = $this->baseQuery()->findOrFail($article);
        $validated = $request->validated();

        try {
            DB::beginTransaction();
            $articleModel->activity = $validated['activity'];
            $articleModel->save();
            DB::commit();

            return back()->with('success', __('admin/controllers.activity_updated_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка updateActivity статьи {$articleModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.activity_updated_error'));
        }
    }

    /**
     * Массовое обновление активности
     * PUT /admin/actions/articles/bulk-activity
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids'      => ['required','array'],
            'ids.*'    => ['required','integer','exists:articles,id'],
            'activity' => ['required','boolean'],
        ]);

        $allowedIds = $this->baseQuery()->whereIn('id', $validated['ids'])->pluck('id')->toArray();
        if (count($allowedIds) !== count($validated['ids'])) {
            return back()->with('error', __('admin/controllers.bulk_activity_updated_error'));
        }

        try {
            $updatedCount = Article::whereIn('id', $allowedIds)->update(['activity' => $validated['activity']]);

            $msg = __('admin/controllers.bulk_activity_updated_success');
            return $request->expectsJson()
                ? response()->json(['message' => $msg, 'updatedCount' => $updatedCount])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка bulkUpdateActivity статей: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.bulk_activity_updated_error');
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Обновление сортировки
     * PUT /admin/actions/articles/{article}/sort
     *
     * @param UpdateSortEntityRequest $request
     * @param int $article
     * @return RedirectResponse
     */
    public function updateSort(UpdateSortEntityRequest $request, int $article): RedirectResponse
    {
        $articleModel = $this->baseQuery()->findOrFail($article);
        $validated = $request->validated();

        try {
            $articleModel->sort = $validated['sort'];
            $articleModel->save();

            return back()->with('success', __('admin/controllers.sort_updated_success'));

        } catch (Throwable $e) {
            Log::error("Ошибка updateSort статьи {$articleModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->with('error', __('admin/controllers.sort_updated_error'));
        }
    }

    /**
     * Массовое обновление сортировки
     * PUT /admin/actions/articles/update-sort-bulk
     *
     * Поддержка payload:
     * - items: [{id, sort}]
     * - либо старый articles: [{id, sort}]
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function updateSortBulk(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['nullable','string', Rule::in($this->availableLocales)],

            'items'             => ['required_without:articles','array'],
            'items.*.id'        => ['required_with:items','integer','exists:articles,id'],
            'items.*.sort'      => ['required_with:items','integer','min:0'],

            'articles'          => ['required_without:items','array'],
            'articles.*.id'     => ['required_with:articles','integer','exists:articles,id'],
            'articles.*.sort'   => ['required_with:articles','integer','min:0'],
        ]);

        $data = $validated['items'] ?? $validated['articles'];

        try {
            DB::transaction(function () use ($data, $validated) {

                $ids = array_column($data, 'id');

                $q = $this->baseQuery()->whereIn('id', $ids);
                if (!empty($validated['locale'])) {
                    $q->where('locale', $validated['locale']);
                }

                $allowedIds = $q->pluck('id')->toArray();
                if (count($allowedIds) !== count($ids)) {
                    abort(403);
                }

                foreach ($data as $row) {
                    Article::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                    ]);
                }
            });

            $msg = __('admin/controllers.bulk_sort_updated_success');
            return $request->expectsJson()
                ? response()->json(['message' => $msg])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Bulk sort articles error: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.bulk_sort_updated_error');
            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Клонирование
     * POST /admin/actions/articles/{article}/clone
     *
     * @param Request $request
     * @param int $article
     * @return RedirectResponse
     */
    public function clone(Request $request, int $article): RedirectResponse
    {
        $articleModel = $this->baseQuery()
            ->with(['rubrics', 'tags', 'videos', 'images', 'relatedArticles'])
            ->findOrFail($article);

        DB::beginTransaction();

        try {
            $cloned = $articleModel->replicate();

            $user = auth()->user();
            if ($user && ! $user->hasRole('admin')) {
                $cloned->user_id = $user->id;
            }

            $cloned->title = $articleModel->title.'-2';
            $cloned->url   = $articleModel->url.'-2';

            $cloned->activity = false;
            $cloned->views    = 0;
            $cloned->created_at = now();
            $cloned->updated_at = now();

            // sort в конец локали
            $maxSort = Article::query()->where('locale', $cloned->locale)->max('sort');
            $cloned->sort = is_null($maxSort) ? 0 : $maxSort + 1;

            $cloned->save();

            $cloned->rubrics()->sync($articleModel->rubrics()->pluck('id'));
            $cloned->tags()->sync($articleModel->tags()->pluck('id'));
            $cloned->videos()->sync($articleModel->videos()->pluck('id'));
            $cloned->relatedArticles()->sync($articleModel->relatedArticles()->pluck('id'));

            // изображения (твоя идея)
            $imageSyncData = [];
            foreach ($articleModel->images as $image) {
                $clonedImage = $image->replicate();
                $clonedImage->save();

                $originalMedia = $image->getFirstMedia('images');
                if ($originalMedia) {
                    try {
                        $originalMedia->copy($clonedImage, 'images');
                        $imageSyncData[$clonedImage->id] = ['order' => $image->pivot->order ?? $image->order ?? 0];
                    } catch (Throwable $e) {
                        Log::error("Ошибка копирования медиа при клонировании статьи: ".$e->getMessage(), ['exception' => $e]);
                    }
                }
            }
            $cloned->images()->sync($imageSyncData);

            DB::commit();

            return redirect()
                ->route('admin.articles.index', ['locale' => $cloned->locale])
                ->with('success', __('admin/controllers.cloned_success'));

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Ошибка при клонировании статьи ID {$articleModel->id}: ".$e->getMessage(), ['exception' => $e]);
            return back()->withInput()->with('error', __('admin/controllers.cloned_error'));
        }
    }

    /**
     * Одобрение admin
     * PUT/POST /admin/actions/articles/{article}/approve
     *
     * @param Request $request
     * @param int $article
     * @return RedirectResponse|JsonResponse
     */
    public function approve(Request $request, int $article): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('admin')) {
            abort(403);
        }

        $articleModel = Article::query()->findOrFail($article);

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', Rule::in([0, 1, 2])],
            'moderation_note'   => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $articleModel->update([
                'moderation_status' => (int) $validated['moderation_status'],
                'moderation_note'   => $validated['moderation_note'] ?? null,
                'moderated_by'      => $user->id,
                'moderated_at'      => now(),
            ]);

            $msg = __('admin/controllers.updated_success');

            return $request->expectsJson()
                ? response()->json(['message' => $msg, 'article' => new ArticleResource($articleModel)])
                : back()->with('success', $msg);

        } catch (Throwable $e) {
            Log::error("Ошибка approve статьи {$articleModel->id}: ".$e->getMessage(), ['exception' => $e]);

            $msg = __('admin/controllers.updated_error');

            return $request->expectsJson()
                ? response()->json(['message' => $msg], 500)
                : back()->with('error', $msg);
        }
    }

    /**
     * Приватный метод удаления изображений (Spatie).
     *
     * @param array $imageIds
     * @return void
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) return;

        $imagesToDelete = ArticleImage::whereIn('id', $imageIds)->get();

        foreach ($imagesToDelete as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }

        Log::info('Удалены записи ArticleImage и их медиа', ['image_ids' => $imageIds]);
    }
}
