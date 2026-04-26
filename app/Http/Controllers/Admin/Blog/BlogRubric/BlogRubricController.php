<?php

namespace App\Http\Controllers\Admin\Blog\BlogRubric;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\BlogRubric\BlogRubricRequest;
use App\Http\Requests\Admin\System\UpdateActivityRequest;
use App\Http\Requests\Admin\System\UpdateSortEntityRequest;
use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricResource;
use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricSharedResource;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogRubric\BlogRubricImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;
use Throwable;

/**
 * Контроллер для управления Рубриками (Blog) в админке.
 *
 * Паттерн:
 * - локали (табы)
 * - дерево (root + children)
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - activity (single + bulk)
 * - sort + parent_id после drag&drop (bulk)
 * - moderation (approve/reject) только для admin
 * - images (Spatie)
 *
 * @version 1.1 (Улучшен с RMB, транзакциями, Form Requests)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 *
 * @see BlogRubric
 * @see BlogRubricRequest
 */
class BlogRubricController extends Controller
{
    /**
     * Берём все разрешенные языки из config/app.php
     * Используется для табов переводов
     */
    private function availableLocales(): array
    {
        return config('app.available_locales', ['ru']);
    }

    /**
     * Базовый запрос с учётом прав:
     * - admin видит всё
     * - обычный пользователь только свои записи
     */
    private function baseQuery(): Builder
    {
        $query = BlogRubric::query();

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Определение уровня вложенности рубрики:
     * root = 1
     * дочерние = parent.level + 1
     */
    private function resolveLevel(?int $parentId): int
    {
        if (!$parentId) {
            return 1;
        }

        $parent = $this->baseQuery()
            ->select('id', 'level')
            ->find($parentId);

        if (!$parent) {
            return 1;
        }

        return ((int) $parent->level) + 1;
    }

    /**
     * Проверка допустимой глубины вложенности (макс 3 уровня)
     */
    private function ensureAllowedLevel(?int $parentId): void
    {
        $level = $this->resolveLevel($parentId);

        if ($level > 3) {
            throw new InvalidArgumentException('Нельзя создавать рубрику глубже 3 уровня вложенности.');
        }
    }

    /**
     * Нормализация локали:
     * если локаль невалидна → fallback
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
     * Приведение параметра сортировки из UI к внутреннему формату
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
            default => $sort ?: 'sort_asc',
        };
    }

    /**
     * Синхронизация переводов:
     * - updateOrCreate для текущих
     * - удаление отсутствующих
     */
    private function syncTranslations(BlogRubric $rubric, array $translations): void
    {
        $locales = array_keys($translations);

        foreach ($translations as $locale => $translationData) {
            $rubric->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $translationData['title'] ?? null,
                    'subtitle' => $translationData['subtitle'] ?? null,
                    'short' => $translationData['short'] ?? null,
                    'description' => $translationData['description'] ?? null,
                    'meta_title' => $translationData['meta_title'] ?? null,
                    'meta_keywords' => $translationData['meta_keywords'] ?? null,
                    'meta_desc' => $translationData['meta_desc'] ?? null,
                ]
            );
        }

