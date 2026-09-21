<?php

namespace App\Http\Controllers\Public\Default\Market\MarketTag;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Market\MarketProduct\MarketProductSharedResource;
use App\Http\Resources\Public\Market\MarketTag\MarketTagResource;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketTag\MarketTag;
use App\Services\Admin\ProcessingModeService;
use App\Services\SiteSettings\PublicSettingsService;
use App\Traits\Public\HasPublicIndexFiltersTrait;
use App\Traits\Public\Market\BuildsMarketCategoryTreeTrait;
use App\Traits\Public\Market\HasMarketSidebarDataTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketTagController extends Controller
{
    use HasPublicIndexFiltersTrait;
    use BuildsMarketCategoryTreeTrait;
    use HasMarketSidebarDataTrait;

    /** Страница конкретного тега маркетплейса */
    public function show(
        Request $request,
        string $url
    ): Response {
        $locale = app()->getLocale();

        $locales = $this->publicLocales(
            $locale
        );

        /**
         * Получаем только публичный тег.
         *
         * Переводы:
         * current + configured fallback.
         */
        $tag = MarketTag::query()
            ->forPublic()
            ->where(
                'market_tags.url',
                $url
            )
            ->with([
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),
            ])
            ->withCount([
                /**
                 * Считаем только Public-товары.
                 */
                'products' =>
                    fn ($query) =>
                    $query->forPublic(),
            ])
            ->firstOrFail();

        /**
         * Увеличиваем просмотры тега.
         */
        $tag->increment('views');

        /**
         * Публичные настройки товаров.
         *
         * Товары тега используют тот же контракт,
         * что и общий Product Index.
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

        /**
         * Поисковая строка товаров тега.
         */
        $search = trim(
            (string) $request->input(
                'q',
                ''
            )
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
         * Количество Public-товаров
         * именно этого тега.
         */
        $productsCount = (int) (
            $tag->products_count ?? 0
        );

        /**
         * Определяем server/frontend режим
         * по количеству товаров текущего тега,
         * а не всего маркетплейса.
         */
        $useServerProcessing = app(
            ProcessingModeService::class
        )->shouldUseServer(
            $processingMode,
            $productsCount,
            300
        );

        /**
         * Получаем связанные с тегом товары.
         */
        $products = $this->getTagProducts(
            tagId: (int) $tag->id,
            locale: $locale,
            useServerProcessing: $useServerProcessing,
            perPage: $perPage,
            search: $search,
            sort: $sort,
        );

        /**
         * Количество найденных товаров
         * после применения поиска.
         */
        $productsFound = $useServerProcessing
            ? $products->total()
            : $products->count();

        /**
         * Тот же карточный контракт,
         * что и Product Index.
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
            'Public/Default/Market/MarketTags/Show',
            [
                'tag' =>
                    new MarketTagResource(
                        $tag
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

                'defaultSort' =>
                    $defaultSort,

                'filters' =>
                    $this->buildIndexFilters(
                        $search,
                        $perPage,
                        $sort,
                        $view,
                        $processingMode
                    ),

                'categoryTree' =>
                    $categoryTree,

                'locale' =>
                    $locale,

                ...$sidebarData,
            ]
        );
    }

    /**
     * Базовый запрос Public-товаров тега.
     *
     * Контракт повторяет Product Index,
     * но набор ограничивается текущим тегом.
     */
    private function productsQuery(
        int $tagId,
        string $locale
    ): Builder {
        $locales = $this->publicLocales(
            $locale
        );

        $query = MarketProduct::query()
            ->forPublic()
            ->whereHas(
                'tags',
                fn (Builder $tagQuery) =>
                $tagQuery->where(
                    'market_tags.id',
                    $tagId
                )
            )
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
                 * Public-бренд.
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
                'publicVariants',

                'reviews' =>
                    fn ($query) =>
                    $query->forPublic(),
            ]);

        /**
         * Состояние лайка
         * текущего пользователя.
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
     * Получить товары тега
     * по активному режиму обработки.
     */
    private function getTagProducts(
        int $tagId,
        string $locale,
        bool $useServerProcessing,
        int $perPage,
        string $search,
        string $sort
    ) {
        $query = $this->productsQuery(
            $tagId,
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
