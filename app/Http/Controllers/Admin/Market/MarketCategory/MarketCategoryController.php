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

        $perPage = $settings->int(
            'adminMarketCategoriesPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminMarketCategoriesDefaultSort',
            'idDesc'
        );

        $sortParam = (string) $request->query(
            'sort',
            $defaultSort
        );

        $search = trim(
            (string) $request->query('search', '')
        );

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
            /**
             * Дерево загружается независимо от режима обработки
             * плоского списка, потому что является отдельным
             * представлением Index.
             */
            $categoriesTree = $this->getIndexCategoriesTree(
                $currentLocale
            );

            /**
             * Vue использует relation children,
             * поэтому рекурсивный childrenRecursive
             * преобразуем в children.
             */
            $this->prepareTreeChildren($categoriesTree);

            $categoriesFlat = $this->getIndexCategories(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render(
                'Admin/Market/MarketCategories/Index',
                [
                    /**
                     * Index использует компактный контракт.
                     * Полный Resource здесь больше не нужен.
                     */
                    'categoriesTree' => MarketCategorySharedResource::collection(
                        $categoriesTree
                    ),

                    'categories' => MarketCategorySharedResource::collection(
                        $categoriesFlat
                    ),

                    'categoriesCount' => $categoriesCount,

                    'useServerProcessing' => $useServerProcessing,

                    'adminMarketCategoriesPerPage' => $perPage,
                    'adminMarketCategoriesDefaultSort' => $defaultSort,
                    'adminMarketCategoriesProcessingMode' => $processingMode,

                    'sortParam' => $sortParam,
                    'search' => $search,

                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка market categories: ' . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/Market/MarketCategories/Index',
                [
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
                ]
            );
        }
    }

    /** Страница создания категории */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render(
            'Admin/Market/MarketCategories/Create',
            [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                /**
                 * Для select родителей нужны только:
                 * id, parent_id, level, sort и translation.
                 *
                 * children_count/images_count здесь не нужны.
                 */
                'parents' => MarketCategorySharedResource::collection(
                    $this->parentsForSelect($currentLocale)
                ),

                'imageProcessorEnabled' => $this->imageProcessorEnabled(),
                'imagePreset' => $this->imagePresetPayload(),
            ]
        );
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

        if (
            $user
            && method_exists($user, 'hasRole')
            && !$user->hasRole('admin')
        ) {
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
                $parentId = $data['parent_id'] ?? null;

                $this->ensureAllowedLevel($parentId);

                $data['level'] = $this->resolveLevel($parentId);

                if (
                    !isset($data['sort'])
                    || is_null($data['sort'])
                ) {
                    $maxSort = MarketCategory::query()
                        ->where(
                            'market_categories.parent_id',
                            $parentId
                        )
                        ->max('market_categories.sort');

                    $data['sort'] = is_null($maxSort)
                        ? 0
                        : $maxSort + 1;
                }

                $category = MarketCategory::create($data);

                $this->syncTranslations(
                    $category,
                    $translations
                );

                $this->syncImages(
                    $category,
                    $request,
                    $imagesData
                );
            });

            return redirect()
                ->route('admin.marketCategories.index')
                ->with(
                    'success',
                    'Категория успешно создана.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании market category: ' . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e instanceof InvalidArgumentException
                        ? $e->getMessage()
                        : 'Ошибка при создании категории.'
                );
        }
    }

    /** Редирект просмотра на редактирование */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route(
            'admin.marketCategories.edit',
            $id
        );
    }

    /** Страница редактирования категории */
    public function edit(
        int $marketCategory,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale($request);

        /**
         * Edit использует:
         * - собственные scalar-поля;
         * - все собственные translations;
         * - images.
         *
         * owner/moderator/parent/counts форме не нужны.
         */
        $category = $this->baseQuery()
            ->with([
                'translations',
                'images.media',
            ])
            ->findOrFail($marketCategory);

        return Inertia::render(
            'Admin/Market/MarketCategories/Edit',
            [
                'category' => new MarketCategoryResource(
                    $category
                ),

                'parents' => MarketCategorySharedResource::collection(
                    $this->parentsForSelect(
                        locale: $currentLocale,
                        excludeCategoryId: $category->id
                    )
                ),

                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'imageProcessorEnabled' => $this->imageProcessorEnabled(),
                'imagePreset' => $this->imagePresetPayload(),
            ]
        );
    }

    /** Обновление категории */
    public function update(
        MarketCategoryRequest $request,
        int $marketCategory
    ): RedirectResponse {
        /**
         * Сохраняем загрузку images для совместимости
         * с текущей логикой syncImages().
         */
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

        if (
            $user
            && method_exists($user, 'hasRole')
            && !$user->hasRole('admin')
        ) {
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
                $parentId = $data['parent_id'] ?? null;

                if (
                    !empty($parentId)
                    && (int) $parentId === (int) $category->id
                ) {
                    throw new InvalidArgumentException(
                        'Категория не может быть родителем самой себя.'
                    );
                }

                $this->ensureParentIsNotDescendant(
                    categoryId: $category->id,
                    parentId: $parentId
                );

                $this->ensureAllowedLevel($parentId);

                $data['level'] = $this->resolveLevel(
                    $parentId
                );

                $category->update($data);

                $this->syncTranslations(
                    $category,
                    $translations
                );

                $this->syncImages(
                    $category,
                    $request,
                    $imagesData,
                    $deletedImageIds
                );
            });

            return redirect()
                ->route('admin.marketCategories.index')
                ->with(
                    'success',
                    'Категория успешно обновлена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении market category ID '
                . $category->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e instanceof InvalidArgumentException
                        ? $e->getMessage()
                        : 'Ошибка при обновлении категории.'
                );
        }
    }

    /** Удаление категории */
    public function destroy(
        int $marketCategory
    ): RedirectResponse {
        /**
         * relations заранее не загружаем:
         * ниже используются relation-query.
         */
        $category = $this->baseQuery()
            ->findOrFail($marketCategory);

        try {
            DB::transaction(function () use ($category) {
                if ($category->children()->exists()) {
                    throw new InvalidArgumentException(
                        'Нельзя удалить категорию: '
                        . 'сначала удалите или переместите дочерние категории.'
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
                ->with(
                    'success',
                    'Категория успешно удалена.'
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении market category ID '
                . $category->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Ошибка при удалении категории.'
            );
        }
    }

    /** Массовое удаление категорий */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => [
                'required',
                'integer',
                'exists:market_categories,id',
            ],
        ]);

        $ids = $validated['ids'];

        $allowedIds = $this->baseQuery()
            ->whereIn(
                'market_categories.id',
                $ids
            )
            ->pluck('market_categories.id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with(
                'error',
                'Часть категорий недоступна для удаления.'
            );
        }

        try {
            DB::transaction(function () use ($allowedIds) {
                $hasChildren = MarketCategory::query()
                    ->whereIn(
                        'market_categories.parent_id',
                        $allowedIds
                    )
                    ->exists();

                if ($hasChildren) {
                    throw new InvalidArgumentException(
                        'Нельзя удалить выбранные категории, '
                        . 'пока у них есть дочерние категории.'
                    );
                }

                $imageIds = MarketCategoryImage::query()
                    ->whereHas(
                        'categories',
                        function (Builder $query) use ($allowedIds) {
                            $query->whereIn(
                                'market_categories.id',
                                $allowedIds
                            );
                        }
                    )
                    ->pluck('market_category_images.id')
                    ->toArray();

                DB::table('market_category_has_images')
                    ->whereIn(
                        'market_category_id',
                        $allowedIds
                    )
                    ->delete();

                if (!empty($imageIds)) {
                    $this->deleteImages($imageIds);
                }

                DB::table('market_category_translations')
                    ->whereIn(
                        'market_category_id',
                        $allowedIds
                    )
                    ->delete();

                MarketCategory::query()
                    ->whereIn(
                        'market_categories.id',
                        $allowedIds
                    )
                    ->delete();
            });

            return back()->with(
                'success',
                'Выбранные категории успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkDestroy market categories: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                $e instanceof InvalidArgumentException
                    ? $e->getMessage()
                    : 'Ошибка при массовом удалении категорий.'
            );
        }
    }

    /** Массовое обновление сортировки дерева */
    public function updateSortBulk(
        Request $request
    ): RedirectResponse|JsonResponse {
        $validated = $request->validate([
            'items' => [
                'required_without:categories',
                'array',
                'min:1',
            ],

            'items.*.id' => [
                'required_with:items',
                'integer',
                'min:1',
            ],

            'items.*.sort' => [
                'required_with:items',
                'integer',
                'min:0',
            ],

            'items.*.parent_id' => [
                'nullable',
                'integer',
                'min:1',
            ],

            /*
             * Сохраняем совместимость со старым
             * названием payload categories.
             */
            'categories' => [
                'required_without:items',
                'array',
                'min:1',
            ],

            'categories.*.id' => [
                'required_with:categories',
                'integer',
                'min:1',
            ],

            'categories.*.sort' => [
                'required_with:categories',
                'integer',
                'min:0',
            ],

            'categories.*.parent_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        $items = $validated['items']
            ?? $validated['categories'];

        try {
            /*
             * Frontend отправляет полный snapshot дерева.
             * Одним запросом получаем все доступные категории.
             */
            $allowedIds = $this->baseQuery()
                ->pluck('market_categories.id')
                ->map(fn ($id) => (int) $id)
                ->all();

            /*
             * Проверяем целостность полного snapshot:
             * доступность ID, дубликаты, parent_id и полноту дерева.
             */
            $tree = $this->validateTreeStructure(
                $items,
                $allowedIds
            );

            /*
             * Уровни рассчитываются только из нового дерева,
             * поэтому старые parent_id/level в БД здесь
             * не влияют на результат.
             */
            $levels = $this->calculateTreeLevels($tree);

            $ids = [];
            $sortCases = [];
            $sortBindings = [];
            $parentCases = [];
            $parentBindings = [];
            $levelCases = [];
            $levelBindings = [];

            foreach ($tree as $categoryId => $node) {
                $categoryId = (int) $categoryId;

                $ids[] = $categoryId;

                $sortCases[] = 'WHEN ? THEN ?';
                $sortBindings[] = $categoryId;
                $sortBindings[] = (int) $node['sort'];

                $parentCases[] = 'WHEN ? THEN ?';
                $parentBindings[] = $categoryId;
                $parentBindings[] = $node['parent_id'];

                $levelCases[] = 'WHEN ? THEN ?';
                $levelBindings[] = $categoryId;
                $levelBindings[] = (int) $levels[$categoryId];
            }

            $placeholders = implode(
                ',',
                array_fill(0, count($ids), '?')
            );

            $sql = '
                UPDATE market_categories
                SET
                    sort = CASE id '
                . implode(' ', $sortCases)
                . ' END,
                    parent_id = CASE id '
                . implode(' ', $parentCases)
                . ' END,
                    level = CASE id '
                . implode(' ', $levelCases)
                . ' END,
                    updated_at = ?
                WHERE id IN (' . $placeholders . ')
            ';

            $bindings = [
                ...$sortBindings,
                ...$parentBindings,
                ...$levelBindings,
                now(),
                ...$ids,
            ];

            DB::transaction(function () use (
                $sql,
                $bindings
            ): void {
                DB::update(
                    $sql,
                    $bindings
                );
            });

            $message = 'Сортировка дерева категорий обновлена.';

            return $request->expectsJson()
                ? response()->json([
                    'message' => $message,
                ])
                : back()->with(
                    'success',
                    $message
                );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка updateSortBulk market categories: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            $message = $e instanceof InvalidArgumentException
                ? $e->getMessage()
                : 'Ошибка при массовом обновлении '
                . 'сортировки категорий.';

            if ($request->expectsJson()) {
                return response()->json(
                    ['message' => $message],
                    $e instanceof InvalidArgumentException
                        ? 422
                        : 500
                );
            }

            return back()->withErrors([
                'general' => $message,
            ]);
        }
    }

    /** Переключение показа категории в главном меню */
    public function updateInMenu(
        Request $request,
        int $marketCategory
    ): RedirectResponse|JsonResponse {
        $validated = $request->validate([
            'in_menu' => [
                'required',
                'boolean',
            ],
        ]);

        $category = $this->baseQuery()
            ->findOrFail($marketCategory);

        $category->update([
            'in_menu' => (bool) $validated['in_menu'],
        ]);

        $message = $validated['in_menu']
            ? 'Категория добавлена в главное меню.'
            : 'Категория скрыта из главного меню.';

        return $request->expectsJson()
            ? response()->json([
                'message' => $message,
            ])
            : back()->with(
                'success',
                $message
            );
    }

    /**
     * Базовый запрос плоского Index.
     *
     * Все переводимые relations загружаются
     * только для currentLocale.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        return $this->baseQuery()
            ->with([
                'translations' => fn ($query) => $query
                    ->where('locale', $locale),

                'parent',

                'parent.translations' => fn ($query) => $query
                    ->where('locale', $locale),

                'owner:id,name,email,profile_photo_path',

                'images.media',
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
        $query = $this->indexQuery(
            $locale
        );

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
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->ordered()
            ->get();
    }

    /**
     * Дерево категорий.
     *
     * Загружаются максимум три уровня.
     * Для каждого уровня:
     * - один перевод currentLocale;
     * - owner;
     * - images.media;
     * - children_count;
     * - images_count.
     */
    private function getIndexCategoriesTree(
        string $locale
    ) {
        return $this->baseQuery()
            ->with(
                $this->treeRelations(
                    locale: $locale,
                    depth: $this->maxCategoryLevel - 1
                )
            )
            ->withCount([
                'children',
                'images',
            ])
            ->root()
            ->ordered()
            ->get();
    }

    /**
     * Relations для рекурсивного дерева.
     *
     * depth:
     * 2 = корень → уровень 2 → уровень 3.
     */
    private function treeRelations(
        string $locale,
        int $depth
    ): array {
        $relations = [
            'translations' => fn ($query) => $query
                ->where('locale', $locale),

            'owner:id,name,email,profile_photo_path',

            'images.media',
        ];

        if ($depth > 0) {
            $relations['childrenRecursive'] = function ($query) use ($locale, $depth) {
                $query
                    ->with(
                        $this->treeRelations(
                            locale: $locale,
                            depth: $depth - 1
                        )
                    )
                    ->withCount([
                        'children',
                        'images',
                    ]);
            };
        }

        return $relations;
    }

    /**
     * Категории для выбора родителя.
     *
     * Здесь не нужны:
     * owner, moderator, images и counts.
     */
    private function parentsForSelect(
        string $locale,
        ?int $excludeCategoryId = null
    ) {
        $query = $this->baseQuery()
            ->with([
                'translations' => fn ($query) => $query
                    ->where('locale', $locale),
            ]);

        if ($excludeCategoryId !== null) {
            $query->where(
                'market_categories.id',
                '!=',
                $excludeCategoryId
            );
        }

        return $query
            ->ordered()
            ->get();
    }

    /**
     * Проверка полного snapshot дерева категорий.
     *
     * Возвращает нормализованную карту:
     * categoryId => [
     *     id,
     *     sort,
     *     parent_id
     * ]
     */
    private function validateTreeStructure(
        array $items,
        array $allowedIds
    ): array {
        $allowedMap = array_fill_keys(
            $allowedIds,
            true
        );

        $tree = [];

        foreach ($items as $row) {
            $categoryId = (int) $row['id'];

            $parentId = isset($row['parent_id'])
                ? (int) $row['parent_id']
                : null;

            if (isset($tree[$categoryId])) {
                throw new InvalidArgumentException(
                    'В дереве обнаружена дублирующаяся '
                    . 'категория ID ' . $categoryId . '.'
                );
            }

            if (!isset($allowedMap[$categoryId])) {
                throw new InvalidArgumentException(
                    'Категория ID ' . $categoryId
                    . ' недоступна для изменения.'
                );
            }

            if (
                $parentId !== null
                && $parentId === $categoryId
            ) {
                throw new InvalidArgumentException(
                    'Категория не может быть '
                    . 'родителем самой себя.'
                );
            }

            $tree[$categoryId] = [
                'id' => $categoryId,
                'sort' => (int) $row['sort'],
                'parent_id' => $parentId,
            ];
        }

        /*
         * Drag&drop работает именно с полным snapshot.
         * Поэтому потеря даже одной доступной категории
         * означает некорректное состояние дерева.
         */
        $missingIds = array_diff(
            $allowedIds,
            array_keys($tree)
        );

        if (!empty($missingIds)) {
            throw new InvalidArgumentException(
                'Передано неполное состояние дерева категорий.'
            );
        }

        /*
         * Каждый parent_id обязан существовать
         * внутри того же snapshot.
         */
        foreach ($tree as $node) {
            $parentId = $node['parent_id'];

            if (
                $parentId !== null
                && !isset($tree[$parentId])
            ) {
                throw new InvalidArgumentException(
                    'Родительская категория ID '
                    . $parentId
                    . ' отсутствует в дереве.'
                );
            }
        }

        return $tree;
    }

    /**
     * Расчёт уровней категорий по новому snapshot дерева.
     *
     * Одновременно проверяет:
     * - отсутствие циклов;
     * - максимальную глубину дерева.
     */
    private function calculateTreeLevels(
        array $tree
    ): array {
        $levels = [];
        $visiting = [];

        $resolve = function (
            int $categoryId
        ) use (
            &$resolve,
            &$levels,
            &$visiting,
            $tree
        ): int {
            if (isset($levels[$categoryId])) {
                return $levels[$categoryId];
            }

            if (isset($visiting[$categoryId])) {
                throw new InvalidArgumentException(
                    'Обнаружен цикл в иерархии категорий.'
                );
            }

            $visiting[$categoryId] = true;

            $parentId = $tree[$categoryId]['parent_id'];

            $level = $parentId === null
                ? 1
                : $resolve($parentId) + 1;

            unset($visiting[$categoryId]);

            if ($level > $this->maxCategoryLevel) {
                throw new InvalidArgumentException(
                    'Нельзя переместить категорию ID '
                    . $categoryId
                    . ': максимальная глубина дерева — '
                    . $this->maxCategoryLevel
                    . ' уровня.'
                );
            }

            $levels[$categoryId] = $level;

            return $level;
        };

        foreach (array_keys($tree) as $categoryId) {
            $resolve((int) $categoryId);
        }

        return $levels;
    }

    /** Определение уровня вложенности */
    private function resolveLevel(
        ?int $parentId
    ): int {
        if (!$parentId) {
            return 1;
        }

        $parent = $this->baseQuery()
            ->select([
                'market_categories.id',
                'market_categories.level',
            ])
            ->find($parentId);

        return $parent
            ? ((int) $parent->level) + 1
            : 1;
    }

    /** Проверка максимальной глубины */
    private function ensureAllowedLevel(
        ?int $parentId
    ): void {
        if (
            $this->resolveLevel($parentId)
            > $this->maxCategoryLevel
        ) {
            throw new InvalidArgumentException(
                'Нельзя создавать категорию глубже '
                . $this->maxCategoryLevel
                . ' уровня вложенности.'
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
            ->select([
                'market_categories.id',
                'market_categories.parent_id',
            ])
            ->find($parentId);

        while ($parent) {
            if (
                (int) $parent->id
                === $categoryId
            ) {
                throw new InvalidArgumentException(
                    'Категорию нельзя переместить '
                    . 'внутрь своей дочерней категории.'
                );
            }

            if (!$parent->parent_id) {
                break;
            }

            $parent = MarketCategory::query()
                ->select([
                    'market_categories.id',
                    'market_categories.parent_id',
                ])
                ->find($parent->parent_id);
        }
    }

    /**
     * Подготовка childrenRecursive для Vue.
     *
     * SharedResource ожидает relation children,
     * поэтому заменяем её уже загруженным
     * childrenRecursive без дополнительных SQL.
     */
    private function prepareTreeChildren(
        $nodes
    ): void {
        $nodes->each(function ($node) {
            if (
                $node->relationLoaded(
                    'childrenRecursive'
                )
            ) {
                $node->setRelation(
                    'children',
                    $node->childrenRecursive
                );

                $this->prepareTreeChildren(
                    $node->childrenRecursive
                );

                return;
            }

            if ($node->relationLoaded('children')) {
                $this->prepareTreeChildren(
                    $node->children
                );
            }
        });
    }
}
