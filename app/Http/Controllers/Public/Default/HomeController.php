<?php

namespace App\Http\Controllers\Public\Default;

use App\Http\Controllers\Controller;
use App\Http\Resources\Public\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Public\Market\MarketCategory\MarketCategorySharedResource;
use App\Http\Resources\Public\Market\MarketProduct\MarketProductSharedResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Market\MarketCategory\MarketCategory;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Traits\Public\Market\HasMarketBrandCarouselDataTrait;
use App\Traits\Public\WithUserLikesTrait;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    use HasMarketBrandCarouselDataTrait;
    use WithUserLikesTrait;

    /**
     * Главная страница.
     */
    public function index(): Response
    {
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

        /* ======================== Market categories ======================== */

        /**
         * Основные категории оборудования
         * для главной страницы.
         */
        $marketCategories = MarketCategory::query()
            ->forPublic()
            ->root()
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
            ])
            ->ordered()
            ->limit(6)
            ->get();

        /* ======================== Market products ======================== */

        /**
         * Популярное оборудование
         * для главной страницы.
         */
        $marketProductsQuery = MarketProduct::query()
            ->forPublic()
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
                 * Только публичный бренд.
                 */
                'brand' =>
                    fn ($query) =>
                    $query
                        ->forPublic()
                        ->with([
                            'translations' =>
                                fn ($translationQuery) =>
                                $translationQuery->whereIn(
                                    'locale',
                                    $locales
                                ),
                        ]),
            ])
            ->withCount([
                /**
                 * Публичные варианты товара.
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
         * Состояние лайка текущего пользователя.
         */
        if (auth()->check()) {
            $marketProductsQuery->withExists([
                'likes as already_liked' =>
                    fn ($likeQuery) =>
                    $likeQuery->where(
                        'user_id',
                        auth()->id()
                    ),
            ]);
        }

        /**
         * Самые просматриваемые Public товары.
         */
        $marketProducts = $marketProductsQuery
            ->publicSortByParam(
                'viewsDesc',
                $locale
            )
            ->limit(8)
            ->get();

        /* ======================== Market brands ======================== */

        /**
         * Универсальная карусель
         * публичных брендов.
         */
        $brandCarouselData =
            $this->getMarketBrandCarouselData(
                $locale
            );

        /* ======================== Blog articles ======================== */

        /**
         * Последние публичные статьи
         * для главной страницы.
         *
         * Загружаем только данные,
         * необходимые ArticleGrid.
         */
        $blogArticlesQuery = BlogArticle::query()
            ->forPublic()
            ->with([
                /**
                 * Только current + fallback
                 * переводы статьи.
                 */
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),

                /**
                 * Автор статьи.
                 */
                'owner',

                /**
                 * Изображения статьи
                 * + Spatie Media.
                 */
                'images.media',
            ])
            ->withCount([
                'likes',
            ]);

        /**
         * Состояние лайка статьи
         * текущим пользователем.
         *
         * Используем тот же Public Trait,
         * что и BlogArticleController.
         */
        $blogArticlesQuery = $this->withUserLike(
            $blogArticlesQuery
        );

        /**
         * Последние Public статьи.
         *
         * dateDesc использует:
         *
         * published_at -> created_at.
         */
        $blogArticles = $blogArticlesQuery
            ->publicSortByParam(
                'dateDesc',
                $locale
            )
            ->limit(3)
            ->get();

        /* ======================== Response ======================== */

        return Inertia::render(
            'Public/Default/Index',
            [
                'marketCategories' =>
                    MarketCategorySharedResource::collection(
                        $marketCategories
                    ),

                'marketProducts' =>
                    MarketProductSharedResource::collection(
                        $marketProducts
                    ),

                'blogArticles' =>
                    BlogArticleSharedResource::collection(
                        $blogArticles
                    ),

                ...$brandCarouselData,
            ]
        );
    }
}
