<?php

namespace App\Http\Controllers\Public\Default\Market\MarketProduct;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Market\MarketCategory\MarketCategorySharedResource;
use App\Http\Resources\Public\Market\MarketProduct\MarketProductResource;
use App\Http\Resources\Public\Market\MarketProduct\MarketProductSharedResource;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\Public\Market\MarketRecentlyViewedProductService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\Market\BuildsMarketCategoryTreeTrait;
use App\Traits\Public\Market\HasMarketBrandCarouselDataTrait;
use App\Traits\Public\Market\HasMarketSidebarDataTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketProductController extends Controller
{
    use HasPublicIndexFiltersTrait;
    use BuildsMarketCategoryTreeTrait;
    use HasMarketSidebarDataTrait;
    use HasMarketBrandCarouselDataTrait;

    /** Конструктор с сервисом просмотренных товаров */
    public function __construct(
        private readonly MarketRecentlyViewedProductService $recentlyViewedService
    ) {
    }

    /** Страница списка товаров маркетплейса */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        /**
         * SEO из CMS.
         */
        $cmsSeoPage = app(CmsPageResolverService::class)
            ->resolveSeo($request->path());

        $cmsSeoTranslation = $cmsSeoPage
            ?->translationOrFallback();

        $seo = $cmsSeoTranslation
            ? [
                'title' =>
                    $cmsSeoTranslation->meta_title
                        ?: $cmsSeoTranslation->title,

                'keywords' =>
                    $cmsSeoTranslation->meta_keywords,

                'description' =>
                    $cmsSeoTranslation->meta_desc
                        ?: $cmsSeoTranslation->short,
            ]
            : [
                'title' => __('Товары'),
                'keywords' => '',
                'description' => '',
            ];

        /**
         * Публичные настройки.
         */
        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int(
                'publicMarketProductsPerPage',
                12
            )
        );

        $search = $this->resolveSearch($request);

        $defaultSort = $settings->string(
            'publicMarketProductsDefaultSort',
            'sortAsc'
        );

        $sort = $this->resolveSort(
            $request,
            $defaultSort
        );

        $view = $this->resolveView(
            $request,
            $settings->string(
                'publicMarketProductsDefaultView',
                'grid'
            )
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string(
                'publicMarketProductsProcessingMode',
                'server'
            )
        );

        /**
         * Общее количество публичных товаров.
         */
        $productsCount = MarketProduct::query()
            ->forPublic()
            ->count();

        /**
         * Определяем server/frontend режим.
         */
        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $productsCount,
            300
        );

        /**
         * Получаем товары.
         */
        $products = $this->getIndexProducts(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        /**
         * Количество найденных товаров.
         */
        $productsFound = $useServerProcessing
            ? $products->total()
            : $products->count();

        /**
         * Единый Public SharedResource-контракт.
         */
        $products = MarketProductSharedResource::collection(
            $products
        );

        /**
         * Дерево категорий для левого сайдбара.
         */
        $categoryTree = $this->getMarketCategoryTree(
            $locale
        );

        /**
         * Данные сайдбаров маркетплейса.
         */
        $sidebarData = $this->getMarketSidebarData(
            $locale
        );

        /**
         * Карусель брендов маркетплейса.
         */
        $brandCarouselData =
            $this->getMarketBrandCarouselData(
                $locale
            );

        /**
         * Недавно просмотренные товары.
         */
        $recentlyViewedProducts =
            $this->getRecentlyViewedProducts(
                locale: $locale
            );

        return Inertia::render(
            'Public/Default/Market/MarketProducts/Index',
            [
                'seo' =>
                    $seo,

                'publicMarketProductsProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

                'products' =>
                    $products,

                'productsCount' =>
                    $productsCount,

                'productsFound' =>
                    $productsFound,

                'filters' =>
                    $this->buildIndexFilters(
                        $search,
                        $perPage,
                        $sort,
                        $view,
                        $processingMode
                    ),

                'defaultSort' =>
                    $defaultSort,

                'recentlyViewedProducts' =>
                    $recentlyViewedProducts,

                'categoryTree' =>
                    $categoryTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
                ...$brandCarouselData,
            ]
        );
    }

    /** Страница конкретного товара маркетплейса */
    public function show(string $url): Response
    {
        $locale = app()->getLocale();

        $locales = $this->publicLocales(
            $locale
        );

        $userId = auth()->check()
            ? (int) auth()->id()
            : null;

        /**
         * Получаем только публичный товар.
         */
        $query = MarketProduct::query()
            ->forPublic()
            ->where(
                'market_products.url',
                $url
            )
            ->with([
                /**
                 * Переводы только:
                 * current + configured fallback.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                'currency',

                'images.media',

                /**
                 * Бренд только Public.
                 */
                'brand' =>
                    fn ($query) =>
                    $query
                        ->forPublic()
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),
                        ]),

                /**
                 * Публичные категории.
                 */
                'categories' =>
                    fn ($query) =>
                    $query
                        ->forPublic()
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),

                            'images.media',
                        ])
                        ->ordered(),

                /**
                 * Основные публичные категории.
                 */
                'mainCategories' =>
                    fn ($query) =>
                    $query
                        ->forPublic()
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),

                            'images.media',
                        ])
                        ->ordered(),

                /**
                 * Public-теги.
                 */
                'tags' =>
                    fn ($query) =>
                    $query
                        ->forPublic()
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),
                        ])
                        ->ordered($locale),

                /**
                 * Значения характеристик.
                 */
                'attributeValues' =>
                    fn ($query) =>
                    $query
                        ->where(
                            'activity',
                            true
                        )
                        ->with([
                            'attribute.translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),

                            'attributeValue.translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),
                        ])
                        ->orderBy('order')
                        ->orderBy('id'),

                /**
                 * Публичные варианты.
                 */
                'publicVariants' =>
                    fn ($query) =>
                    $query
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),

                            'currency',

                            'images.media',

                            'values.attribute.translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),

                            'values.attributeValue.translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),
                        ])
                        ->withCount([
                            'values',
                            'images',
                        ]),

                /**
                 * Только публичные отзывы.
                 */
                'reviews' =>
                    fn ($query) =>
                    $query
                        ->forPublic()
                        ->with([
                            'author:id,name,profile_photo_path',
                            'replier:id,name,profile_photo_path',
                            'images.media',
                        ])
                        ->latest(),

                /**
                 * Похожие товары:
                 * только Public.
                 */
                'relatedProducts' =>
                    function ($query) use (
                        $locales,
                        $userId
                    ) {
                        $query
                            ->forPublic()
                            ->with([
                                'translations' =>
                                    fn ($translationQuery) =>
                                    $translationQuery
                                        ->whereIn(
                                            'locale',
                                            $locales
                                        ),

                                'currency',

                                'images.media',

                                'brand' =>
                                    fn ($brandQuery) =>
                                    $brandQuery
                                        ->forPublic()
                                        ->with([
                                            'translations' =>
                                                fn ($translationQuery) =>
                                                $translationQuery
                                                    ->whereIn(
                                                        'locale',
                                                        $locales
                                                    ),
                                        ]),
                            ])
                            ->withCount([
                                'publicVariants',

                                'reviews' =>
                                    fn ($reviewQuery) =>
                                    $reviewQuery->forPublic(),
                            ]);

                        /**
                         * Состояние лайка связанных товаров.
                         */
                        if ($userId) {
                            $query->withExists([
                                'likes as already_liked' =>
                                    fn ($likeQuery) =>
                                    $likeQuery->where(
                                        'user_id',
                                        $userId
                                    ),
                            ]);
                        }
                    },
            ])
            ->withCount([
                'images',

                'categories' =>
                    fn ($query) =>
                    $query->forPublic(),

                'publicVariants',

                'reviews' =>
                    fn ($query) =>
                    $query->forPublic(),
            ]);

        /**
         * Состояние лайка основного товара.
         */
        if ($userId) {
            $query->withExists([
                'likes as already_liked' =>
                    fn ($likeQuery) =>
                    $likeQuery->where(
                        'user_id',
                        $userId
                    ),
            ]);
        }

        $product = $query->firstOrFail();

        /**
         * Увеличиваем просмотры.
         */
        $product->increment('views');

        /**
         * Запоминаем просмотр товара
         * авторизованного пользователя.
         */
        if ($userId) {
            $this->recentlyViewedService->remember(
                userId: $userId,
                productId: (int) $product->id
            );
        }

        /**
         * Основной Public Resource.
         *
         * already_liked уже входит
         * в MarketProductResource.
         */
        $productData = (
        new MarketProductResource(
            $product
        )
        )->resolve();

        /**
         * Связанные товары.
         *
         * already_liked уже входит
         * в MarketProductSharedResource.
         */
        $productData['related_products'] =
            $product
                ->relatedProducts
                ->map(
                    fn (MarketProduct $relatedProduct) =>
                    (
                    new MarketProductSharedResource(
                        $relatedProduct
                    )
                    )->resolve()
                )
                ->values()
                ->all();

        /**
         * Основная категория
         * для хлебных крошек.
         */
        $breadcrumbCategory =
            $product
                ->mainCategories
                ->first()
                ?: $product
                ->categories
                ->first();

        /**
         * Дерево категорий.
         */
        $categoryTree =
            $this->getMarketCategoryTree(
                $locale
            );

        /**
         * Данные сайдбаров.
         */
        $sidebarData =
            $this->getMarketSidebarData(
                $locale
            );

        /**
         * Недавно просмотренные товары.
         * Текущий товар исключаем.
         */
        $recentlyViewedProducts =
            $this->getRecentlyViewedProducts(
                locale: $locale,
                excludeProductId: $product->id
            );

        return Inertia::render(
            'Public/Default/Market/MarketProducts/Show',
            [
                'product' =>
                    $productData,

                'breadcrumbCategory' =>
                    $breadcrumbCategory
                        ? (
                    new MarketCategorySharedResource(
                        $breadcrumbCategory
                    )
                    )->resolve()
                        : null,

                'recentlyViewedProducts' =>
                    $recentlyViewedProducts,

                'categoryTree' =>
                    $categoryTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /** Лайк товара */
    public function like(string $id): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Для постановки лайка нужно авторизоваться.',
            ], 401);
        }

        $product = MarketProduct::query()
            ->forPublic()
            ->findOrFail($id);

        $userId = (int) auth()->id();

        $alreadyLiked = $product
            ->likes()
            ->where(
                'user_id',
                $userId
            )
            ->exists();

        if ($alreadyLiked) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Вы уже поставили лайк.',
                'likes' =>
                    $product->likes()->count(),
            ]);
        }

        $product->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'likes' =>
                $product->likes()->count(),
        ]);
    }

    /**
     * Получить недавно просмотренные товары.
     *
     * Авторизованный пользователь:
     * история из БД.
     *
     * Гость:
     * ID приходят из localStorage.
     */
    public function recentlyViewed(
        Request $request
    ): JsonResponse {
        $locale = app()->getLocale();

        if (auth()->check()) {
            $products =
                $this->getRecentlyViewedProducts(
                    locale: $locale
                );

            return response()->json([
                'success' => true,
                'products' => $products,
            ]);
        }

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

        $ids = collect(
            $validated['ids'] ?? []
        )
            ->map(
                fn ($id) =>
                (int) $id
            )
            ->filter(
                fn ($id) =>
                    $id > 0
            )
            ->unique()
            ->take(8)
            ->values();

        if ($ids->isEmpty()) {
            return response()->json([
                'success' => true,
                'products' => [],
            ]);
        }

        $locales = $this->publicLocales(
            $locale
        );

        /**
         * Только актуальные Public товары.
         */
        $products = MarketProduct::query()
            ->forPublic()
            ->whereIn(
                'market_products.id',
                $ids
            )
            ->with([
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                'currency',

                'images.media',

                'brand' =>
                    fn ($query) =>
                    $query
                        ->forPublic()
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),
                        ]),
            ])
            ->withCount([
                'publicVariants',

                'reviews' =>
                    fn ($query) =>
                    $query->forPublic(),
            ])
            ->get();

        /**
         * whereIn() не гарантирует порядок.
         * Восстанавливаем порядок localStorage.
         */
        $positions = $ids->flip();

        $products = $products
            ->sortBy(
                fn (MarketProduct $product) =>
                $positions->get(
                    $product->id,
                    PHP_INT_MAX
                )
            )
            ->values()
            ->map(
                fn (MarketProduct $product) =>
                (
                new MarketProductSharedResource(
                    $product
                )
                )->resolve()
            )
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
    public function mergeRecentlyViewed(
        Request $request
    ): JsonResponse {
        if (! auth()->check()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Необходимо авторизоваться.',
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

        $ids = collect(
            $validated['ids']
        )
            ->map(
                fn ($id) =>
                (int) $id
            )
            ->filter(
                fn ($id) =>
                    $id > 0
            )
            ->unique()
            ->values()
            ->all();

        if ($ids === []) {
            return response()->json([
                'success' => true,
                'products' => [],
            ]);
        }

        $this->recentlyViewedService
            ->mergeGuestHistory(
                userId: (int) auth()->id(),
                productIds: $ids
            );

        $products =
            $this->getRecentlyViewedProducts(
                locale: app()->getLocale()
            );

        return response()->json([
            'success' => true,
            'products' => $products,
        ]);
    }

    /**
     * Очистить историю недавно
     * просмотренных товаров.
     */
    public function clearRecentlyViewed(): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Необходимо авторизоваться.',
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
     * Получить recently viewed
     * авторизованного пользователя.
     */
    private function getRecentlyViewedProducts(
        string $locale,
        ?int $excludeProductId = null
    ): array {
        if (! auth()->check()) {
            return [];
        }

        return $this
            ->recentlyViewedService
            ->getProducts(
                userId: (int) auth()->id(),
                excludeProductId: $excludeProductId,
                locale: $locale
            )
            ->map(
                fn (MarketProduct $product) =>
                (
                new MarketProductSharedResource(
                    $product
                )
                )->resolve()
            )
            ->values()
            ->all();
    }

    /**
     * Базовый запрос Public Index.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        $locales = $this->publicLocales(
            $locale
        );

        $query = MarketProduct::query()
            ->forPublic()
            ->with([
                /**
                 * Только current + fallback.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                'currency',

                'images.media',

                /**
                 * Компания и магазин
                 * Public Index не нужны.
                 */
                'brand' =>
                    fn ($query) =>
                    $query
                        ->forPublic()
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery
                                    ->whereIn(
                                        'locale',
                                        $locales
                                    ),
                        ]),
            ])
            ->withCount([
                /**
                 * SharedResource использует
                 * эти два count.
                 */
                'publicVariants',

                'reviews' =>
                    fn ($query) =>
                    $query->forPublic(),
            ]);

        /**
         * Состояние лайка текущего пользователя.
         */
        if (auth()->check()) {
            $query->withExists([
                'likes as already_liked' =>
                    fn ($likeQuery) =>
                    $likeQuery->where(
                        'user_id',
                        auth()->id()
                    ),
            ]);
        }

        return $query;
    }

    /**
     * Получение товаров
     * по активному режиму обработки.
     */
    private function getIndexProducts(
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
                ->publicSearch(
                    $search,
                    $locale
                )
                ->publicSortByParam(
                    $sort,
                    $locale
                )
                ->paginate(
                    $perPage
                )
                ->withQueryString();
        }

        /**
         * Frontend получает полный
         * Public-набор уже в том же
         * начальном порядке.
         *
         * Поиск выполняет Vue.
         */
        return $query
            ->publicSortByParam(
                $sort,
                $locale
            )
            ->get();
    }

    /**
     * Разрешённые локали Public:
     * current + configured fallback.
     */
    private function publicLocales(
        string $locale
    ): array {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        return array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );
    }
}