        $rubric->translations()
            ->whereNotIn('locale', $locales)
            ->delete();
    }

    /**
     * Синхронизация изображений:
     * - обновление существующих
     * - добавление новых
     * - удаление помеченных
     * - синхронизация pivot (order)
     */
    private function syncImages(BlogRubric $rubric, Request $request, array $imagesData, array $deletedImageIds = []): void
    {
        if (!empty($deletedImageIds)) {
            $rubric->images()->detach($deletedImageIds);
            $this->deleteImages($deletedImageIds);
        }

        $syncData = [];

        foreach ($imagesData as $index => $imageData) {
            $fileKey = "images.{$index}.file";

            // существующее изображение
            if (!empty($imageData['id'])) {
                $image = BlogRubricImage::find($imageData['id']);

                if (!$image || in_array((int) $image->id, $deletedImageIds, true)) {
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

                $syncData[$image->id] = ['order' => $image->order];
                continue;
            }

            // новое изображение
            if ($request->hasFile($fileKey)) {
                $image = BlogRubricImage::create([
                    'order' => $imageData['order'] ?? 0,
                    'alt' => $imageData['alt'] ?? '',
                    'caption' => $imageData['caption'] ?? '',
                ]);

                $image->addMedia($request->file($fileKey))->toMediaCollection('images');

                $syncData[$image->id] = ['order' => $image->order];
            }
        }

        $existingIds = $rubric->images()
            ->whereNotIn('blog_rubric_images.id', $deletedImageIds)
            ->pluck('blog_rubric_images.id')
            ->toArray();

        foreach ($existingIds as $existingId) {
            if (!array_key_exists($existingId, $syncData)) {
                $existingImage = BlogRubricImage::find($existingId);
                if ($existingImage) {
                    $syncData[$existingId] = ['order' => $existingImage->order];
                }
            }
        }

        $rubric->images()->sync($syncData);
    }

    /**
     * Полное удаление изображений:
     * - удаление медиа (Spatie)
     * - удаление записи из БД
     */
    private function deleteImages(array $imageIds): void
    {
        if (empty($imageIds)) {
            return;
        }

        $images = BlogRubricImage::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            $image->clearMediaCollection('images');
            $image->delete();
        }
    }

    /**
     * Подготовка дерева:
     * childrenRecursive → children
     * для удобства фронта (Vue)
     */
    private function prepareTreeChildren($nodes): void
    {
        $nodes->each(function ($node) {
            if ($node->relationLoaded('childrenRecursive')) {
                $node->setRelation('children', $node->childrenRecursive);
                $this->prepareTreeChildren($node->childrenRecursive);
            } elseif ($node->relationLoaded('children')) {
                $this->prepareTreeChildren($node->children);
            }
        });
    }

    /**
     * Список рубрик:
     * - дерево (для drag&drop)
     * - плоский список (для таблицы)
     * - поиск + сортировка + локаль
     */
    public function index(Request $request): Response
    {
        $adminCountRubrics = (int) config('site_settings.AdminCountRubrics', 15);
        $adminSortRubrics = (string) config('site_settings.AdminSortRubrics', 'idDesc');

        $currentLocale = $this->normalizeLocale($request->query('locale'));
        $search = trim((string) $request->query('search', ''));
        $sortParam = $this->normalizeSortParam($request->query('sort', $adminSortRubrics));

        try {
            $rubricsTree = $this->baseQuery()
                ->roots()
                ->with([
                    'owner',
                    'moderator',
                    'images',
                    'translations',
                    'childrenRecursive',
                ])
                ->withCount(['articles', 'images'])
                ->ordered()
                ->get();

            $this->prepareTreeChildren($rubricsTree);

            $rubricsFlat = $this->baseQuery()
                ->with([
                    'owner',
                    'moderator',
                    'images',
                    'translations',
                ])
                ->withCount(['articles', 'images'])
                ->search($search, $currentLocale)
                ->sortByParam($sortParam, $currentLocale)
                ->get();

            $rubricsCount = $this->baseQuery()->count();

            return Inertia::render('Admin/Blog/BlogRubrics/Index', [
                'rubricsTree' => BlogRubricResource::collection($rubricsTree),
                'rubrics' => BlogRubricResource::collection($rubricsFlat),
                'rubricsCount' => $rubricsCount,

                'adminCountRubrics' => $adminCountRubrics,
                'adminSortRubrics' => $adminSortRubrics,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'search' => $search,
                'sortParam' => $sortParam,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка blog rubrics: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Blog/BlogRubrics/Index', [
                'rubricsTree' => [],
                'rubrics' => [],
                'rubricsCount' => 0,

                'adminCountRubrics' => $adminCountRubrics,
                'adminSortRubrics' => $adminSortRubrics,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
                'search' => $search,
                'sortParam' => $sortParam,
                'error' => 'Ошибка загрузки рубрик.',
            ]);
        }
    }

    /**
     * Страница создания рубрики:
     * - список родителей
     * - текущая локаль
     */
    public function create(Request $request): Response
    {
        $targetLocale = $this->normalizeLocale($request->query('locale'));

        $parents = $this->baseQuery()
            ->with(['translations'])
            ->ordered()
            ->get();

        return Inertia::render('Admin/Blog/BlogRubrics/Create', [
            'targetLocale' => $targetLocale,
            'parents' => BlogRubricSharedResource::collection($parents),
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /**
     * Создание рубрики:
     * - проверка уровня вложенности
     * - авто sort
     * - сохранение переводов
     * - сохранение изображений
     */
    public function store(BlogRubricRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset($data['translations'], $data['images'], $data['deletedImages']);

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderated_by'], $data['moderated_at'], $data['moderation_note']);
        } else {
            $data['user_id'] = $data['user_id'] ?? $user?->id;
        }

        try {
            DB::transaction(function () use ($request, &$rubric, $data, $translations, $imagesData) {
                $this->ensureAllowedLevel($data['parent_id'] ?? null);

                $data['level'] = $this->resolveLevel($data['parent_id'] ?? null);

                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = BlogRubric::query()
                        ->where('parent_id', $data['parent_id'] ?? null)
                        ->max('sort');

                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $rubric = BlogRubric::create($data);

                $this->syncTranslations($rubric, $translations);
                $this->syncImages($rubric, $request, $imagesData);
            });

            return redirect()
                ->route('admin.blogRubrics.index')
                ->with('success', 'Рубрика успешно создана.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании blog rubric: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', $e instanceof InvalidArgumentException ? $e->getMessage() : 'Ошибка при создании рубрики.');
        }
    }

    /**
     * Редирект на страницу редактирования
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.blogRubrics.edit', $id);
    }

    /**
     * Страница редактирования:
     * - загрузка рубрики с переводами
     * - список возможных родителей
     */
    public function edit(int $blogRubric, Request $request): Response|RedirectResponse
    {
        $rubric = $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'images',
                'translations',
            ])
            ->withCount(['articles', 'images'])
            ->findOrFail($blogRubric);

        $targetLocale = $this->normalizeLocale($request->query('locale'));

        $parents = $this->baseQuery()
            ->where('id', '!=', $rubric->id)
            ->with(['translations'])
            ->ordered()
            ->get();

        return Inertia::render('Admin/Blog/BlogRubrics/Edit', [
            'rubric' => new BlogRubricResource($rubric),
            'parents' => BlogRubricSharedResource::collection($parents),
            'targetLocale' => $targetLocale,
            'availableLocales' => $this->availableLocales(),
        ]);
    }

    /**
     * Обновление рубрики:
     * - защита от self-parent
     * - обновление уровня
     * - синхронизация переводов и изображений
     */
    public function update(BlogRubricRequest $request, int $blogRubric): RedirectResponse
    {
        $rubric = $this->baseQuery()
            ->with('images')
            ->findOrFail($blogRubric);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset($data['translations'], $data['images'], $data['deletedImages']);

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $data['user_id'] = $user->id;
            unset($data['moderation_status'], $data['moderated_by'], $data['moderated_at'], $data['moderation_note']);
        }

        try {
            DB::transaction(function () use ($request, $rubric, $data, $translations, $imagesData, $deletedImageIds) {
                if (!empty($data['parent_id']) && (int) $data['parent_id'] === (int) $rubric->id) {
                    throw new InvalidArgumentException('Рубрика не может быть родителем самой себе.');
                }

                $this->ensureAllowedLevel($data['parent_id'] ?? null);

                $data['level'] = $this->resolveLevel($data['parent_id'] ?? null);

                $rubric->update($data);

                $this->syncTranslations($rubric, $translations);
                $this->syncImages($rubric, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.blogRubrics.index')
                ->with('success', 'Рубрика успешно обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка при обновлении blog rubric ID ' . $rubric->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', $e instanceof InvalidArgumentException ? $e->getMessage() : 'Ошибка при обновлении рубрики.');
        }
    }

    /**
     * Удаление рубрики:
     * - запрещено если есть дочерние
     * - удаление изображений
     * - удаление переводов
     */
    public function destroy(int $blogRubric): RedirectResponse
    {
        $rubric = $this->baseQuery()
            ->with('images')
            ->findOrFail($blogRubric);

        try {
            DB::transaction(function () use ($rubric) {
                if ($rubric->children()->exists()) {
                    throw new InvalidArgumentException('Нельзя удалить рубрику: сначала удалите или переместите дочерние рубрики.');
                }

                $imageIds = $rubric->images()->pluck('blog_rubric_images.id')->toArray();

                if (!empty($imageIds)) {
                    $rubric->images()->detach();
                    $this->deleteImages($imageIds);
                }

                $rubric->translations()->delete();
                $rubric->delete();
            });

            return redirect()
                ->route('admin.blogRubrics.index')
                ->with('success', 'Рубрика успешно удалена.');
        } catch (Throwable $e) {
            Log::error('Ошибка при удалении blog rubric ID ' . $rubric->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', $e instanceof InvalidArgumentException ? $e->getMessage() : 'Ошибка при удалении рубрики.');
        }
    }

    /**
     * Массовое удаление:
     * - проверка доступа
     * - проверка отсутствия детей
     * - удаление pivot + изображений + переводов
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_rubrics,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть рубрик недоступна для удаления.');
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                $hasChildren = BlogRubric::whereIn('parent_id', $allowedIds)->exists();

                if ($hasChildren) {
                    throw new InvalidArgumentException('Нельзя удалить выбранные рубрики, пока у них есть дочерние элементы.');
                }

                $imageIds = BlogRubricImage::whereHas('rubrics', function ($query) use ($allowedIds) {
                    $query->whereIn('blog_rubrics.id', $allowedIds);
                })->pluck('id')->toArray();

                DB::table('blog_rubric_has_images')
                    ->whereIn('rubric_id', $allowedIds)
                    ->delete();

                if (!empty($imageIds)) {
                    $this->deleteImages($imageIds);
                }

                DB::table('blog_rubric_translations')
                    ->whereIn('rubric_id', $allowedIds)
                    ->delete();

                BlogRubric::whereIn('id', $allowedIds)->delete();
            });

            return back()->with('success', 'Выбранные рубрики успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy blog rubrics: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', $e instanceof InvalidArgumentException ? $e->getMessage() : 'Ошибка при массовом удалении рубрик.');
        }
    }

    /**
     * Обновление активности одной рубрики
     */
    public function updateActivity(UpdateActivityRequest $request, int $blogRubric): RedirectResponse
    {
        $rubric = $this->baseQuery()->findOrFail($blogRubric);

        $rubric->update([
            'activity' => $request->validated('activity'),
        ]);

        return back()->with('success', 'Активность рубрики обновлена.');
    }

    /**
     * Массовое обновление активности
     */
    public function bulkUpdateActivity(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:blog_rubrics,id'],
            'activity' => ['required', 'boolean'],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $validated['ids'])
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            return back()->with('error', 'Часть рубрик недоступна для обновления активности.');
        }

        BlogRubric::whereIn('id', $allowedIds)->update([
            'activity' => $validated['activity'],
        ]);

        $message = 'Активность выбранных рубрик обновлена.';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }

    /**
     * Обновление сортировки одной записи
     */
    public function updateSort(UpdateSortEntityRequest $request, int $blogRubric): RedirectResponse
    {
        $rubric = $this->baseQuery()->findOrFail($blogRubric);

        $rubric->update([
            'sort' => $request->validated('sort'),
        ]);

        return back()->with('success', 'Сортировка рубрики обновлена.');
    }

    /**
     * Массовое обновление сортировки (drag & drop):
     * - обновляет sort + parent_id + level
     */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required_without:rubrics', 'array'],
            'items.*.id' => ['required_with:items', 'integer', 'exists:blog_rubrics,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],
            'items.*.parent_id' => ['nullable', 'integer', 'exists:blog_rubrics,id'],

            'rubrics' => ['required_without:items', 'array'],
            'rubrics.*.id' => ['required_with:rubrics', 'integer', 'exists:blog_rubrics,id'],
            'rubrics.*.sort' => ['required_with:rubrics', 'integer', 'min:0'],
            'rubrics.*.parent_id' => ['nullable', 'integer', 'exists:blog_rubrics,id'],
        ]);

        $items = $validated['items'] ?? $validated['rubrics'];
        $ids = array_column($items, 'id');

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            $message = 'Часть рубрик недоступна для изменения сортировки.';
            return $request->expectsJson()
                ? response()->json(['message' => $message], 400)
                : back()->with('error', $message);
        }

        try {
            DB::transaction(function () use ($items) {
                foreach ($items as $row) {
                    if (!empty($row['parent_id']) && (int) $row['parent_id'] === (int) $row['id']) {
                        throw new InvalidArgumentException('Рубрика не может быть родителем самой себе.');
                    }

                    $this->ensureAllowedLevel($row['parent_id'] ?? null);

                    BlogRubric::whereKey($row['id'])->update([
                        'sort' => (int) $row['sort'],
                        'parent_id' => $row['parent_id'] ?? null,
                        'level' => $this->resolveLevel($row['parent_id'] ?? null),
                    ]);
                }
            });

            $message = 'Сортировка дерева рубрик обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка updateSortBulk blog rubrics: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'Ошибка при массовом обновлении сортировки рубрик.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /**
     * Клонирование рубрики:
     * - копия записи
     * - копия переводов
     * - копия изображений (Spatie)
     */
    public function clone(int $blogRubric): RedirectResponse
    {
        $rubric = $this->baseQuery()
            ->with(['images', 'translations'])
            ->findOrFail($blogRubric);

        try {
            DB::transaction(function () use ($rubric, &$cloned) {
                $this->ensureAllowedLevel($rubric->parent_id);

                $cloned = $rubric->replicate();
                $cloned->url = $rubric->url . '-copy-' . now()->timestamp;
                $cloned->activity = false;
                $cloned->views = 0;
                $cloned->level = $this->resolveLevel($cloned->parent_id);
                $cloned->save();

                foreach ($rubric->translations as $translation) {
                    $cloned->translations()->create([
                        'locale' => $translation->locale,
                        'title' => $translation->title . ' (copy)',
                        'subtitle' => $translation->subtitle,
                        'short' => $translation->short,
                        'description' => $translation->description,
                        'meta_title' => $translation->meta_title,
                        'meta_keywords' => $translation->meta_keywords,
                        'meta_desc' => $translation->meta_desc,
                    ]);
                }

                $syncData = [];

                foreach ($rubric->images as $image) {
                    $newImage = $image->replicate();
                    $newImage->save();

                    $media = $image->getFirstMedia('images');
                    if ($media) {
                        $media->copy($newImage, 'images');
                    }

                    $syncData[$newImage->id] = [
                        'order' => $image->pivot->order ?? $image->order ?? 0,
                    ];
                }

                if (!empty($syncData)) {
                    $cloned->images()->sync($syncData);
                }
            });

            return back()->with('success', 'Рубрика успешно клонирована.');
        } catch (Throwable $e) {
            Log::error('Ошибка clone blog rubric: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', $e instanceof InvalidArgumentException ? $e->getMessage() : 'Ошибка при клонировании рубрики.');
        }
    }

    /**
     * Модерация (approve/reject):
     * доступ только для admin
     */
    public function approve(Request $request, int $blogRubric): RedirectResponse|JsonResponse
    {
        $user = auth()->user();

        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'moderation_status' => ['required', 'integer', Rule::in([0, 1, 2])],
            'moderation_note' => ['nullable', 'string', 'max:500'],
        ]);

        $rubric = BlogRubric::findOrFail($blogRubric);

        $rubric->update([
            'moderation_status' => (int) $validated['moderation_status'],
            'moderation_note' => $validated['moderation_note'] ?? null,
            'moderated_by' => $user->id,
            'moderated_at' => now(),
        ]);

        $message = 'Статус модерации рубрики обновлён.';

        return $request->expectsJson()
            ? response()->json([
                'message' => $message,
                'rubric' => new BlogRubricResource(
                    $rubric->load(['owner', 'moderator', 'translations', 'images'])->loadCount(['articles', 'images'])
                ),
            ])
            : back()->with('success', $message);
    }
}
