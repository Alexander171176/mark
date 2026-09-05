<?php

namespace App\Http\Controllers\Admin\Market\MarketProductBundle;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketProductBundle\MarketProductBundleRequest;
use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Http\Resources\Admin\Market\MarketCompany\MarketCompanySharedResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductSharedResource;
use App\Http\Resources\Admin\Market\MarketProductBundle\MarketProductBundleResource;
use App\Http\Resources\Admin\Market\MarketProductBundle\MarketProductBundleSharedResource;
use App\Http\Resources\Admin\Market\MarketProductVariant\MarketProductVariantSharedResource;
use App\Http\Resources\Admin\Market\MarketShop\MarketShopSharedResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketProductBundle\MarketProductBundle;
use App\Models\Admin\Market\MarketProductBundle\MarketProductBundleImage;
use App\Models\Admin\Market\MarketProductBundle\MarketProductBundleItem;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariant;
use App\Models\Admin\Market\MarketShop\MarketShop;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер управления комплектами товаров маркетплейса.
 *
 * Поддерживает:
 * - frontend, auto и server режимы списка;
 * - поиск, сортировку и пагинацию;
 * - создание и редактирование комплектов;
 * - мультиязычные переводы;
 * - состав комплектов из товаров и вариантов;
 * - автоматическую и ручную цену;
 * - изображения через Spatie MediaLibrary;
 * - активность через HasMarketActivityTrait;
 * - сортировку через HasMarketSortingTrait;
 * - модерацию через HasMarketModerationTrait;
 * - рекламное размещение через HasMarketPlacementTrait;
 * - одиночное и массовое удаление.
 */
class MarketProductBundleController extends BaseMarketAdminController
{
    /** Основная модель. */
    protected string $modelClass = MarketProductBundle::class;

    /** Модель изображений. */
    protected string $imageModelClass = MarketProductBundleImage::class;

    /** Коллекция Spatie MediaLibrary. */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для системных сообщений. */
    protected string $entityLabel = 'комплектов товаров';

    /** Поля переводов. */
    protected array $translationFields = [
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Пресет изображений комплекта. */
    protected string $imagePresetKey = 'rectangle_large';

    /** Директория обработанных изображений. */
    protected string $imagePresetDirectory = 'market/market_product_bundle_images/preset';

    /**
     * Список комплектов товаров.
     */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);
        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminMarketProductBundlesPerPage', 6);

        $defaultSort = $settings->string(
            'adminMarketProductBundlesDefaultSort',
            'idDesc'
        );

        $processingMode = $settings->string(
            'adminMarketProductBundlesProcessingMode',
            'frontend'
        );

        $sortParam = (string) $request->query('sort', $defaultSort);
        $search = trim((string) $request->query('search', ''));

