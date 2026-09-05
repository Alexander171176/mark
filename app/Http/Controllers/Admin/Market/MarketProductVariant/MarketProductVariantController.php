<?php

namespace App\Http\Controllers\Admin\Market\MarketProductVariant;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketProductVariant\MarketProductVariantRequest;
use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Http\Resources\Admin\Market\MarketAttribute\MarketAttributeResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductSharedResource;
use App\Http\Resources\Admin\Market\MarketProductVariant\MarketProductVariantResource;
use App\Http\Resources\Admin\Market\MarketProductVariant\MarketProductVariantSharedResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Market\MarketAttribute\MarketAttribute;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariant;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariantImage;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariantValue;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\AdminSettingsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Контроллер управления вариантами товаров.
 *
 * Поддерживает:
 * - frontend, auto и server режимы списка;
 * - поиск, сортировку и пагинацию;
 * - создание и редактирование вариантов;
 * - мультиязычные переводы;
 * - значения характеристик;
 * - изображения через Spatie MediaLibrary;
 * - основной вариант товара;
 * - активность через HasMarketActivityTrait;
 * - сортировку через HasMarketSortingTrait;
 * - модерацию через HasMarketModerationTrait;
 * - одиночное и массовое удаление.
 */
class MarketProductVariantController extends BaseMarketAdminController
{
    /** Основная модель. */
    protected string $modelClass = MarketProductVariant::class;

    /** Модель изображений. */
    protected string $imageModelClass = MarketProductVariantImage::class;

    /** Коллекция Spatie MediaLibrary. */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для системных сообщений. */
    protected string $entityLabel = 'вариантов товаров';

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

    /** Пресет изображений варианта. */
    protected string $imagePresetKey = 'rectangle_large';

    /** Директория обработанных изображений. */
    protected string $imagePresetDirectory = 'market/market_product_variant_images/preset';

