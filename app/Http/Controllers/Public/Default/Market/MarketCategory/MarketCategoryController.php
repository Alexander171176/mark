<?php

namespace App\Http\Controllers\Public\Default\Market\MarketCategory;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Market\MarketCategory\MarketCategoryResource;
use App\Http\Resources\Admin\Market\MarketCategory\MarketCategorySharedResource;
use App\Models\Admin\Market\MarketCategory\MarketCategory;
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

    /** Страница списка категорий маркетплейса. */
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
                'title' => __('Категории товаров'),
                'keywords' => '',
                'description' => '',
            ];

        /** Публичные настройки */
        $settings = app(PublicSettingsService::class);

        $perPage = $this->resolvePerPage(
            $request,
            $settings->int('publicMarketCategoriesPerPage', 12)
        );

        $search = $this->resolveSearch($request);

        $sort = $this->resolveSort(
            $request,
            $settings->string('publicMarketCategoriesDefaultSort', 'sortAsc')
        );

        $view = $this->resolveView(
            $request,
            $settings->string('publicMarketCategoriesDefaultView', 'grid')
        );

        $processingMode = $this->resolveProcessingMode(
            $settings->string('publicMarketCategoriesProcessingMode', 'server')
        );

        /** Общее количество публичных категорий */
        $categoriesCount = MarketCategory::query()
            ->forPublic()
            ->count();

        /** Определяем server/frontend режим */
        $useServerProcessing = app(ProcessingModeService::class)
            ->shouldUseServer(
                $processingMode,
                $categoriesCount,
                300
            );

        /** Получаем категории */
        $categories = $this->getIndexCategories(
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            sort: $sort,
            search: $search,
        );

        /** Количество найденных категорий */
        $categoriesFound = $useServerProcessing
            ? $categories->total()
            : $categories->count();

        /** Лёгкий ресурс для списка */
        $categories = MarketCategorySharedResource::collection($categories);

        /** Дерево категорий для левого сайдбара */
        $categoryTree = $this->getMarketCategoryTree($locale);

        /** Данные сайдбаров маркетплейса */
        $sidebarData = $this->getMarketSidebarData($locale);

        return Inertia::render('Public/Default/Market/MarketCategories/Index', [
            'seo' => $seo,

            'publicMarketCategoriesProcessingMode' => $processingMode,
            'useServerProcessing' => $useServerProcessing,

            'categories' => $categories,

            'categoriesCount' => $categoriesCount,
            'categoriesFound' => $categoriesFound,

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

    /** Категории для публичного меню маркетплейса. */
    public function menuCategories(): JsonResponse
    {
        $locale = app()->getLocale();

        return response()->json([
            'categories' => $this->getMarketCategoryTree($locale),
        ]);
    }

    /** Страница конкретной категории маркетплейса. */
    public function show(Request $request, string $url): Response
    {
        $locale = app()->getLocale();

        /** Получаем публичную категорию */
        $category = MarketCategory::query()
            ->forPublic()
            ->where('url', $url)
            ->with([
                'translations',
                'owner',
                'images',

                'parent' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations',
                        'images',
                    ]),

                'children' => fn ($query) => $query
                    ->forPublic()
                    ->with([
                        'translations',
                        'images',
                    ])
                    ->withCount([
                        'children',
                        'products',
                        'images',
                    ])
                    ->ordered(),
            ])
            ->withCount([
                'children',
                'products',
                'images',
            ])
            ->firstOrFail();

        /** Увеличиваем просмотры категории */
        $category->increment('views');

        /** Дерево категорий для левого сайдбара */
        $categoryTree = $this->getMarketCategoryTree($locale);

        /** Данные сайдбаров маркетплейса */
        $sidebarData = $this->getMarketSidebarData($locale);

        return Inertia::render('Public/Default/Market/MarketCategories/Show', [
            'category' => new MarketCategoryResource($category),

            'categoryTree' => $categoryTree,
            'locale' => $locale,

            ...$sidebarData,
        ]);
    }

    /** Базовый запрос списка публичных категорий. */
    private function indexQuery(): Builder
    {
        return MarketCategory::query()
            ->forPublic()
            ->with([
                'translations',
                'images',

                'parent' => fn ($query) => $query
                    ->forPublic()
                    ->with('translations'),
            ])
            ->withCount([
                'children',
                'products',
                'images',
            ]);
    }

    /** Получение категорий по активному режиму обработки. */
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
            ->sortByParam($sort, $locale)
            ->get();
    }
}
