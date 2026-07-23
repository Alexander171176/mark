<?php

namespace App\Http\Controllers\Admin\Market\MarketProduct;

use App\Http\Controllers\Admin\Market\BaseMarketAdminController;
use App\Http\Requests\Admin\Market\MarketProduct\MarketProductRequest;
use App\Http\Resources\Admin\Finance\Currency\CurrencyResource;
use App\Http\Resources\Admin\Market\MarketAttribute\MarketAttributeResource;
use App\Http\Resources\Admin\Market\MarketAttributeValue\MarketAttributeValueResource;
use App\Http\Resources\Admin\Market\MarketBrand\MarketBrandResource;
use App\Http\Resources\Admin\Market\MarketCategory\MarketCategorySharedResource;
use App\Http\Resources\Admin\Market\MarketCompany\MarketCompanyResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductSharedResource;
use App\Http\Resources\Admin\Market\MarketShop\MarketShopResource;
use App\Http\Resources\Admin\Market\MarketTag\MarketTagSharedResource;
use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Market\MarketAttribute\MarketAttribute;
use App\Models\Admin\Market\MarketAttributeValue\MarketAttributeValue;
use App\Models\Admin\Market\MarketBrand\MarketBrand;
use App\Models\Admin\Market\MarketCategory\MarketCategory;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketProduct\MarketProductImage;
use App\Models\Admin\Market\MarketShop\MarketShop;
use App\Models\Admin\Market\MarketTag\MarketTag;
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
use Throwable;

/**
 * Контроллер управления товарами маркетплейса.
 *
 * Поддерживает:
 * - frontend / auto / server режимы списка;
 * - поиск, сортировку и пагинацию;
 * - мультиязычные переводы;
 * - изображения через Spatie MediaLibrary;
 * - категории и основную категорию;
 * - теги;
 * - характеристики;
 * - рекомендуемые товары;
 * - владельца и модерацию.
 */
class MarketProductController extends BaseMarketAdminController
{
    /** Основная модель */
    protected string $modelClass = MarketProduct::class;

    /** Модель изображений */
    protected string $imageModelClass = MarketProductImage::class;

    /** Коллекция Spatie MediaLibrary */
    protected string $imageMediaCollection = 'images';

    /** Название сущности для системных сообщений */
    protected string $entityLabel = 'товаров';

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

    /** Пресет изображений товара */
    protected string $imagePresetKey = 'rectangle_large';

    /** Директория обработанных изображений */
    protected string $imagePresetDirectory =
        'market/market_product_images/preset';

