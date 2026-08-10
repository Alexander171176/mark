<?php

namespace App\Http\Controllers\Public\Default\Market\MarketProduct;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Market\MarketCategory\MarketCategorySharedResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductResource;
use App\Http\Resources\Admin\Market\MarketProduct\MarketProductSharedResource;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Services\Admin\ProcessingModeService;
use App\Services\Public\Cms\CmsPageResolverService;
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
    use WithUserLikesTrait;
    use HasPublicIndexFiltersTrait;
    use BuildsMarketCategoryTreeTrait;
    use HasMarketSidebarDataTrait;

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

        return Inertia::render('Public/Default/Market/MarketProducts/Show', [
            'product' => $productData,

            'breadcrumbCategory' => $breadcrumbCategory
                ? (new MarketCategorySharedResource(
                    $breadcrumbCategory
                ))->resolve()
                : null,

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