        $bundlesCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)->shouldUseServer(
            $processingMode,
            $bundlesCount,
            300
        );

        try {
            $bundles = $this->getIndexBundles(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render('Admin/Market/MarketProductBundles/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,

                'adminMarketProductBundlesPerPage' => $perPage,
                'adminMarketProductBundlesDefaultSort' => $defaultSort,
                'adminMarketProductBundlesProcessingMode' => $processingMode,

                'bundles' => MarketProductBundleSharedResource::collection($bundles),
                'bundlesCount' => $bundlesCount,

                'sortParam' => $sortParam,
                'search' => $search,
            ]);
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка комплектов товаров: ' . $e->getMessage(),
                [
                    'exception' => $e,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'user_id' => auth()->id(),
                    'bundles_count' => $bundlesCount,
                ]
            );

            return Inertia::render(
                'Admin/Market/MarketProductBundles/Index',
                [
                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'useServerProcessing' => $useServerProcessing,

                    'adminMarketProductBundlesPerPage' => $perPage,
                    'adminMarketProductBundlesDefaultSort' => $defaultSort,
                    'adminMarketProductBundlesProcessingMode' => $processingMode,

                    'bundles' => [],
                    'bundlesCount' => $bundlesCount,

                    'sortParam' => $sortParam,
                    'search' => $search,

                    'error' => app()->isLocal()
                        ? $e->getMessage()
                        : 'Ошибка загрузки комплектов товаров.',
                ]
            );
        }
    }

    /**
     * Страница создания комплекта товаров.
     */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render(
            'Admin/Market/MarketProductBundles/Create',
            array_merge(
                [
                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'imageProcessorEnabled' => $this->imageProcessorEnabled(),
                    'imagePreset' => $this->imagePresetPayload(),
                ],
                $this->sharedSelects($currentLocale)
            )
        );
    }

    /**
     * Создание комплекта товаров.
     */
    public function store(
        MarketProductBundleRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $items = $data['items'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset(
            $data['translations'],
            $data['items'],
            $data['images'],
            $data['deletedImages'],
            $data['_method']
        );

        $user = auth()->user();

        /**
         * Владелец комплекта всегда определяется
         * текущим авторизованным пользователем.
         */
        $data['user_id'] = $user?->id;

        /**
         * Обычный пользователь не может самостоятельно
         * назначать результат модерации.
         */
        if (
            $user
            && method_exists($user, 'hasRole')
            && ! $user->hasRole('admin')
        ) {
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
                $data,
                $translations,
                $items,
                $imagesData
            ): void {
                if (
                    ! array_key_exists('sort', $data)
                    || $data['sort'] === null
                ) {
                    $maxSort = MarketProductBundle::query()->max('sort');

                    $data['sort'] = $maxSort === null
                        ? 0
                        : (int) $maxSort + 1;
                }

                /**
                 * При автоматическом расчёте сохранённая цена
                 * используется только как резервное значение.
                 */
                if ((bool) ($data['calculate_price'] ?? true)) {
                    $data['price'] = $data['price'] ?? 0;
                }

                $bundle = MarketProductBundle::query()->create($data);

                $this->syncTranslations($bundle, $translations);
                $this->syncBundleItems($bundle, $items);

                $this->syncImages(
                    $bundle,
                    $request,
                    $imagesData
                );
            });

            return redirect()
                ->route('admin.marketProductBundles.index')
                ->with('success', 'Комплект товаров успешно создан.');
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании комплекта товаров: ' . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании комплекта товаров.');
        }
    }

    /**
     * Просмотр комплекта перенаправляется на редактирование.
     */
    public function show(int $marketProductBundle): RedirectResponse
    {
        $bundle = $this->baseQuery()->findOrFail($marketProductBundle);

        return redirect()->route('admin.marketProductBundles.edit', [
            'marketProductBundle' => $bundle->id,
        ]);
    }

    /**
     * Страница редактирования комплекта товаров.
     */
    public function edit(
        int $marketProductBundle,
        Request $request
    ): Response {
        $currentLocale = $this->resolveLocale($request);

        $bundle = $this->baseQuery()
            ->with($this->editRelations($currentLocale))
            ->withCount($this->bundleCountRelations())
            ->findOrFail($marketProductBundle);

        return Inertia::render(
            'Admin/Market/MarketProductBundles/Edit',
            array_merge(
                [
                    'bundle' => new MarketProductBundleResource($bundle),

                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'imageProcessorEnabled' => $this->imageProcessorEnabled(),
                    'imagePreset' => $this->imagePresetPayload(),
                ],
                $this->sharedSelects($currentLocale)
            )
        );
    }

    /**
     * Обновление комплекта товаров.
     */
    public function update(
        MarketProductBundleRequest $request,
        int $marketProductBundle
    ): RedirectResponse {
        $bundle = $this->baseQuery()
            ->findOrFail($marketProductBundle);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $items = $data['items'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset(
            $data['translations'],
            $data['items'],
            $data['images'],
            $data['deletedImages'],
            $data['_method']
        );

        $user = auth()->user();

        /**
         * Владелец существующего комплекта не изменяется.
         */
        unset($data['user_id']);

        if (
            $user
            && method_exists($user, 'hasRole')
            && ! $user->hasRole('admin')
        ) {
            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        }

        try {
            DB::transaction(function () use (
                $bundle,
                $request,
                $data,
                $translations,
                $items,
                $imagesData,
                $deletedImageIds
            ): void {
                if ((bool) ($data['calculate_price'] ?? true)) {
                    $data['price'] = $data['price'] ?? 0;
                }

                $bundle->update($data);

                $this->syncTranslations($bundle, $translations);
                $this->syncBundleItems($bundle, $items);

                $this->syncImages(
                    $bundle,
                    $request,
                    $imagesData,
                    $deletedImageIds
                );
            });

            return redirect()
                ->route('admin.marketProductBundles.index')
                ->with('success', 'Комплект товаров успешно обновлён.');
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления комплекта товаров ID '
                . $bundle->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении комплекта товаров.');
        }
    }

    /**
     * Удаление комплекта товаров.
     */
    public function destroy(
        int $marketProductBundle
    ): RedirectResponse {
        $bundle = $this->baseQuery()
            ->with([
                'images',
                'translations',
                'items',
            ])
            ->findOrFail($marketProductBundle);

        try {
            DB::transaction(function () use ($bundle): void {
                $this->deleteBundle($bundle);
            });

            return redirect()
                ->route('admin.marketProductBundles.index')
                ->with('success', 'Комплект товаров успешно удалён.');
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления комплекта товаров ID '
                . $bundle->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при удалении комплекта товаров.'
            );
        }
    }

    /**
     * Массовое удаление комплектов товаров.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],

            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:market_product_bundles,id',
            ],
        ]);

        $ids = collect($validated['ids'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        /**
         * baseQuery ограничивает обычного пользователя
         * только принадлежащими ему комплектами.
         */
        $allowedIds = $this->baseQuery()
            ->whereIn('market_product_bundles.id', $ids)
            ->pluck('market_product_bundles.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (count($allowedIds) !== count($ids)) {
            return back()->with(
                'error',
                'Часть комплектов товаров недоступна для удаления.'
            );
        }

        try {
            DB::transaction(function () use ($allowedIds): void {
                $bundles = MarketProductBundle::query()
                    ->whereIn('id', $allowedIds)
                    ->with([
                        'images',
                        'translations',
                        'items',
                    ])
                    ->get();

                foreach ($bundles as $bundle) {
                    $this->deleteBundle($bundle);
                }
            });

            return back()->with(
                'success',
                'Выбранные комплекты товаров успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового удаления комплектов товаров: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении комплектов товаров.'
            );
        }
    }

    /**
     * Переключение признака «Новинка».
     */
    public function updateIsNew(
        Request $request,
        int $marketProductBundle
    ): RedirectResponse {
        return $this->updateBooleanFlag(
            request: $request,
            bundleId: $marketProductBundle,
            field: 'is_new'
        );
    }

    /**
     * Переключение признака «Хит продаж».
     */
    public function updateIsHit(
        Request $request,
        int $marketProductBundle
    ): RedirectResponse {
        return $this->updateBooleanFlag(
            request: $request,
            bundleId: $marketProductBundle,
            field: 'is_hit'
        );
    }

    /**
     * Переключение признака «Распродажа».
     */
    public function updateIsSale(
        Request $request,
        int $marketProductBundle
    ): RedirectResponse {
        return $this->updateBooleanFlag(
            request: $request,
            bundleId: $marketProductBundle,
            field: 'is_sale'
        );
    }

    /**
     * Массовое обновление признака «Новинка».
     */
    public function bulkUpdateIsNew(
        Request $request
    ): RedirectResponse|JsonResponse {
        return $this->bulkUpdateBooleanFlag(
            request: $request,
            field: 'is_new'
        );
    }

    /**
     * Массовое обновление признака «Хит продаж».
     */
    public function bulkUpdateIsHit(
        Request $request
    ): RedirectResponse|JsonResponse {
        return $this->bulkUpdateBooleanFlag(
            request: $request,
            field: 'is_hit'
        );
    }

    /**
     * Массовое обновление признака «Распродажа».
     */
    public function bulkUpdateIsSale(
        Request $request
    ): RedirectResponse|JsonResponse {
        return $this->bulkUpdateBooleanFlag(
            request: $request,
            field: 'is_sale'
        );
    }

    /**
     * Общие справочники для страниц создания и редактирования.
     *
     * @return array<string, mixed>
     */
    private function sharedSelects(string $locale): array
    {
        $companiesQuery = MarketCompany::query();
        $shopsQuery = MarketShop::query();
        $productsQuery = MarketProduct::query();
        $variantsQuery = MarketProductVariant::query();

        $user = auth()->user();

        if (
            $user
            && method_exists($user, 'hasRole')
            && ! $user->hasRole('admin')
        ) {
            $companiesQuery->where('user_id', $user->id);
            $shopsQuery->where('user_id', $user->id);
            $productsQuery->where('user_id', $user->id);

            $variantsQuery->whereHas(
                'product',
                fn (Builder $query) => $query->where(
                    'user_id',
                    $user->id
                )
            );
        }

        $companies = $companiesQuery
            ->with([
                'translations' => fn ($query) =>
                $query->where('locale', $locale),
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $shops = $shopsQuery
            ->with([
                'translations' => fn ($query) =>
                $query->where('locale', $locale),
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $currencies = Currency::query()
            ->active()
            ->ordered()
            ->get();

        /**
         * Для формы достаточно:
         * - текущего перевода товара;
         * - валюты;
         * - количества вариантов.
         *
         * Полный список вариантов передаётся отдельным prop `variants`.
         */
        $products = $productsQuery
            ->with([
                'translations' => fn ($query) =>
                $query->where('locale', $locale),
                'currency',
            ])
            ->withCount('variants')
            ->whereHas(
                'translations',
                fn (Builder $query) => $query->where(
                    'locale',
                    $locale
                )
            )
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $variants = $variantsQuery
            ->with([
                'translations' => fn ($query) =>
                $query->where('locale', $locale),
                'currency',
                'product',
                'product.translations' => fn ($query) =>
                $query->where('locale', $locale),
                'product.currency',
            ])
            ->orderBy('market_product_id')
            ->orderByDesc('is_default')
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return [
            'companies' => MarketCompanySharedResource::collection($companies),
            'shops' => MarketShopSharedResource::collection($shops),
            'currencies' => CurrencyResource::collection($currencies),

            'products' => MarketProductSharedResource::collection($products),
            'variants' => MarketProductVariantSharedResource::collection($variants),
        ];
    }

    /**
     * Базовый запрос списка комплектов.
     */
    private function indexQuery(string $locale): Builder
    {
        return $this->baseQuery()
            ->with($this->indexRelations($locale))
            ->withCount($this->bundleCountRelations());
    }

    /**
     * Получение списка комплектов
     * по выбранному режиму обработки.
     */
    private function getIndexBundles(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ): LengthAwarePaginator|Collection {
        $query = $this->indexQuery($locale);

        if ($useServerProcessing) {
            return $query
                ->search($search, $locale)
                ->sortByParam($sort, $locale)
                ->paginate($perPage)
                ->withQueryString();
        }

        /**
         * Во frontend-режиме сервер возвращает все записи.
         * Поиск, фильтрация и сортировка выполняются в Index.vue.
         */
        return $query
            ->ordered()
            ->get();
    }

    /**
     * Отношения для Index.
     *
     * Только данные, реально необходимые SharedResource/Table/Card:
     * - current-locale переводы;
     * - owner;
     * - bundle images + media;
     * - company/shop;
     * - состав для расчётной цены, наличия и tooltip.
     *
     * @return array<int|string, mixed>
     */
    private function indexRelations(string $locale): array
    {
        return [
            'owner' => fn ($query) =>
            $query->select(
                'id',
                'name',
                'email',
                'profile_photo_path'
            ),

            'translations' => fn ($query) =>
            $query->where('locale', $locale),

            'images.media',
            'currency',

            'company.translations' => fn ($query) =>
            $query->where('locale', $locale),

            'shop.translations' => fn ($query) =>
            $query->where('locale', $locale),

            'items.product.translations' => fn ($query) =>
            $query->where('locale', $locale),

            'items.product.currency',

            /**
             * Нужны MarketProductBundleItem::availableQuantity()
             * при позиции без явно выбранного варианта.
             */
            'items.product.variants',

            'items.product.defaultVariant',

            'items.variant.translations' => fn ($query) =>
            $query->where('locale', $locale),

            'items.variant.currency',
            'items.variant.product.currency',
        ];
    }

    /**
     * Минимальный граф редактируемого комплекта.
     *
     * Собственные translations загружаются полностью для TranslationTabs.
     * Связанные справочники формы приходят из sharedSelects().
     *
     * @return array<int|string, mixed>
     */
    private function editRelations(string $locale): array
    {
        return [
            'translations',
            'images.media',
            'currency',

            'items.product.translations' => fn ($query) =>
            $query->where('locale', $locale),

            'items.product.currency',
            'items.product.variants',
            'items.product.defaultVariant',

            'items.variant.translations' => fn ($query) =>
            $query->where('locale', $locale),

            'items.variant.currency',
            'items.variant.product.currency',
        ];
    }

    /**
     * Счётчики связей комплекта.
     *
     * @return array<int|string, string|\Closure>
     */
    private function bundleCountRelations(): array
    {
        return [
            'items',

            'items as active_items_count' => function (
                Builder $query
            ): void {
                $query->where('market_product_bundle_items.activity', true);
            },

            'images',
        ];
    }

    /**
     * Синхронизация состава комплекта.
     *
     * Поддерживает:
     * - создание новых позиций;
     * - обновление существующих;
     * - удаление убранных из формы позиций;
     * - сохранение порядка.
     */
    private function syncBundleItems(
        MarketProductBundle $bundle,
        array $items
    ): void {
        $existingIds = $bundle->items()
            ->pluck('market_product_bundle_items.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $savedIds = [];

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $productId = $item['market_product_id'] ?? null;

            if (! $productId) {
                continue;
            }

            $payload = [
                'market_product_id' => (int) $productId,

                'market_product_variant_id' =>
                    ! empty($item['market_product_variant_id'])
                        ? (int) $item['market_product_variant_id']
                        : null,

                'quantity' => max(
                    1,
                    (int) ($item['quantity'] ?? 1)
                ),

                'unit_price' => $item['unit_price'] ?? null,
                'discount_type' => $item['discount_type'] ?? null,
                'discount_value' => $item['discount_value'] ?? null,

                'sort' => (int) ($item['sort'] ?? $index),
                'activity' => (bool) ($item['activity'] ?? true),
            ];

            $itemId = ! empty($item['id'])
                ? (int) $item['id']
                : null;

            if ($itemId !== null) {
                $bundleItem = $bundle->items()
                    ->whereKey($itemId)
                    ->first();

                if ($bundleItem) {
                    $bundleItem->update($payload);
                    $savedIds[] = (int) $bundleItem->id;

                    continue;
                }
            }

            $bundleItem = $bundle->items()->create($payload);
            $savedIds[] = (int) $bundleItem->id;
        }

        $deleteIds = array_diff($existingIds, $savedIds);

        if ($deleteIds !== []) {
            MarketProductBundleItem::query()
                ->where('market_product_bundle_id', $bundle->id)
                ->whereIn('id', $deleteIds)
                ->delete();
        }
    }

    /**
     * Полное удаление комплекта товаров.
     */
    private function deleteBundle(
        MarketProductBundle $bundle
    ): void {
        $imageIds = $bundle->images()
            ->pluck('market_product_bundle_images.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        /**
         * Сначала удаляем pivot-связи изображений.
         */
        $bundle->images()->detach();

        /**
         * Удаляем записи изображений и файлы MediaLibrary.
         */
        if ($imageIds !== []) {
            $this->deleteImages($imageIds);
        }

        $bundle->items()->delete();
        $bundle->translations()->delete();
        $bundle->delete();
    }

    /**
     * Обновление одиночного boolean-признака.
     */
    private function updateBooleanFlag(
        Request $request,
        int $bundleId,
        string $field
    ): RedirectResponse {
        $this->validateBooleanField($field);

        $validated = $request->validate([
            $field => [
                'required',
                'boolean',
            ],
        ]);

        $bundle = $this->baseQuery()->findOrFail($bundleId);

        $bundle->update([
            $field => $validated[$field],
        ]);

        return back()->with(
            'success',
            "Поле {$field} комплекта товаров обновлено."
        );
    }

    /**
     * Массовое обновление boolean-признака.
     */
    private function bulkUpdateBooleanFlag(
        Request $request,
        string $field
    ): RedirectResponse|JsonResponse {
        $this->validateBooleanField($field);

        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],

            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:market_product_bundles,id',
            ],

            $field => [
                'required',
                'boolean',
            ],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('market_product_bundles.id', $validated['ids'])
            ->pluck('market_product_bundles.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (count($allowedIds) !== count($validated['ids'])) {
            $message = 'Часть комплектов товаров недоступна для обновления.';

            return $request->expectsJson()
                ? response()->json([
                    'success' => false,
                    'message' => $message,
                ], 403)
                : back()->with('error', $message);
        }

        MarketProductBundle::query()
            ->whereIn('id', $allowedIds)
            ->update([
                $field => $validated[$field],
            ]);

        $message = "Поле {$field} выбранных комплектов товаров обновлено.";

        return $request->expectsJson()
            ? response()->json([
                'success' => true,
                'message' => $message,
            ])
            : back()->with('success', $message);
    }

    /**
     * Проверка разрешённого boolean-поля.
     */
    private function validateBooleanField(string $field): void
    {
        abort_unless(
            in_array(
                $field,
                [
                    'is_new',
                    'is_hit',
                    'is_sale',
                ],
                true
            ),
            422,
            'Недопустимое поле комплекта товаров.'
        );
    }
}