    /** Общие справочники для страниц create/edit. */
    private function sharedSelects(
        string $locale,
        ?int $excludeProductId = null
    ): array {
        $companies = MarketCompany::query()
            ->with('translations')
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $shops = MarketShop::query()
            ->with([
                'translations',
                'company.translations',
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $brands = MarketBrand::query()
            ->with('translations')
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $currencies = Currency::query()
            ->active()
            ->ordered()
            ->get();

        $categories = MarketCategory::query()
            ->with([
                'translations',
                'parent.translations',
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $tags = MarketTag::query()
            ->with('translations')
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $attributes = MarketAttribute::query()
            ->with([
                'translations',
                'group.translations',
                'values.translations',
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $attributeValues = MarketAttributeValue::query()
            ->with([
                'translations',
                'attribute.translations',
            ])
            ->orderBy('sort')
            ->orderByDesc('id')
            ->get();

        $relatedProducts = MarketProduct::query()
            ->with([
                'translations',
                'images',
                'currency',
                'company.translations',
                'shop.translations',
                'brand.translations',
            ])
            ->withCount([
                'images',
                'categories',
                'tags',
                'reviews',
            ])
            ->when(
                $excludeProductId,
                fn (Builder $query) => $query->where(
                    'market_products.id',
                    '<>',
                    $excludeProductId
                )
            )
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

        return [
            'companies' => MarketCompanyResource::collection($companies),
            'shops' => MarketShopResource::collection($shops),
            'brands' => MarketBrandResource::collection($brands),
            'currencies' => CurrencyResource::collection($currencies),

            'categories' => MarketCategorySharedResource::collection(
                $categories
            ),

            'tags' => MarketTagSharedResource::collection($tags),

            'attributes' => MarketAttributeResource::collection(
                $attributes
            ),

            'attributeValues' =>
                MarketAttributeValueResource::collection($attributeValues),

            /*
             * Оба имени можно временно передавать для удобства Vue.
             */
            'related_products' =>
                MarketProductSharedResource::collection($relatedProducts),

            'relatedProducts' =>
                MarketProductSharedResource::collection($relatedProducts),
        ];
    }

    /** Список товаров. */
    public function index(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        $settings = app(AdminSettingsService::class);

        $perPage = $settings->int(
            'adminMarketProductsPerPage',
            6
        );

        $defaultSort = $settings->string(
            'adminMarketProductsDefaultSort',
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
            'adminMarketProductsProcessingMode',
            'frontend'
        );

        $productsCount = $this->baseQuery()->count();

        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $productsCount,
                300
            );

        try {
            $products = $this->getIndexProducts(
                locale: $currentLocale,
                useServerProcessing: $useServerProcessing,
                perPage: $perPage,
                sort: $sortParam,
                search: $search
            );

            return Inertia::render(
                'Admin/Market/MarketProducts/Index',
                [
                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'useServerProcessing' => $useServerProcessing,

                    'adminMarketProductsPerPage' => $perPage,
                    'adminMarketProductsDefaultSort' => $defaultSort,
                    'adminMarketProductsProcessingMode' =>
                        $processingMode,

                    'products' =>
                        MarketProductResource::collection($products),

                    'productsCount' => $productsCount,

                    'sortParam' => $sortParam,
                    'search' => $search,
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка загрузки списка market products: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return Inertia::render(
                'Admin/Market/MarketProducts/Index',
                [
                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'useServerProcessing' => $useServerProcessing,

                    'adminMarketProductsPerPage' => $perPage,
                    'adminMarketProductsDefaultSort' => $defaultSort,
                    'adminMarketProductsProcessingMode' =>
                        $processingMode,

                    'products' => [],
                    'productsCount' => 0,

                    'sortParam' => $sortParam,
                    'search' => $search,

                    'error' => 'Ошибка загрузки товаров.',
                ]
            );
        }
    }

    /** Страница создания товара. */
    public function create(Request $request): Response
    {
        $currentLocale = $this->resolveLocale($request);

        return Inertia::render(
            'Admin/Market/MarketProducts/Create',
            array_merge(
                [
                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'imageProcessorEnabled' =>
                        $this->imageProcessorEnabled(),

                    'imagePreset' => $this->imagePresetPayload(),
                ],
                $this->sharedSelects($currentLocale)
            )
        );
    }

    /** Создание товара. */
    public function store(
        MarketProductRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];

        $categories = $data['categories'] ?? [];
        $tags = $data['tags'] ?? [];
        $relatedProducts = $data['related_products'] ?? [];
        $attributeValues = $data['attribute_values'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['categories'],
            $data['tags'],
            $data['related_products'],
            $data['attribute_values']
        );

        $user = auth()->user();

        if (
            $user
            && method_exists($user, 'hasRole')
            && ! $user->hasRole('admin')
        ) {
            $data['user_id'] = $user->id;

            /*
             * Обычный пользователь не должен назначать себе
             * одобрение и модератора.
             */
            unset(
                $data['moderation_status'],
                $data['moderated_by'],
                $data['moderated_at'],
                $data['moderation_note']
            );
        } else {
            $data['user_id'] = $data['user_id']
                ?? $user?->id;
        }

        try {
            DB::transaction(function () use (
                &$product,
                $request,
                $data,
                $translations,
                $imagesData,
                $categories,
                $tags,
                $relatedProducts,
                $attributeValues
            ): void {
                if (
                    ! isset($data['sort'])
                    || is_null($data['sort'])
                ) {
                    $maxSort = MarketProduct::query()->max('sort');

                    $data['sort'] = is_null($maxSort)
                        ? 0
                        : $maxSort + 1;
                }

                $product = MarketProduct::query()->create($data);

                $this->syncTranslations(
                    $product,
                    $translations
                );

                $this->syncCategories(
                    $product,
                    $categories
                );

                $this->syncTags(
                    $product,
                    $tags
                );

                $this->syncRelatedProducts(
                    $product,
                    $relatedProducts
                );

                $this->syncAttributeValues(
                    $product,
                    $attributeValues
                );

                $this->syncImages(
                    $product,
                    $request,
                    $imagesData
                );
            });

            return redirect()
                ->route('admin.marketProducts.index')
                ->with('success', 'Товар успешно создан.');
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при создании market product: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании товара.');
        }
    }

    /** Просмотр перенаправляем на редактирование. */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route(
            'admin.marketProducts.edit',
            $id
        );
    }

    /** Страница редактирования товара. */
    public function edit(
        int $marketProduct,
        Request $request
    ): Response {
        $product = $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'translations',
                'images',
                'currency',

                'company.translations',
                'shop.translations',
                'brand.translations',

                'categories.translations',
                'mainCategories.translations',
                'tags.translations',

                'attributeValues.attribute.translations',
                'attributeValues.attributeValue.translations',

                'relatedProducts.translations',
                'relatedProducts.images',
                'relatedProducts.currency',
                'relatedProducts.company.translations',
                'relatedProducts.shop.translations',
                'relatedProducts.brand.translations',
            ])
            ->withCount([
                'images',
                'categories',
                'tags',
                'attributeValues',
                'reviews',
                'likes',
                'relatedProducts',
            ])
            ->findOrFail($marketProduct);

        $currentLocale = $this->resolveLocale($request);

        return Inertia::render(
            'Admin/Market/MarketProducts/Edit',
            array_merge(
                [
                    'product' => new MarketProductResource($product),

                    'currentLocale' => $currentLocale,
                    'availableLocales' => $this->availableLocales(),

                    'imageProcessorEnabled' =>
                        $this->imageProcessorEnabled(),

                    'imagePreset' => $this->imagePresetPayload(),
                ],
                $this->sharedSelects(
                    $currentLocale,
                    $product->id
                )
            )
        );
    }

    /** Обновление товара. */
    public function update(
        MarketProductRequest $request,
        int $marketProduct
    ): RedirectResponse {
        $product = $this->baseQuery()
            ->findOrFail($marketProduct);

        $data = $request->validated();

        $translations = $data['translations'] ?? [];
        $imagesData = $data['images'] ?? [];
        $deletedImageIds = $data['deletedImages'] ?? [];

        $categories = $data['categories'] ?? [];
        $tags = $data['tags'] ?? [];
        $relatedProducts = $data['related_products'] ?? [];
        $attributeValues = $data['attribute_values'] ?? [];

        unset(
            $data['translations'],
            $data['images'],
            $data['deletedImages'],
            $data['categories'],
            $data['tags'],
            $data['related_products'],
            $data['attribute_values'],
            $data['_method']
        );

        $user = auth()->user();

        if (
            $user
            && method_exists($user, 'hasRole')
            && ! $user->hasRole('admin')
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
                $product,
                $request,
                $data,
                $translations,
                $imagesData,
                $deletedImageIds,
                $categories,
                $tags,
                $relatedProducts,
                $attributeValues
            ): void {
                $product->update($data);

                $this->syncTranslations(
                    $product,
                    $translations
                );

                $this->syncCategories(
                    $product,
                    $categories
                );

                $this->syncTags(
                    $product,
                    $tags
                );

                $this->syncRelatedProducts(
                    $product,
                    $relatedProducts
                );

                $this->syncAttributeValues(
                    $product,
                    $attributeValues
                );

                $this->syncImages(
                    $product,
                    $request,
                    $imagesData,
                    $deletedImageIds
                );
            });

            return redirect()
                ->route('admin.marketProducts.index')
                ->with('success', 'Товар успешно обновлён.');
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при обновлении market product ID '
                . $product->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении товара.');
        }
    }

    /** Удаление товара. */
    public function destroy(
        int $marketProduct
    ): RedirectResponse {
        $product = $this->baseQuery()
            ->with([
                'images',
                'translations',
                'attributeValues',
            ])
            ->findOrFail($marketProduct);

        try {
            DB::transaction(function () use ($product): void {
                $imageIds = $product->images()
                    ->pluck('market_product_images.id')
                    ->toArray();

                /*
                 * Сначала удаляем связи, потом сами изображения.
                 */
                $product->images()->detach();

                if (! empty($imageIds)) {
                    $this->deleteImages($imageIds);
                }

                $product->categories()->detach();
                $product->tags()->detach();

                $product->relatedProducts()->detach();
                $product->usedInRelatedProducts()->detach();

                $product->attributeValues()->delete();

                /*
                 * Отзывы и лайки также имеют cascadeOnDelete,
                 * но явное удаление делает жизненный цикл понятнее.
                 */
                $product->reviews()->delete();
                $product->likes()->delete();
                $product->translations()->delete();

                $product->delete();
            });

            return redirect()
                ->route('admin.marketProducts.index')
                ->with('success', 'Товар успешно удалён.');
        } catch (Throwable $e) {
            Log::error(
                'Ошибка при удалении market product ID '
                . $product->id
                . ': '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()
                ->with('error', 'Ошибка при удалении товара.');
        }
    }


    /** Массовое удаление товаров. */
    public function bulkDestroy(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
            ],

            'ids.*' => [
                'required',
                'integer',
                'exists:market_products,id',
            ],
        ]);

