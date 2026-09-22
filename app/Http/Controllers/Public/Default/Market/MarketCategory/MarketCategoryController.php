<?php

namespace App\Http\Controllers\Public\Default\Market\MarketCategory;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Market\MarketCategory\MarketCategoryResource;
use App\Http\Resources\Public\Market\MarketCategory\MarketCategorySharedResource;
use App\Http\Resources\Public\Market\MarketProduct\MarketProductSharedResource;
use App\Models\Admin\Market\MarketCategory\MarketCategory;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\Market\BuildsMarketCategoryTreeTrait;
use App\Traits\Public\Market\HasMarketSidebarDataTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketCategoryController extends Controller
{
    use HasPublicIndexFiltersTrait;
    use BuildsMarketCategoryTreeTrait;
    use HasMarketSidebarDataTrait;

    /**
     * Страница списка категорий маркетплейса.
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
                    __('Категории товаров'),

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
                'publicMarketCategoriesPerPage',
                12
            )
        );

        $search = $this->resolveSearch(
            $request
        );

        $defaultSort = $settings->string(
            'publicMarketCategoriesDefaultSort',
            'sortAsc'
        );

        $sort = $this->resolveSort(
            $request,
            $defaultSort
        );

        $view = $this->resolveView(
            $request,
            $settings->string(
                'publicMarketCategoriesDefaultView',
                'grid'
            )
        );

        $processingMode =
            $this->resolveProcessingMode(
                $settings->string(
                    'publicMarketCategoriesProcessingMode',
                    'server'
                )
            );

        /**
         * Общее количество
         * публичных категорий.
         */
        $categoriesCount =
            MarketCategory::query()
                ->forPublic()
                ->count();

        /**
         * Определяем server/frontend режим.
         */
        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $categoriesCount,
            300
        );

        /**
         * Получаем категории.
         */
        $categories =
            $this->getIndexCategories(
                locale: $locale,
                useServerProcessing:
                $useServerProcessing,
                perPage: $perPage,
                sort: $sort,
                search: $search,
            );

        /**
         * Количество найденных категорий.
         */
        $categoriesFound =
            $useServerProcessing
                ? $categories->total()
                : $categories->count();

        /**
         * Компактный Public Resource
         * для Index.
         */
        $categories =
            MarketCategorySharedResource::collection(
                $categories
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
            'Public/Default/Market/MarketCategories/Index',
            [
                'seo' => $seo,

                'publicMarketCategoriesProcessingMode' =>
                    $processingMode,

                'useServerProcessing' =>
                    $useServerProcessing,

                'categories' =>
                    $categories,

                'categoriesCount' =>
                    $categoriesCount,

                'categoriesFound' =>
                    $categoriesFound,

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
     * Категории для публичного
     * меню маркетплейса.
     */
    public function menuCategories(): JsonResponse
    {
        $locale = app()->getLocale();

        return response()->json([
            'categories' =>
                $this->getMarketCategoryTree(
                    $locale
                ),
        ]);
    }

    /**
     * Страница конкретной
     * категории маркетплейса.
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
         * Получаем только публичную категорию.
         */
        $category =
            MarketCategory::query()
                ->forPublic()
                ->where(
                    'market_categories.url',
                    $url
                )
                ->with([
                    /**
                     * Переводы самой категории:
                     * current + fallback.
                     */
                    'translations' =>
                        fn ($query) =>
                        $query->whereIn(
                            'locale',
                            $locales
                        ),

                    /**
                     * Изображения категории.
                     */
                    'images.media',

                    /**
                     * Родительская категория.
                     */
                    'parent' =>
                        fn ($query) =>
                        $query
                            ->forPublic()
                            ->with([
                                'translations' =>
                                    fn ($query) =>
                                    $query->whereIn(
                                        'locale',
                                        $locales
                                    ),

                                'images.media',
                            ]),

                    /**
                     * Публичные дочерние
                     * категории каталога.
                     */
                    'publicCatalogChildren' =>
                        fn ($query) =>
                        $query
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
                                 * Только публичные
                                 * дочерние категории.
                                 */
                                'children as children_count' =>
                                    fn ($query) =>
                                    $query->forPublic(),

                                /**
                                 * Только публичные товары.
                                 */
                                'products as products_count' =>
                                    fn ($query) =>
                                    $query->forPublic(),

                                'images',
                            ]),
                ])
                ->withCount([
                    /**
                     * Только публичные
                     * дочерние категории.
                     */
                    'children as children_count' =>
                        fn ($query) =>
                        $query->forPublic(),

                    /**
                     * Только публичные товары.
                     */
                    'products as products_count' =>
                        fn ($query) =>
                        $query->forPublic(),

                    'images',
                ])
                ->firstOrFail();

        /**
         * Увеличиваем просмотры категории.
         *
         * increment() обновляет значение
         * в БД и текущей модели.
         */
        $category->increment('views');

        /**
         * Публичные настройки
         * каталога товаров категории.
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
         * товаров текущей категории.
         */
        $productsCount = (int) (
            $category->products_count ?? 0
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
         * Получаем товары категории.
         *
         * В server-режиме поиск и сортировка
         * выполняются Laravel.
         *
         * Во frontend-режиме полный публичный
         * набор передаётся Vue.
         */
        $products =
            $this->getCategoryProducts(
                categoryId: (int) $category->id,
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
         * товаров категории.
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
            'Public/Default/Market/MarketCategories/Show',
            [
                'category' =>
                    new MarketCategoryResource(
                        $category
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
     * публичных категорий.
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

        return MarketCategory::query()
            ->forPublic()
            ->with([
                /**
                 * Только current + fallback
                 * переводы категории.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Изображения категории.
                 */
                'images.media',

                /**
                 * Родительская категория
                 * нужна для Public-поиска.
                 */
                'parent' =>
                    fn ($query) =>
                    $query
                        ->forPublic()
                        ->with([
                            'translations' =>
                                fn ($query) =>
                                $query->whereIn(
                                    'locale',
                                    $locales
                                ),
                        ]),
            ])
            ->withCount([
                /**
                 * Только публичные
                 * дочерние категории.
                 */
                'children as children_count' =>
                    fn ($query) =>
                    $query->forPublic(),

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
            ]);
    }

    /**
     * Базовый запрос публичных
     * товаров конкретной категории.
     */
    private function productsQuery(
        int $categoryId,
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

            /**
             * Только товары,
             * связанные с текущей категорией.
             */
            ->whereHas(
                'categories',
                fn (Builder $categoryQuery) =>
                $categoryQuery->where(
                    'market_categories.id',
                    $categoryId
                )
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
     * Получение товаров категории
     * по активному режиму обработки.
     */
    private function getCategoryProducts(
        int $categoryId,
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $sort,
        string $search = ''
    ) {
        $query =
            $this->productsQuery(
                $categoryId,
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

    /**
     * Получение категорий
     * по активному режиму обработки.
     */
    private function getIndexCategories(
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

        return $query
            ->publicSortByParam(
                $sort,
                $locale
            )
            ->get();
    }
}
