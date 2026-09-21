<?php

namespace App\Http\Controllers\Public\Default\Market\MarketBrand;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Market\MarketBrand\MarketBrandResource;
use App\Http\Resources\Public\Market\MarketBrand\MarketBrandSharedResource;
use App\Http\Resources\Public\Market\MarketProduct\MarketProductSharedResource;
use App\Models\Admin\Market\MarketBrand\MarketBrand;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\Market\BuildsMarketCategoryTreeTrait;
use App\Traits\Public\Market\HasMarketSidebarDataTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketBrandController extends Controller
{
    use HasPublicIndexFiltersTrait;
    use BuildsMarketCategoryTreeTrait;
    use HasMarketSidebarDataTrait;

    /**
     * Страница списка брендов маркетплейса.
     */
    public function index(Request $request): Response
    {
        $locale = app()->getLocale();

        /**
         * SEO из CMS.
         */
        $cmsSeoPage = app(
            CmsPageResolverService::class
        )->resolveSeo(
            $request->path()
        );

        $cmsSeoTranslation =
            $cmsSeoPage?->translationOrFallback();

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
                'title' =>
                    __('Бренды товаров'),

                'keywords' => '',
                'description' => '',
            ];

        /**
         * Публичные настройки.
         *
         * PublicSettingsService является
         * единственным источником значений
         * по умолчанию.
         */
        $settings = app(
            PublicSettingsService::class
        );

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int(
                'publicMarketBrandsPerPage',
                12
            )
        );

        $search = $this->resolveSearch(
            $request
        );

        $defaultSort = $settings->string(
            'publicMarketBrandsDefaultSort',
            'sortAsc'
        );

        $sort = $this->resolveSort(
            $request,
            $defaultSort
        );

        $view = $this->resolveView(
            $request,
            $settings->string(
                'publicMarketBrandsDefaultView',
                'grid'
            )
        );

        $processingMode =
            $this->resolveProcessingMode(
                $settings->string(
                    'publicMarketBrandsProcessingMode',
                    'server'
                )
            );

        /**
         * Общее количество
         * публичных брендов.
         */
        $brandsCount =
            MarketBrand::query()
                ->forPublic()
                ->count();

        /**
         * Определяем server/frontend режим.
         */
        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $brandsCount,
            300
        );

        /**
         * Получаем бренды.
         */
        $brands =
            $this->getIndexBrands(
                locale: $locale,
                useServerProcessing:
                $useServerProcessing,
                perPage: $perPage,
                sort: $sort,
                search: $search,
            );

        /**
         * Количество найденных брендов.
         */
        $brandsFound =
            $useServerProcessing
                ? $brands->total()
                : $brands->count();

        /**
         * Компактный Public Resource
         * для Index.
         */
        $brands =
            MarketBrandSharedResource::collection(
                $brands
            );

        /**
         * Дерево категорий
         * для левого сайдбара.
         */
        $categoryTree =
            $this->getMarketCategoryTree(
                $locale
            );

        /**
         * Данные сайдбаров маркетплейса.
         */
        $sidebarData =
            $this->getMarketSidebarData(
                $locale
            );

        return Inertia::render(
            'Public/Default/Market/MarketBrands/Index',
            [
                'seo' => $seo,

                'publicMarketBrandsProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

                'brands' =>
                    $brands,

                'brandsCount' =>
                    $brandsCount,

                'brandsFound' =>
                    $brandsFound,

                'filters' =>
                    $this->buildIndexFilters(
                        $search,
                        $perPage,
                        $sort,
                        $view,
                        $processingMode
                    ),

                /**
                 * Источник истины для
                 * frontend-сортировки.
                 */
                'defaultSort' =>
                    $defaultSort,

                'categoryTree' =>
                    $categoryTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /**
     * Публичная страница бренда.
     */
    public function show(
        Request $request,
        string $url
    ): Response {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        /**
         * Получаем только публичный бренд.
         *
         * Переводы:
         * current + fallback.
         *
         * Изображения:
         * загружаем media заранее,
         * чтобы Resource не создавал N+1.
         */
        $brand =
            MarketBrand::query()
                ->forPublic()
                ->where(
                    'market_brands.url',
                    $url
                )
                ->with([
                    'translations' =>
                        fn ($query) =>
                        $query->whereIn(
                            'locale',
                            $locales
                        ),

                    'images.media',
                ])
                ->withCount([
                    /**
                     * Только публичные товары.
                     */
                    'products as products_count' =>
                        fn ($query) =>
                        $query->forPublic(),

                    /**
                     * Количество изображений.
                     */
                    'images',
                ])
                ->firstOrFail();

        /**
         * Увеличиваем просмотры бренда.
         *
         * increment() обновляет значение
         * в БД и текущей модели.
         */
        $brand->increment('views');

        /**
         * Публичные настройки
         * каталога товаров бренда.
         */
        $settings = app(
            PublicSettingsService::class
        );

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int(
                'publicMarketProductsPerPage',
                12
            )
        );

        $search = $this->resolveSearch(
            $request
        );

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

        $processingMode =
            $this->resolveProcessingMode(
                $settings->string(
                    'publicMarketProductsProcessingMode',
                    'server'
                )
            );

        /**
         * Количество только публичных
         * товаров текущего бренда.
         */
        $productsCount = (int) (
            $brand->products_count ?? 0
        );

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
         * Получаем товары бренда.
         *
         * В server-режиме поиск и сортировка
         * выполняются Laravel.
         *
         * Во frontend-режиме полный публичный
         * набор передаётся Vue.
         */
        $products =
            $this->getBrandProducts(
                brandId: (int) $brand->id,
                locale: $locale,
                useServerProcessing:
                $useServerProcessing,
                perPage: $perPage,
                sort: $sort,
                search: $search,
            );

        /**
         * Количество найденных товаров.
         */
        $productsFound =
            $useServerProcessing
                ? $products->total()
                : $products->count();

        /**
         * Компактный Public Resource
         * товаров бренда.
         */
        $products =
            MarketProductSharedResource::collection(
                $products
            );

        /**
         * Дерево категорий
         * для левого сайдбара.
         */
        $categoryTree =
            $this->getMarketCategoryTree(
                $locale
            );

        /**
         * Данные сайдбаров маркетплейса.
         */
        $sidebarData =
            $this->getMarketSidebarData(
                $locale
            );

        return Inertia::render(
            'Public/Default/Market/MarketBrands/Show',
            [
                'brand' =>
                    new MarketBrandResource(
                        $brand
                    ),

                'products' =>
                    $products,

                'productsCount' =>
                    $productsCount,

                'productsFound' =>
                    $productsFound,

                'publicMarketProductsProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

                'filters' =>
                    $this->buildIndexFilters(
                        $search,
                        $perPage,
                        $sort,
                        $view,
                        $processingMode
                    ),

                /**
                 * Источник истины для
                 * frontend-сортировки товаров.
                 */
                'defaultSort' =>
                    $defaultSort,

                'categoryTree' =>
                    $categoryTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /**
     * Базовый запрос списка
     * публичных брендов.
     */
    private function indexQuery(
        string $locale
    ): Builder {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        return MarketBrand::query()
            ->forPublic()
            ->with([
                /**
                 * Только current + fallback
                 * переводы бренда.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Изображения нужны
                 * MarketBrandSharedResource.
                 *
                 * Используются в Index
                 * и будущей карусели брендов.
                 */
                'images.media',
            ])
            ->withCount([
                /**
                 * Только публичные товары.
                 */
                'products as products_count' =>
                    fn ($query) =>
                    $query->forPublic(),
            ]);
    }

    /**
     * Получение брендов
     * по активному режиму обработки.
     */
    private function getIndexBrands(
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query =
            $this->indexQuery(
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
         * Во frontend режиме отдаём
         * весь публичный набор.
         *
         * Поиск и дальнейшая сортировка
         * выполняются Vue.
         */
        return $query
            ->publicSortByParam(
                $sort,
                $locale
            )
            ->get();
    }

    /**
     * Базовый запрос публичных
     * товаров конкретного бренда.
     */
    private function productsQuery(
        int $brandId,
        string $locale
    ): Builder {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        $query = MarketProduct::query()
            ->forPublic()
            ->where(
                'market_products.market_brand_id',
                $brandId
            )
            ->with([
                /**
                 * Только current + fallback
                 * переводы товара.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Валюта товара.
                 */
                'currency',

                /**
                 * Изображения товара.
                 */
                'images.media',

                /**
                 * Публичный бренд товара.
                 *
                 * MarketProductSharedResource
                 * использует relation brand.
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
                 * Только публичные варианты.
                 */
                'publicVariants',

                /**
                 * Только публичные отзывы.
                 */
                'reviews' =>
                    fn ($query) =>
                    $query->forPublic(),
            ]);

        /**
         * Лайк текущего пользователя.
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
     * Получение товаров бренда
     * по активному режиму обработки.
     */
    private function getBrandProducts(
        int $brandId,
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query =
            $this->productsQuery(
                $brandId,
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
         * Во frontend режиме отдаём
         * весь публичный набор.
         *
         * Поиск и сортировка далее
         * выполняются Vue.
         */
        return $query
            ->publicSortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
