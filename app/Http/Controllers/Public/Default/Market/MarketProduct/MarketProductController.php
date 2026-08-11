<?php

namespace App\Http\Controllers\Public\Default\Market\MarketProduct;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Market\MarketCategory\MarketCategorySharedResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductSharedResource;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\Public\Market\MarketRecentlyViewedProductService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\Market\BuildsMarketCategoryTreeTrait;
use App\Traits\Public\Market\HasMarketSidebarDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketProductController extends Controller
{
    use WithUserLikesTrait;            // трейт лайков
    use HasPublicIndexFiltersTrait;    // трейт для списка
    use BuildsMarketCategoryTreeTrait; // трейт дерево категорий для сайдбара
    use HasMarketSidebarDataTrait;     // трейт сервиса данных сайдбаров

    /** Конструктор с сервисом просмотренных товаров */
    public function __construct(
        private readonly MarketRecentlyViewedProductService $recentlyViewedService
    ) {
    }

    /** Страница списка товаров маркетплейса. */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        /** SEO из CMS */
        $cmsSeoPage = app(CmsPageResolverService::class)
            ->resolveSeo($request->path());

        $cmsSeoTranslation = $cmsSeoPage?->translationOrFallback();

        $seo = $cmsSeoTranslation
            ? [
                'title' => $cmsSeoTranslation->meta_title ?: $cmsSeoTranslation->title,
                'keywords' => $cmsSeoTranslation->meta_keywords,
                'description' => $cmsSeoTranslation->meta_desc ?: $cmsSeoTranslation->short,
            ]
            : [
                'title' => __('Товары'),
                'keywords' => '',
                'description' => '',
            ];

        /** Публичные настройки */
        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicMarketProductsPerPage', 12)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicMarketProductsDefaultSort', 'sortAsc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicMarketProductsDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicMarketProductsProcessingMode', 'server')
        );

        /** Общее количество публичных товаров */
        $productsCount = MarketProduct::query()
            ->forPublic()
            ->count();

        /** Определяем server/frontend режим */
        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $productsCount,
                300
            );

        /** Получаем товары */
        $products = $this->getIndexProducts(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        /** Количество найденных товаров */
        $productsFound = $useServerProcessing
            ? $products->total()
            : $products->count();

        /**
         * В server-режиме добавляем already_liked
         * непосредственно в элементы пагинации.
         */
        $products = $useServerProcessing
            ? $this->appendUserLikes(
                $products,
                MarketProductSharedResource::class
            )
            : MarketProductSharedResource::collection($products);

        /** Дерево категорий для левого сайдбара */
        $categoryTree = $this->getMarketCategoryTree($locale);

        /** Данные сайдбаров маркетплейса */
        $sidebarData = $this->getMarketSidebarData($locale);

        /** Недавно просмотренные товары */
        $recentlyViewedProducts = $this->getRecentlyViewedProducts(
            locale: $locale
        );

        return Inertia::render('Public/Default/Market/MarketProducts/Index', [
            'seo' => $seo,

            'publicMarketProductsProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'products' => $products,

            'productsCount' => $productsCount,
            'productsFound' => $productsFound,

            'filters' => $this->buildIndexFilters(
                $search,
                $perPage,
                $sort,
                $view,
                $processingMode
            ),

            /** Недавно просмотренные товары */
            'recentlyViewedProducts' => $recentlyViewedProducts,

            'categoryTree' => $categoryTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /** Страница конкретного товара маркетплейса. */
    public function show(string $url): Response
    {
        $locale = app()->getLocale();

        /** Получаем публичный товар */
        $product = MarketProduct::query()
            ->forPublic()
            ->where('url', $url)
            ->with([
                /** Основные данные */
                'translations',
                'owner',
                'currency',
                'images',

                /** Компания / магазин / бренд */
                'company.translations',
                'shop.translations',
                'brand.translations',

                /** Категории */
                'categories' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations',
                        'images',
                    ])
                    ->ordered(),

                /** Основные категории */
                'mainCategories' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations',
                        'images',
                    ])
                    ->ordered(),

                /** Теги */
                'tags' => fn ($query) => $query
                    ->forPublic()
                    ->with('translations')
                    ->ordered($locale),

                /** Характеристики */
                'attributeValues' => fn ($query) => $query
                    ->where('activity', true)
                    ->with([
                        'attribute.translations',
                        'attributeValue.translations',
                    ])
                    ->orderBy('order')
                    ->orderBy('id'),

                /** Только публичные варианты */
                'publicVariants' => fn ($query) => $query
                    ->with([
                        'translations',
                        'currency',
                        'images',

                        'values.attribute.translations',
                        'values.attributeValue.translations',
                    ])
                    ->withCount([
                        'values',
                        'images',
                    ]),

                /** Публичные отзывы */
                'reviews' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'author:id,name,profile_photo_path',
                        'replier:id,name,profile_photo_path',
                        'images',
                    ])
                    ->latest(),

                /** Рекомендуемые / похожие товары */
                'relatedProducts' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations',
                        'currency',
                        'images',
                        'company.translations',
                        'shop.translations',
                        'brand.translations',
                    ])
                    ->withCount([
                        'images',
                        'categories',
                        'tags',
                        'variants',
                        'reviews',
                        'likes',
                    ]),
            ])
            ->withCount([
                'images',
                'categories',
                'tags',
                'attributeValues',
                'variants',
                'reviews',
                'likes',
                'relatedProducts',
            ])
            ->firstOrFail();

        /** Увеличиваем просмотры товара */
        $product->increment('views');

        /**
         * Запоминаем просмотр товара
         * для авторизованного пользователя.
         *
         * Для гостя история будет храниться
         * на frontend в localStorage.
         */
        if (auth()->check()) {
            $this->recentlyViewedService->remember(
                userId: (int) auth()->id(),
                productId: (int) $product->id
            );
        }

        /** Лайк текущего пользователя */
        $alreadyLiked = auth()->check()
            ? $product->likes()
                ->where('user_id', auth()->id())
                ->exists()
            : false;

        /** Формируем основной ресурс товара */
        $productData = (new MarketProductResource($product))->resolve();

        $productData['already_liked'] = $alreadyLiked;

        /** Добавляем состояние лайка связанным товарам */
        $productData['related_products'] = $product->relatedProducts
            ->map(function ($relatedProduct) {
                $resolved = (new MarketProductSharedResource(
                    $relatedProduct
                ))->resolve();

                $resolved['already_liked'] = auth()->check()
                    ? $relatedProduct->likes()
                        ->where('user_id', auth()->id())
                        ->exists()
                    : false;

                return $resolved;
            })
            ->values()
            ->all();

        /** Основная категория для хлебных крошек */
        $breadcrumbCategory = $product->mainCategories
            ->first()
            ?: $product->categories->first();

        /** Дерево категорий для левого сайдбара */
        $categoryTree = $this->getMarketCategoryTree($locale);

        /** Данные сайдбаров маркетплейса */
        $sidebarData = $this->getMarketSidebarData($locale);

        /**
         * Недавно просмотренные товары.
         *
         * Текущий товар исключаем,
         * чтобы он не показывался сам у себя.
         */
        $recentlyViewedProducts = $this->getRecentlyViewedProducts(
            locale: $locale,
            excludeProductId: $product->id
        );

        return Inertia::render('Public/Default/Market/MarketProducts/Show', [
            'product' => $productData,

            'breadcrumbCategory' => $breadcrumbCategory
                ? (new MarketCategorySharedResource(
                    $breadcrumbCategory
                ))->resolve()
                : null,

            /** Недавно просмотренные товары */
            'recentlyViewedProducts' => $recentlyViewedProducts,

            'categoryTree' => $categoryTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /** Лайк товара. */
    public function like(string $id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        /** Получаем публичный товар */
        $product = MarketProduct::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = auth()->id();

        /** Проверяем существующий лайк */
        $alreadyLiked = $product->likes()
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyLiked) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже поставили лайк.',
                'likes' => $product->likes()->count(),
            ]);
        }

        /** Создаём лайк */
        $product->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'likes' => $product->likes()->count(),
        ]);
    }

    /**
     * Получить недавно просмотренные товары.
     *
     * Для авторизованного пользователя история берётся из БД.
     * Для гостя ID товаров передаются с frontend из localStorage.
     */
    public function recentlyViewed(Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        /**
         * Авторизованный пользователь:
         * получаем персональную историю из БД.
         */
        if (auth()->check()) {
            $products = $this->getRecentlyViewedProducts(
                locale: $locale
            );

            return response()->json([
                'success' => true,
                'products' => $products,
            ]);
        }

        /**
         * Гость:
         * frontend передаёт ID из localStorage.
         */
        $validated = $request->validate([
            'ids' => [
                'nullable',
                'array',
                'max:8',
            ],

            'ids.*' => [
                'integer',
                'distinct',
                'min:1',
            ],
        ]);

        $ids = collect($validated['ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->take(8)
            ->values();

        if ($ids->isEmpty()) {
            return response()->json([
                'success' => true,
                'products' => [],
            ]);
        }

        /**
         * Получаем только актуальные публичные товары.
         */
        $products = MarketProduct::query()
            ->forPublic()
            ->whereIn('market_products.id', $ids)
            ->with([
                'translations',
                'currency',
                'images',

                'company.translations',
                'shop.translations',
                'brand.translations',
            ])
            ->withCount([
                'images',
                'categories',
                'tags',
                'variants',
                'reviews',
                'likes',
            ])
            ->get();

        /**
         * whereIn() не гарантирует порядок ID,
         * поэтому восстанавливаем порядок localStorage:
         * самый свежий товар должен остаться первым.
         */
        $positions = $ids
            ->flip();

        $products = $products
            ->sortBy(
                fn (MarketProduct $product) =>
                $positions->get($product->id, PHP_INT_MAX)
            )
            ->values()
            ->map(function (MarketProduct $product) {
                $resolved = (new MarketProductSharedResource(
                    $product
                ))->resolve();

                /**
                 * У гостя лайк не может принадлежать
                 * текущему пользователю.
                 */
                $resolved['already_liked'] = false;

                return $resolved;
            })
            ->all();

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Объединить гостевую историю просмотров
     * с историей авторизованного пользователя.
     */
    public function mergeRecentlyViewed(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо авторизоваться.',
            ], 401);
        }

        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
                'max:20',
            ],

            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'min:1',
            ],
        ]);

        $ids = collect($validated['ids'])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if ($ids === []) {
            return response()->json([
                'success' => true,
                'products' => [],
            ]);
        }

        /**
         * Передаём гостевую историю сервису.
         *
         * Ожидаемый порядок:
         * первый ID — самый недавно просмотренный товар.
         */
        $this->recentlyViewedService->mergeGuestHistory(
            userId: (int) auth()->id(),
            productIds: $ids
        );

        /**
         * Сразу возвращаем объединённую историю,
         * чтобы frontend не делал дополнительный запрос.
         */
        $products = $this->getRecentlyViewedProducts(
            locale: app()->getLocale()
        );

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Очистить историю недавно просмотренных товаров
     * авторизованного пользователя.
     */
    public function clearRecentlyViewed(): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Необходимо авторизоваться.',
            ], 401);
        }

        $this->recentlyViewedService->clear(
            userId: (int) auth()->id()
        );

        return response()->json([
            'success' => true,
            'products' => [],
        ]);
    }

    /**
     * Получить недавно просмотренные товары
     * авторизованного пользователя.
     *
     * Для гостя возвращается пустой массив:
     * его история обрабатывается через localStorage.
     */
    private function getRecentlyViewedProducts(
        string $locale,
        ?int $excludeProductId = null
    ): array {
        if (!auth()->check()) {
            return [];
        }

        return $this->recentlyViewedService
            ->getProducts(
                userId: (int) auth()->id(),
                excludeProductId: $excludeProductId,
                locale: $locale
            )
            ->map(function (MarketProduct $product) {
                $resolved = (new MarketProductSharedResource(
                    $product
                ))->resolve();

                $resolved['already_liked'] =
                    (bool) $product->already_liked;

                return $resolved;
            })
            ->values()
            ->all();
    }

    /** Базовый запрос списка публичных товаров. */
    private function indexQuery(): Builder
    {
        return MarketProduct::query()
            ->forPublic()
            ->with([
                'translations',
                'currency',
                'images',

                'company.translations',
                'shop.translations',
                'brand.translations',
            ])
            ->withCount([
                'images',
                'categories',
                'tags',
                'variants',
                'reviews',
                'likes',
            ]);
    }

    /** Получение товаров по активному режиму обработки. */
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
            ->sortByParam($sort, $locale)
            ->get();
    }
}
