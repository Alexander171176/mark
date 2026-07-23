<?php

namespace App\Http\Controllers\Admin\Market\MarketCategory;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketCategory\MarketCategoryRequest;
use App\Http\Resources\Admin\Market\MarketCategory\MarketCategoryResource;
use App\Http\Resources\Admin\Market\MarketCategory\MarketCategorySharedResource;
use App\Models\Admin\Market\MarketCategory\MarketCategory;
use App\Models\Admin\Market\MarketCategory\MarketCategoryImage;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;
use Throwable;

/**
 * Контроллер для управления Категориями товаров (MarketCategory) в админке.
 *
 * Паттерн:
 * - Поиск, Пагинация, сортировка (режимы: frontend | auto | server )
 * - CRUD
 * - owner/ограничение “владелец/админ”
 * - delete (single + bulk)
 * - activity (single + bulk)
 * - sort + drag&drop (bulk) в карточках
 * - moderation (approve/reject) только для admin
 * - images + сервис обработки изображений.
 * - вид деревом и карточками
 *
 * @version 1.1 (мультиязычеая архитектура)
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */
class MarketCategoryController extends BaseMarketAdminController
{
    /** Основная модель */
    protected string $modelClass = MarketCategory::class;

    /** Модель изображений */
    protected string $imageModelClass = MarketCategoryImage::class;

    /** Коллекция изображений */
    protected string $imageMediaCollection = 'images';

    /** Название сущности */
    protected string $entityLabel = 'категорий';

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

    /** Пресет для галереи категории */
    protected string $imagePresetKey = 'rectangle_large';

    /** Директория для обработанных изображений */
    protected string $imagePresetDirectory = 'market/market_category_images/preset';

    /** Максимальный уровень вложенности */
    protected int $maxCategoryLevel = 3;

    /** Список категорий */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminMarketCategoriesPerPage', 6);
        $defaultSort = $settings->string('adminMarketCategoriesDefaultSort', 'idDesc');

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $processingMode = $settings->string(
            'adminMarketCategoriesProcessingMode',
            'frontend'
        );