    /** Список вариантов товаров. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);
        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int('adminMarketProductVariantsPerPage', 10);
        $defaultSort = $settings->string('adminMarketProductVariantsDefaultSort', 'idDesc');
        $processingMode = $settings->string('adminMarketProductVariantsProcessingMode', 'frontend');

        $sortParam = (string)$request->query('sort', $defaultSort);
        $search = trim((string)$request->query('search', ''));
        $productId = $request->integer('market_product_id') ?: null;

        if ($productId !== null) {
            $this->productQuery()->findOrFail($productId);
        }

        $variantsCount = $this->baseQuery()
            ->when(
                $productId !== null,
                fn(Builder $query) => $query->where(
                    'market_product_variants.market_product_id',
                    $productId
                )
            )
            ->count();

        $useServerProcessing = app(ProcessingModeService::class)->shouldUseServer(
            $processingMode,
            $variantsCount,
            300
        );

        try {
            $variants = $this->getIndexVariants(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search,
                productId: $productId
            );

            return Inertia::render('Admin/Market/MarketProductVariants/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,
                'adminMarketProductVariantsPerPage' => $perPage,
                'adminMarketProductVariantsDefaultSort' => $defaultSort,
                'adminMarketProductVariantsProcessingMode' => $processingMode,

                'variants' => MarketProductVariantSharedResource::collection($variants),
                'variantsCount' => $variantsCount,

                'sortParam' => $sortParam,
                'search' => $search,

                'filters' => [
                    'market_product_id' => $productId,
                ],

                'products' => $this->productsForFilter($currentLocale),
                'currentProductId' => $productId,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка загрузки вариантов товаров: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Inertia::render('Admin/Market/MarketProductVariants/Index', [
                'currentLocale' => $currentLocale,
                'availableLocales' => $this->availableLocales(),

                'useServerProcessing' => $useServerProcessing,
                'adminMarketProductVariantsPerPage' => $perPage,
                'adminMarketProductVariantsDefaultSort' => $defaultSort,
                'adminMarketProductVariantsProcessingMode' => $processingMode,

                'variants' => [],
                'variantsCount' => 0,

                'sortParam' => $sortParam,
                'search' => $search,

                'filters' => [
                    'market_product_id' => $productId,
                ],

                'products' => [],
                'currentProductId' => $productId,

                'error' => 'Ошибка загрузки вариантов товаров.',
            ]);
        }
    }

    /** Страница создания варианта. */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);
        $selectedProductId = $request->integer('market_product_id') ?: null;

        if ($selectedProductId !== null) {
            $this->productQuery()->findOrFail($selectedProductId);
        }

        return Inertia::render(
            'Admin/Market/MarketProductVariants/Create',
            array_merge(
                [
                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),
                    'selectedProductId' => $selectedProductId,

                    'imageProcessorEnabled' => $this->imageProcessorEnabled(),
                    'imagePreset' => $this->imagePresetPayload(),
                ],
                $this->sharedSelects($selectedProductId)
            )
        );
    }

    /** Создание варианта товара. */
    public function store(MarketProductVariantRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->productQuery()->findOrFail((int)$data['market_product_id']);

        $translations = $data['translations'] ?? [];
        $values = $data['values'] ?? [];
        $imagesData = $data['images'] ?? [];

        unset(
            $data['translations'],
            $data['values'],
            $data['images'],
            $data['deletedImages'],
            $data['_method']
        );

        try {
            DB::transaction(function () use (
                $request,
                $data,
                $translations,
                $values,
                $imagesData
            ): void {
                $productId = (int)$data['market_product_id'];

                if (!array_key_exists('sort', $data) || $data['sort'] === null) {
                    $maxSort = MarketProductVariant::query()
                        ->where('market_product_id', $productId)
                        ->max('sort');

                    $data['sort'] = $maxSort === null ? 0 : (int)$maxSort + 1;
                }

                $hasVariants = MarketProductVariant::query()
                    ->where('market_product_id', $productId)
                    ->exists();

                if (!$hasVariants) {
                    $data['is_default'] = true;
                }

                if (!empty($data['is_default'])) {
                    $this->clearDefaultVariant($productId);
                }

                $variant = MarketProductVariant::query()->create($data);

                $this->syncTranslations($variant, $translations);
                $this->syncVariantValues($variant, $values);
                $this->syncImages($variant, $request, $imagesData);

                $this->ensureDefaultVariant($productId);
            });

            return redirect()
                ->route('admin.marketProductVariants.index')
                ->with('success', 'Вариант товара успешно создан.');
        } catch (Throwable $e) {
            Log::error('Ошибка при создании варианта товара: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании варианта товара.');
        }
    }

    /** Просмотр варианта перенаправляется на редактирование. */
    public function show(int $marketProductVariant): RedirectResponse
    {
        $variant = $this->baseQuery()->findOrFail($marketProductVariant);

        return redirect()->route('admin.marketProductVariants.edit', [
            'marketProductVariant' => $variant->id,
        ]);
    }

    /** Страница редактирования варианта. */
    public function edit(
        int     $marketProductVariant,
        Request $request
    ): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $variant = $this->baseQuery()
            ->with([
                /** Все собственные переводы нужны TranslationTabs. */
                'translations',

                /** Изображения варианта используют Spatie MediaLibrary. */
                'images.media',

                /** Собственная валюта варианта. */
                'currency',

                /**
                 * Родительский товар нужен для эффективных наследуемых
                 * значений полного Resource. Его перевод — только current locale.
                 */
                'product.translations' => fn($query) =>
                $query->where('locale', $currentLocale),
                'product.currency',

                /**
                 * Для инициализации формы достаточно ID строк значений.
                 * Подписи характеристик приходят из sharedSelects().
                 */
                'values',
            ])
            ->withCount([
                'images',
                'values',
            ])
            ->findOrFail($marketProductVariant);

        return Inertia::render(
            'Admin/Market/MarketProductVariants/Edit',
            array_merge(
                [
                    'variant' => new MarketProductVariantResource($variant),

                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'imageProcessorEnabled' => $this->imageProcessorEnabled(),
                    'imagePreset' => $this->imagePresetPayload(),
                ],
                $this->sharedSelects(
                    (int)$variant->market_product_id
                )
            )
        );
    }

    /** Обновление варианта товара. */
    public function update(
        MarketProductVariantRequest $request,
        int                         $marketProductVariant
    ): RedirectResponse
    {
        $variant = $this->baseQuery()->findOrFail($marketProductVariant);
        $data = $request->validated();

        $this->productQuery()->findOrFail((int)$data['market_product_id']);

        $translations = $data['translations'] ?? [];
        $values = $data['values'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        unset(
            $data['translations'],
            $data['values'],
            $data['images'],
            $data['deletedImages'],
            $data['_method']
        );

        try {
            DB::transaction(function () use (
                $variant,
                $request,
                $data,
                $translations,
                $values,
                $imagesData,
                $deletedImageIds
            ): void {
                $oldProductId = (int)$variant->market_product_id;
                $newProductId = (int)$data['market_product_id'];
                $wasDefault = (bool)$variant->is_default;
                $willBeDefault = (bool)($data['is_default'] ?? false);

                if ($willBeDefault) {
                    $this->clearDefaultVariant(
                        productId: $newProductId,
                        exceptVariantId: (int)$variant->id
                    );
                }

                $variant->update($data);

                $this->syncTranslations($variant, $translations);
                $this->syncVariantValues($variant, $values);
                $this->syncImages(
                    $variant,
                    $request,
                    $imagesData,
                    $deletedImageIds
                );

                if ($oldProductId !== $newProductId) {
                    $this->ensureDefaultVariant($oldProductId);
                }

                if ($wasDefault && !$willBeDefault) {
                    $this->ensureDefaultVariant($newProductId);
                }

                $this->ensureDefaultVariant($newProductId);
            });

            return redirect()
                ->route('admin.marketProductVariants.index')
                ->with('success', 'Вариант товара успешно обновлён.');
        } catch (Throwable $e) {
            Log::error(
                'Ошибка обновления варианта товара ID ' . $variant->id . ': ' . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении варианта товара.');
        }
    }

    /** Удаление варианта товара. */
    public function destroy(int $marketProductVariant): RedirectResponse
    {
        $variant = $this->baseQuery()
            ->with([
                'images',
                'translations',
                'values',
            ])
            ->findOrFail($marketProductVariant);

        $productId = (int)$variant->market_product_id;

        try {
            DB::transaction(function () use ($variant, $productId): void {
                $this->deleteVariant($variant);
                $this->ensureDefaultVariant($productId);
            });

            return redirect()
                ->route('admin.marketProductVariants.index')
                ->with('success', 'Вариант товара успешно удалён.');
        } catch (Throwable $e) {
            Log::error(
                'Ошибка удаления варианта товара ID ' . $variant->id . ': ' . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with('error', 'Ошибка при удалении варианта товара.');
        }
    }

    /**
     * Массовое удаление вариантов товаров.
     *
     * Собственная реализация нужна для:
     * - удаления изображений и MediaLibrary;
     * - удаления значений характеристик;
     * - удаления переводов;
     * - восстановления основного варианта товара.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('market_product_variants', 'id'),
            ],
        ]);

        $ids = collect($validated['ids'])
            ->map(fn($id) => (int)$id)
            ->unique()
            ->values()
            ->all();

        $allowedIds = $this->baseQuery()
            ->whereIn('market_product_variants.id', $ids)
            ->pluck('market_product_variants.id')
            ->map(fn($id) => (int)$id)
            ->all();

        if (count($allowedIds) !== count($ids)) {
            return back()->with(
                'error',
                'Часть вариантов товаров недоступна для удаления.'
            );
        }

        try {
            DB::transaction(function () use ($allowedIds): void {
                $variants = $this->baseQuery()
                    ->whereIn('market_product_variants.id', $allowedIds)
                    ->with([
                        'images',
                        'translations',
                        'values',
                    ])
                    ->get();

                $productIds = $variants
                    ->pluck('market_product_id')
                    ->map(fn($id) => (int)$id)
                    ->unique()
                    ->values();

                foreach ($variants as $variant) {
                    $this->deleteVariant($variant);
                }

                foreach ($productIds as $productId) {
                    $this->ensureDefaultVariant($productId);
                }
            });

            return back()->with(
                'success',
                'Выбранные варианты товаров успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка массового удаления вариантов товаров: ' . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении вариантов товаров.'
            );
        }
    }

    /**
     * Базовый запрос вариантов.
     *
     * У варианта нет собственного user_id.
     * Доступ обычного пользователя определяется
     * через владельца родительского товара.
     *
     * Этот метод используется также унаследованными трейтами:
     * - HasMarketActivityTrait;
     * - HasMarketSortingTrait;
     * - HasMarketModerationTrait.
     */
    protected function baseQuery(): Builder
    {
        $query = MarketProductVariant::query();
        $user = auth()->user();

        if (
            $user
            && method_exists($user, 'hasRole')
            && !$user->hasRole('admin')
        ) {
            $query->whereHas('product', function (Builder $productQuery) use ($user): void {
                $productQuery->where('user_id', $user->id);
            });
        }

        return $query;
    }

    /** Доступные текущему пользователю товары. */
    private function productQuery(): Builder
    {
        $query = MarketProduct::query();
        $user = auth()->user();

        if (
            $user
            && method_exists($user, 'hasRole')
            && !$user->hasRole('admin')
        ) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Общие справочники для создания и редактирования.
     *
     * Все переводимые справочники загружаются только для текущей локали.
     *
     * @return array<string, mixed>
     */
    private function sharedSelects(?int $selectedProductId = null): array
    {
        $locale = app()->getLocale();

        $products = $this->productQuery()
            ->with([
                'translations' => fn($query) =>
                $query->where('locale', $locale),
                'currency',
            ])
            ->withCount('variants')
            ->orderBy('market_products.sort')
            ->orderByDesc('market_products.id')
            ->get();

        $currencies = Currency::query()
            ->active()
            ->ordered()
            ->get();

        $attributes = MarketAttribute::query()
            ->where('market_attributes.use_for_variants', true)
            ->whereIn('market_attributes.type', [
                'select',
                'multiselect',
            ])
            ->with([
                'translations' => fn($query) =>
                $query->where('locale', $locale),

                'group.translations' => fn($query) =>
                $query->where('locale', $locale),

                'values' => function ($query) use ($locale): void {
                    $query
                        ->with([
                            'translations' => fn($translationQuery) =>
                            $translationQuery->where('locale', $locale),
                        ])
                        ->orderBy('market_attribute_values.sort')
                        ->orderBy('market_attribute_values.id');
                },
            ])
            ->orderBy('market_attributes.sort')
            ->orderByDesc('market_attributes.id')
            ->get();

        return [
            'products' => MarketProductSharedResource::collection($products),
            'currencies' => CurrencyResource::collection($currencies),
            'attributes' => MarketAttributeResource::collection($attributes),
            'selectedProductId' => $selectedProductId,
        ];
    }

    /**
     * Базовый запрос списка вариантов.
     *
     * Index получает только текущую локаль переводов и все media
     * загружает пакетно, чтобы исключить N+1 Spatie MediaLibrary.
     */
    private function indexQuery(
        string $locale,
        ?int $productId = null
    ): Builder {
        return $this->baseQuery()
            ->when(
                $productId !== null,
                fn(Builder $query) => $query->where(
                    'market_product_variants.market_product_id',
                    $productId
                )
            )
            ->with([
                'moderator' => fn($query) =>
                $query->select('id', 'name', 'email', 'profile_photo_path'),

                'translations' => fn($query) =>
                $query->where('locale', $locale),

                'images.media',

                'currency',

                'product.translations' => fn($query) =>
                $query->where('locale', $locale),
                'product.currency',

                'values.attribute.translations' => fn($query) =>
                $query->where('locale', $locale),

                'values.attributeValue.translations' => fn($query) =>
                $query->where('locale', $locale),
            ])
            ->withCount([
                'images',
                'values',
            ]);
    }

    /** Получение списка по выбранному режиму обработки. */
    private function getIndexVariants(
        string $locale,
        bool   $useServerProcessing,
        int    $perPage,
        string $sort,
        string $search = '',
        ?int $productId = null
    ): LengthAwarePaginator|Collection
    {
        $query = $this->indexQuery($locale, $productId);

        if ($useServerProcessing) {
            return $query
                ->search($search, $locale)
                ->sortByParam($sort, $locale)
                ->paginate($perPage)
                ->withQueryString();
        }

        return $query
            ->sortByParam($sort, $locale)
            ->get();
    }

    /**
     * Товары для фильтра списка вариантов.
     *
     * @return array<int, array<string, mixed>>
     */
    private function productsForFilter(string $locale): array
    {
        return $this->productQuery()
            ->with([
                'translations' => fn($query) =>
                $query->where('locale', $locale),
            ])
            ->withCount('variants')
            ->orderBy('market_products.sort')
            ->orderByDesc('market_products.id')
            ->get()
            ->map(function (MarketProduct $product): array {
                $translation = $product->relationLoaded('translations')
                    ? $product->translations->first()
                    : null;

                return [
                    'id' => (int) $product->id,
                    'title' => $translation?->title,
                    'code' => $product->code,
                    'sku' => $product->sku,
                    'variants_count' => (int) $product->variants_count,
                ];
            })
            ->values()
            ->all();
    }

    /** Синхронизация значений характеристик варианта. */
    private function syncVariantValues(
        MarketProductVariant $variant,
        array                $values
    ): void
    {
        $existingIds = $variant->values()
            ->pluck('market_product_variant_values.id')
            ->map(fn($id) => (int)$id)
            ->all();

        $savedIds = [];

        foreach ($values as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $attributeId = $item['market_attribute_id'] ?? null;
            $attributeValueId = $item['market_attribute_value_id'] ?? null;

            if (!$attributeId || !$attributeValueId) {
                continue;
            }

            $payload = [
                'market_attribute_id' => (int)$attributeId,
                'market_attribute_value_id' => (int)$attributeValueId,
                'sort' => (int)($item['sort'] ?? $index),
            ];

            $rowId = !empty($item['id']) ? (int)$item['id'] : null;

            if ($rowId !== null) {
                $row = $variant->values()
                    ->whereKey($rowId)
                    ->first();

                if ($row) {
                    $row->update($payload);
                    $savedIds[] = (int)$row->id;

                    continue;
                }
            }

            $row = $variant->values()->create($payload);
            $savedIds[] = (int)$row->id;
        }

        $deleteIds = array_diff($existingIds, $savedIds);

        if ($deleteIds !== []) {
            MarketProductVariantValue::query()
                ->where('market_product_variant_id', $variant->id)
                ->whereIn('id', $deleteIds)
                ->delete();
        }
    }

    /** Удаление варианта вместе с зависимыми данными. */
    private function deleteVariant(MarketProductVariant $variant): void
    {
        $imageIds = $variant->images()
            ->pluck('market_product_variant_images.id')
            ->map(fn($id) => (int)$id)
            ->all();

        $variant->images()->detach();

        if ($imageIds !== []) {
            $this->deleteImages($imageIds);
        }

        $variant->values()->delete();
        $variant->translations()->delete();
        $variant->delete();
    }

    /**
     * Снять признак основного варианта
     * со всех остальных вариантов товара.
     */
    private function clearDefaultVariant(
        int  $productId,
        ?int $exceptVariantId = null
    ): void
    {
        MarketProductVariant::query()
            ->where('market_product_id', $productId)
            ->when(
                $exceptVariantId !== null,
                fn(Builder $query) => $query->where('id', '<>', $exceptVariantId)
            )
            ->where('is_default', true)
            ->update([
                'is_default' => false,
            ]);
    }

    /**
     * Гарантировать наличие одного основного варианта товара.
     *
     * Если основных вариантов несколько, сохраняется первый
     * по sort и id. Если основного варианта нет, первый вариант
     * автоматически назначается основным.
     */
    private function ensureDefaultVariant(int $productId): void
    {
        $variantsQuery = MarketProductVariant::query()
            ->where('market_product_id', $productId);

        if (!(clone $variantsQuery)->exists()) {
            return;
        }

        $defaultVariants = (clone $variantsQuery)
            ->where('is_default', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        if ($defaultVariants->count() > 1) {
            $keepId = (int)$defaultVariants->first()->id;

            (clone $variantsQuery)
                ->where('is_default', true)
                ->where('id', '<>', $keepId)
                ->update([
                    'is_default' => false,
                ]);

            return;
        }

        if ($defaultVariants->isNotEmpty()) {
            return;
        }

        $firstVariant = (clone $variantsQuery)
            ->orderBy('sort')
            ->orderBy('id')
            ->first();

        if ($firstVariant) {
            $firstVariant->update([
                'is_default' => true,
            ]);
        }
    }

    /**
     * Назначить вариант основным для родительского товара.
     */
    public function makeDefault(
        int $marketProductVariant
    ): RedirectResponse {
        $variant = $this->baseQuery()
            ->findOrFail($marketProductVariant);

        try {
            DB::transaction(function () use ($variant): void {
                $productId = (int) $variant->market_product_id;

                MarketProductVariant::query()
                    ->where('market_product_id', $productId)
                    ->where('id', '<>', $variant->id)
                    ->where('is_default', true)
                    ->update([
                        'is_default' => false,
                    ]);

                if (! $variant->is_default) {
                    $variant->update([
                        'is_default' => true,
                    ]);
                }
            });

            return back()->with(
                'success',
                'Основной вариант товара успешно изменён.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка назначения основного варианта товара ID '
                . $variant->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при назначении основного варианта товара.'
            );
        }
    }
}