        $ids = $validated['ids'];

        /*
         * baseQuery ограничивает обычного пользователя
         * только принадлежащими ему товарами.
         */
        $allowedIds = $this->baseQuery()
            ->whereIn('market_products.id', $ids)
            ->pluck('market_products.id')
            ->toArray();

        if (count($allowedIds) !== count($ids)) {
            return back()->with(
                'error',
                'Часть товаров недоступна для удаления.'
            );
        }

        try {
            DB::transaction(function () use ($allowedIds): void {
                $products = MarketProduct::query()
                    ->whereIn('id', $allowedIds)
                    ->with('images')
                    ->get();

                foreach ($products as $product) {
                    $imageIds = $product->images()
                        ->pluck('market_product_images.id')
                        ->toArray();

                    $product->images()->detach();

                    if (! empty($imageIds)) {
                        $this->deleteImages($imageIds);
                    }

                    $product->categories()->detach();
                    $product->tags()->detach();

                    $product->relatedProducts()->detach();
                    $product->usedInRelatedProducts()->detach();

                    $product->attributeValues()->delete();
                    $product->reviews()->delete();
                    $product->likes()->delete();
                    $product->translations()->delete();

                    $product->delete();
                }
            });

            return back()->with(
                'success',
                'Выбранные товары успешно удалены.'
            );
        } catch (Throwable $e) {
            Log::error(
                'Ошибка bulkDestroy market products: '
                . $e->getMessage(),
                [
                    'exception' => $e,
                ]
            );

            return back()->with(
                'error',
                'Ошибка при массовом удалении товаров.'
            );
        }
    }

    /** Переключение флага "новый товар". */
    public function updateIsNew(Request $request, int $marketProduct): RedirectResponse
    {
        return $this->updateBooleanFlag($request, $marketProduct, 'is_new');
    }

    /** Переключение флага "рекомендуемый / хит продаж". */
    public function updateIsHit(Request $request, int $marketProduct): RedirectResponse
    {
        return $this->updateBooleanFlag($request, $marketProduct, 'is_hit');
    }

    /** Переключение флага "товар в распродаже". */
    public function updateIsSale(Request $request, int $marketProduct): RedirectResponse
    {
        return $this->updateBooleanFlag($request, $marketProduct, 'is_sale');
    }

    /** Массовое обновление флага "новый товар". */
    public function bulkUpdateIsNew(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'is_new');
    }

    /** Массовое обновление флага "рекомендуемый / хит продаж". */
    public function bulkUpdateIsHit(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'is_hit');
    }

    /** Массовое обновление флага "товар в распродаже". */
    public function bulkUpdateIsSale(Request $request): RedirectResponse|JsonResponse
    {
        return $this->bulkUpdateBooleanFlag($request, 'is_sale');
    }

    /** Обновление boolean-флага товара. */
    private function updateBooleanFlag(Request $request, int $marketProduct, string $field): RedirectResponse
    {
        $this->validateBooleanField($field);

        $validated = $request->validate([
            $field => ['required', 'boolean'],
        ]);

        $product = $this->baseQuery()->findOrFail($marketProduct);

        $product->update([
            $field => $validated[$field],
        ]);

        return back()->with('success', "Поле {$field} товара обновлено.");
    }

    /** Массовое обновление boolean-флага товаров. */
    private function bulkUpdateBooleanFlag(Request $request, string $field): RedirectResponse|JsonResponse
    {
        $this->validateBooleanField($field);

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:market_products,id'],
            $field => ['required', 'boolean'],
        ]);

        $allowedIds = $this->baseQuery()
            ->whereIn('market_products.id', $validated['ids'])
            ->pluck('market_products.id')
            ->toArray();

        if (count($allowedIds) !== count($validated['ids'])) {
            $message = 'Часть товаров недоступна для обновления.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $message], 403)
                : back()->with('error', $message);
        }

        MarketProduct::query()->whereIn('id', $allowedIds)->update([
            $field => $validated[$field],
        ]);

        $message = "Поле {$field} выбранных товаров обновлено.";

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => $message])
            : back()->with('success', $message);
    }

    /** Проверка разрешённого boolean-поля. */
    private function validateBooleanField(string $field): void
    {
        abort_unless(in_array($field, ['is_new', 'is_hit', 'is_sale'], true), 422, 'Недопустимое поле товара.');
    }

    /** Базовый запрос списка товаров. */
    private function indexQuery(): Builder
    {
        return $this->baseQuery()
            ->with([
                'owner',
                'moderator',
                'translations',
                'images',
                'currency',

                'company.translations',
                'shop.translations',
                'brand.translations',

                'categories.translations',
                'tags.translations',
            ])
            ->withCount([
                'images',
                'categories',
                'tags',
                'attributeValues',
                'reviews',
                'likes',
                'relatedProducts',
            ]);
    }

    /** Получение списка по активному режиму обработки. */
    private function getIndexProducts(
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

    /** Синхронизация категорий товара. */
    private function syncCategories(
        MarketProduct $product,
        array $categories
    ): void {
        $syncData = [];

        foreach ($categories as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $categoryId = $item['id'] ?? null;

            if (! $categoryId) {
                continue;
            }

            $syncData[(int) $categoryId] = [
                'main' => (bool) ($item['main'] ?? false),
                'order' => (int) ($item['order'] ?? $index),
            ];
        }

        $product->categories()->sync($syncData);
    }

    /** Синхронизация тегов товара. */
    private function syncTags(
        MarketProduct $product,
        array $tags
    ): void {
        $syncData = [];

        foreach ($tags as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $tagId = $item['id'] ?? null;

            if (! $tagId) {
                continue;
            }

            $syncData[(int) $tagId] = [
                'order' => (int) ($item['order'] ?? $index),
            ];
        }

        $product->tags()->sync($syncData);
    }

    /** Синхронизация рекомендуемых товаров. */
    private function syncRelatedProducts(
        MarketProduct $product,
        array $relatedProducts
    ): void {
        /*
         * Request запрещает повторы id, поэтому стандартного sync()
         * достаточно: один рекомендуемый товар — одна связь.
         */
        $syncData = [];

        foreach ($relatedProducts as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $relatedProductId = $item['id'] ?? null;

            if (
                ! $relatedProductId
                || (int) $relatedProductId === (int) $product->id
            ) {
                continue;
            }

            $syncData[(int) $relatedProductId] = [
                'type' => $item['type'] ?? 'related',
                'order' => (int) ($item['order'] ?? $index),
                'activity' => (bool) ($item['activity'] ?? true),
            ];
        }

        /*
         * В relation есть wherePivot(activity = true).
         * Для надёжного обновления также удаляем старые исходящие
         * строки напрямую из pivot-таблицы.
         */
        DB::table('market_product_related')
            ->where('market_product_id', $product->id)
            ->delete();

        if ($syncData !== []) {
            $product->relatedProducts()->attach($syncData);
        }
    }

    /**
     * Синхронизация характеристик товара.
     *
     * Одна характеристика может присутствовать у товара
     * только один раз.
     */
    private function syncAttributeValues(
        MarketProduct $product,
        array $attributeValues
    ): void {
        $receivedAttributeIds = [];

        foreach ($attributeValues as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $attributeId = $item['market_attribute_id'] ?? null;

            if (! $attributeId) {
                continue;
            }

            $attributeId = (int) $attributeId;

            $receivedAttributeIds[] = $attributeId;

            $product->attributeValues()->updateOrCreate(
                [
                    'market_attribute_id' => $attributeId,
                ],
                [
                    'market_attribute_value_id' =>
                        $item['market_attribute_value_id'] ?? null,

                    'value_string' =>
                        $item['value_string'] ?? null,

                    'value_number' =>
                        $item['value_number'] ?? null,

                    'value_boolean' =>
                        $item['value_boolean'] ?? null,

                    'value_date' =>
                        $item['value_date'] ?? null,

                    'value_json' =>
                        $item['value_json'] ?? null,

                    'unit' => $item['unit'] ?? null,

                    'order' => (int) (
                        $item['order'] ?? $index
                    ),

                    'activity' => (bool) (
                        $item['activity'] ?? true
                    ),
                ]
            );
        }

        /*
         * Удаляем характеристики, которые пользователь убрал из формы.
         */
        $query = $product->attributeValues();

        if ($receivedAttributeIds === []) {
            $query->delete();

            return;
        }

        $query
            ->whereNotIn(
                'market_attribute_id',
                array_unique($receivedAttributeIds)
            )
            ->delete();
    }
}