        $categoriesCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $categoriesCount,
                300
            );

        try {
            $categoriesTree = $this->getIndexCategoriesTree();

            $this->prepareTreeChildren($categoriesTree);

            $categoriesFlat = $this->getIndexCategories(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render('Admin/Market/MarketCategories/Index', [
                'categoriesTree' => MarketCategoryResource::collection($categoriesTree),
                'categories' => MarketCategoryResource::collection($categoriesFlat),
                'categoriesCount' => $categoriesCount,

                'useServerProcessing' => $useServerProcessing,

                'adminMarketCategoriesPerPage' => $perPage,
                'adminMarketCategoriesDefaultSort' => $defaultSort,
                'adminMarketCategoriesProcessingMode' => $processingMode,

                'sortParam' => $sortParam,
                'search' => $search,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки списка market categories: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Market/MarketCategories/Index', [
                'categoriesTree' => [],
                'categories' => [],
                'categoriesCount' => 0,

                'useServerProcessing' => $useServerProcessing,

                'adminMarketCategoriesPerPage' => $perPage,
                'adminMarketCategoriesDefaultSort' => $defaultSort,
                'adminMarketCategoriesProcessingMode' => $processingMode,

                'sortParam' => $sortParam,
                'search' => $search,

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'error' => 'Ошибка загрузки категорий.',
            ]);
        }
    }

    /** Страница создания категории */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $parents = $this->baseQuery()
            ->with(['translations'])
            ->withCount(['children', 'images'])
            ->ordered()
            ->get();

        return Inertia::render('Admin/Market/MarketCategories/Create', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'parents' => MarketCategorySharedResource::collection($parents),

            'imageProcessorEnabled' => $this->imageProcessorEnabled(),
            'imagePreset' => $this->imagePresetPayload(),
        ]);
    }

    /** Создание категории */
    public function store(MarketCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages']
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
            DB::transaction(function () use (
                $request,
                $data,
                $translations,
                $imagesData
            ) {
                $this->ensureAllowedLevel($data['parent_id'] ?? null);

                $data['level'] = $this->resolveLevel($data['parent_id'] ?? null);

                if (!isset($data['sort']) || is_null($data['sort'])) {
                    $maxSort = MarketCategory::query()
                        ->where('parent_id', $data['parent_id'] ?? null)
                        ->max('sort');

                    $data['sort'] = is_null($maxSort) ? 0 : $maxSort + 1;
                }

                $category = MarketCategory::create($data);

                $this->syncTranslations($category, $translations);
                $this->syncImages($category, $request, $imagesData);
            });

            return redirect()
                ->route('admin.marketCategories.index')
                ->with('success', 'Категория успешно создана.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании market category: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Ошибка при создании категории.');
        }
    }

    /** Редирект просмотра на редактирование */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.marketCategories.edit', $id);
    }

    /** Страница редактирования категории */
    public function edit(int $marketCategory, Request $request): Response
    {
        $category = $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'parent.translations',
                'translations',
                'images',
            ])
            ->withCount([
                'children',
                'images',
            ])
            ->findOrFail($marketCategory);

        $currentLocale = $this->resolveLocale($request);

        $parents = $this->baseQuery()
            ->where('id', '!=', $category->id)
            ->with(['translations'])
            ->withCount(['children', 'images'])
            ->ordered()
            ->get();

        return Inertia::render('Admin/Market/MarketCategories/Edit', [
            'category' => new MarketCategoryResource($category),
            'parents' => MarketCategorySharedResource::collection($parents),

            'currentLocale' => $currentLocale,
            'availableLocales' => $this->availableLocales(),

            'imageProcessorEnabled' => $this->imageProcessorEnabled(),
            'imagePreset' => $this->imagePresetPayload(),
        ]);
    }

    /** Обновление категории */
    public function update(
        MarketCategoryRequest $request,
        int $marketCategory
    ): RedirectResponse {
        $category = $this->baseQuery()
            ->with('images')
            ->findOrFail($marketCategory);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
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
            DB::transaction(function () use (
                $request,
                $category,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds
            ) {
                if (!empty($data['parent_id']) && (int) $data['parent_id'] === (int) $category->id) {
                    throw new InvalidArgumentException('Категория не может быть родителем самой себя.');
                }

                $this->ensureParentIsNotDescendant(
                    categoryId: $category->id,
                    parentId: $data['parent_id'] ?? null
                );

                $this->ensureAllowedLevel($data['parent_id'] ?? null);

                $data['level'] = $this->resolveLevel($data['parent_id'] ?? null);

                $category->update($data);

                $this->syncTranslations($category, $translations);
                $this->syncImages($category, $request, $imagesData, $deletedImageIds);
            });

            return redirect()
                ->route('admin.marketCategories.index')
                ->with('success', 'Категория успешно обновлена.');
        } catch (Throwable $e) {
            Log::error('Ошибка при обновлении market category ID ' . $category->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Ошибка при обновлении категории.');
        }
    }

    /** Удаление категории */
    public function destroy(int $marketCategory): RedirectResponse
    {
        $category = $this->baseQuery()
            ->with(['images', 'translations'])
            ->findOrFail($marketCategory);

        try {
            DB::transaction(function () use ($category) {
                if ($category->children()->exists()) {
                    throw new InvalidArgumentException(
                        'Нельзя удалить категорию: сначала удалите или переместите дочерние категории.'
                    );
                }

                $imageIds = $category->images()
                    ->pluck('market_category_images.id')
                    ->toArray();

                if (!empty($imageIds)) {
                    $category->images()->detach();
                    $this->deleteImages($imageIds);
                }

                $category->translations()->delete();
                $category->delete();
            });

            return redirect()
                ->route('admin.marketCategories.index')
                ->with('success', 'Категория успешно удалена.');
        } catch (Throwable $e) {
            Log::error('Ошибка при удалении market category ID ' . $category->id . ': ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'Ошибка при удалении категории.');
        }
    }

    /** Массовое удаление категорий */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:market_categories,id'],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with('error', 'Часть категорий недоступна для удаления.');
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                $hasChildren = MarketCategory::query()
                    ->whereIn('parent_id', $allowedIds)
                    ->exists();

                if ($hasChildren) {
                    throw new InvalidArgumentException(
                        'Нельзя удалить выбранные категории, пока у них есть дочерние категории.'
                    );
                }

                $imageIds = MarketCategoryImage::query()
                    ->whereHas('categories', function (Builder $query) use ($allowedIds) {
                        $query->whereIn('market_categories.id', $allowedIds);
                    })
                    ->pluck('id')
                    ->toArray();

                DB::table('market_category_has_images')
                    ->whereIn('market_category_id', $allowedIds)
                    ->delete();

                if (!empty($imageIds)) {
                    $this->deleteImages($imageIds);
                }

                DB::table('market_category_translations')
                    ->whereIn('market_category_id', $allowedIds)
                    ->delete();

                MarketCategory::query()
                    ->whereIn('id', $allowedIds)
                    ->delete();
            });

            return back()->with('success', 'Выбранные категории успешно удалены.');
        } catch (Throwable $e) {
            Log::error('Ошибка bulkDestroy market categories: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->with('error', $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'Ошибка при массовом удалении категорий.');
        }
    }

    /** Массовое обновление сортировки дерева */
    public function updateSortBulk(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required_without:categories', 'array'],
            'items.*.id' => ['required_with:items', 'integer', 'exists:market_categories,id'],
            'items.*.sort' => ['required_with:items', 'integer', 'min:0'],
            'items.*.parent_id' => ['nullable', 'integer', 'exists:market_categories,id'],

            'categories' => ['required_without:items', 'array'],
            'categories.*.id' => ['required_with:categories', 'integer', 'exists:market_categories,id'],
            'categories.*.sort' => ['required_with:categories', 'integer', 'min:0'],
            'categories.*.parent_id' => ['nullable', 'integer', 'exists:market_categories,id'],
        ]);

        $items = $validated['items'] ?? $validated['categories'];
        $ids = array_column($items, 'id');

        $allowedIds = $this->baseQuery()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            $message = 'Часть категорий недоступна для изменения сортировки.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 400)
                : back()->with('error', $message);
        }

        try {
            DB::transaction(function () use ($items) {
                foreach ($items as $row) {
                    $categoryId = (int) $row['id'];
                    $parentId = $row['parent_id'] ?? null;

                    if (!empty($parentId) && (int) $parentId === $categoryId) {
                        throw new InvalidArgumentException(
                            'Категория не может быть родителем самой себя.'
                        );
                    }

                    $this->ensureParentIsNotDescendant(
                        categoryId: $categoryId,
                        parentId: $parentId
                    );

                    $this->ensureAllowedLevel($parentId);

                    MarketCategory::query()
                        ->whereKey($categoryId)
                        ->update([
                            'sort' => (int) $row['sort'],
                            'parent_id' => $parentId,
                            'level' => $this->resolveLevel($parentId),
                        ]);
                }
            });

            $message = 'Сортировка дерева категорий обновлена.';

            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('success', $message);
        } catch (Throwable $e) {
            Log::error('Ошибка updateSortBulk market categories: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            $message = $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'Ошибка при массовом обновлении сортировки категорий.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 500)
                : back()->with('error', $message);
        }
    }

    /** Переключение показа категории в главном меню */
    public function updateInMenu(
        Request $request,
        int $marketCategory
    ): RedirectResponse|JsonResponse {
        $validated = $request->validate([
            'in_menu' => ['required', 'boolean'],
        ]);

        $category = $this->baseQuery()->findOrFail($marketCategory);

        $category->update([
            'in_menu' => (bool) $validated['in_menu'],
        ]);

        $message = $validated['in_menu']
            ? 'Категория добавлена в главное меню.'
            : 'Категория скрыта из главного меню.';

        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }

    /** Базовый запрос для Index */
    private function indexQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'parent.translations',
                'translations',
                'images',
            ])
            ->withCount([
                'children',
                'images',
            ]);
    }

    /** Плоский список категорий */
    private function getIndexCategories(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query = $this->indexQuery();

        if ($useServerProcessing) {
            return $query
                ->search($search, $locale)
                ->sortByParam($sort, $locale)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->ordered()
            ->get();
    }

    /** Дерево категорий */
    private function getIndexCategoriesTree()
    {
        return $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'translations',
                'images',
                'childrenRecursive',
            ])
            ->withCount([
                'children',
                'images',
            ])
            ->root()
            ->ordered()
            ->get();
    }

    /** Определение уровня вложенности */
    private function resolveLevel(?int $parentId): int
    {
        if (!$parentId) {
            return 1;
        }

        $parent = $this->baseQuery()
            ->select('id', 'level')
            ->find($parentId);

        return $parent ? ((int) $parent->level) + 1 : 1;
    }

    /** Проверка максимальной глубины */
    private function ensureAllowedLevel(?int $parentId): void
    {
        if ($this->resolveLevel($parentId) > $this->maxCategoryLevel) {
            throw new InvalidArgumentException(
                'Нельзя создавать категорию глубже ' . $this->maxCategoryLevel . ' уровня вложенности.'
            );
        }
    }

    /** Запрет переноса категории внутрь своих потомков */
    private function ensureParentIsNotDescendant(
        int $categoryId,
        ?int $parentId
    ): void {
        if (!$parentId) {
            return;
        }

        $parent = MarketCategory::query()
            ->select(['id', 'parent_id'])
            ->find($parentId);

        while ($parent) {
            if ((int) $parent->id === $categoryId) {
                throw new InvalidArgumentException(
                    'Категорию нельзя переместить внутрь своей дочерней категории.'
                );
            }

            if (!$parent->parent_id) {
                break;
            }

            $parent = MarketCategory::query()
                ->select(['id', 'parent_id'])
                ->find($parent->parent_id);
        }
    }

    /** Подготовка childrenRecursive для Vue */
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
}
