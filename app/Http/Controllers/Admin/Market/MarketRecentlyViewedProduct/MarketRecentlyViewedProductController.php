<?php

namespace App\Http\Controllers\Admin\Market\MarketRecentlyViewedProduct;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Market\MarketRecentlyViewedProduct\MarketRecentlyViewedProductResource;
use App\Models\Admin\Market\MarketProduct\MarketProductTranslation;
use App\Models\Admin\Market\MarketRecentlyViewedProduct\MarketRecentlyViewedProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketRecentlyViewedProductController extends Controller
{

    /**
     * Пользователи, имеющие историю
     * просмотренных товаров маркетплейса.
     */
    public function index(Request $request): Response
    {
        /** Поиск */
        $search = trim(
            (string) $request->query('search', '')
        );

        /** Количество пользователей на странице */
        $perPage = (int) $request->query(
            'per_page',
            20
        );

        $perPage = in_array(
            $perPage,
            [10, 20, 30, 50, 100],
            true
        )
            ? $perPage
            : 20;

        /** Сортировка */
        $sort = (string) $request->query(
            'sort',
            'lastViewedAtDesc'
        );

        /**
         * Пользователи, у которых есть
         * хотя бы один просмотренный товар.
         */
        $usersQuery = User::query()
            ->whereHas('recentlyViewedMarketProducts')
            ->withCount('recentlyViewedMarketProducts')
            ->withMax(
                'recentlyViewedMarketProducts as last_viewed_at',
                'viewed_at'
            );

        /**
         * Поиск пользователя.
         */
        if ($search !== '') {
            $usersQuery->where(function (Builder $query) use ($search) {
                if (is_numeric($search)) {
                    $query->orWhere(
                        'id',
                        (int) $search
                    );
                }

                $query
                    ->orWhere(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        /**
         * Сортировка.
         */
        $this->applyUsersSort(
            query: $usersQuery,
            sort: $sort
        );

        /**
         * Пользователи с пагинацией.
         */
        $users = $usersQuery
            ->paginate($perPage)
            ->withQueryString();

        /**
         * Общее количество пользователей,
         * имеющих историю просмотров.
         */
        $usersCount = User::query()
            ->whereHas('recentlyViewedMarketProducts')
            ->count();

        return Inertia::render(
            'Admin/Market/MarketRecentlyViewedProducts/Index',
            [
                'users' => $users,
                'usersCount' => $usersCount,

                'search' => $search,

                'filters' => [
                    'per_page' => $perPage,
                    'sort' => $sort,
                ],
            ]
        );
    }

    /**
     * История просмотренных товаров конкретного пользователя.
     */
    public function show(
        Request $request,
        User $user
    ): Response {
        $locale = app()->getLocale();

        /** Поисковая строка */
        $search = trim(
            (string) $request->query('search', '')
        );

        /** Количество строк на странице */
        $perPage = (int) $request->query(
            'per_page',
            20
        );

        $perPage = in_array(
            $perPage,
            [10, 20, 30, 50, 100],
            true
        )
            ? $perPage
            : 20;

        /** Сортировка */
        $sort = (string) $request->query(
            'sort',
            'viewedAtDesc'
        );

        /**
         * Общее количество просмотренных
         * пользователем уникальных товаров.
         */
        $historyCount = MarketRecentlyViewedProduct::query()
            ->where('user_id', $user->id)
            ->count();

        /**
         * Последний просмотр товара пользователем.
         */
        $lastViewedAt = MarketRecentlyViewedProduct::query()
            ->where('user_id', $user->id)
            ->max('viewed_at');

        /**
         * Основной запрос истории просмотров.
         */
        $historyQuery = MarketRecentlyViewedProduct::query()
            ->where('user_id', $user->id)
            ->with([
                /**
                 * Сам товар.
                 */
                'product' => fn ($query) => $query
                    ->with([
                        'translations',
                        'images',
                        'currency',

                        'brand' => fn ($brandQuery) => $brandQuery
                            ->with('translations'),

                        'company' => fn ($companyQuery) => $companyQuery
                            ->with('translations'),

                        'shop' => fn ($shopQuery) => $shopQuery
                            ->with('translations'),
                    ])
                    ->withCount([
                        'images',
                        'variants',
                        'reviews',
                        'likes',
                    ]),
            ]);

        /**
         * Поиск по данным товара.
         *
         * Ищем:
         * - ID товара;
         * - URL;
         * - SKU;
         * - артикул;
         * - штрихкод;
         * - название;
         * - подзаголовок;
         * - краткое описание.
         */
        if ($search !== '') {
            $historyQuery->whereHas(
                'product',
                function (Builder $productQuery) use (
                    $search
                ) {
                    $productQuery
                        ->where(function (Builder $query) use ($search) {
                            /**
                             * Поиск по ID.
                             */
                            if (is_numeric($search)) {
                                $query->orWhere(
                                    'id',
                                    (int) $search
                                );
                            }

                            $query
                                ->orWhere(
                                    'url',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'sku',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'vendor_code',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'barcode',
                                    'like',
                                    "%{$search}%"
                                );
                        })
                        ->orWhereHas(
                            'translations',
                            function (Builder $translationQuery) use (
                                $search
                            ) {
                                $translationQuery
                                    ->where(
                                        'title',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'subtitle',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'short',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                }
            );
        }

        /**
         * Применяем сортировку.
         */
        $this->applyHistorySort(
            query: $historyQuery,
            sort: $sort,
            locale: $locale
        );

        /** История с пагинацией */
        $history = $historyQuery
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render(
            'Admin/Market/MarketRecentlyViewedProducts/Show',
            [
                /** Пользователь */
                'user' => [
                    'id' => (int) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                ],

                /** История */
                'history' => MarketRecentlyViewedProductResource::collection(
                    $history
                ),

                /** Общая статистика */
                'historyCount' => $historyCount,
                'lastViewedAt' => $lastViewedAt,

                /** Поиск */
                'search' => $search,

                /** Остальные фильтры */
                'filters' => [
                    'per_page' => $perPage,
                    'sort' => $sort,
                ],

                'locale' => $locale,
            ]
        );
    }

    /**
     * Применить сортировку списка пользователей
     * с историей просмотров.
     */
    private function applyUsersSort(
        Builder $query,
        string $sort
    ): void {
        switch ($sort) {
            case 'idAsc':
                $query->orderBy('id');
                break;

            case 'idDesc':
                $query->orderByDesc('id');
                break;

            case 'nameAsc':
                $query
                    ->orderBy('name')
                    ->orderBy('id');
                break;

            case 'nameDesc':
                $query
                    ->orderByDesc('name')
                    ->orderByDesc('id');
                break;

            case 'emailAsc':
                $query
                    ->orderBy('email')
                    ->orderBy('id');
                break;

            case 'emailDesc':
                $query
                    ->orderByDesc('email')
                    ->orderByDesc('id');
                break;

            case 'viewsCountAsc':
                $query
                    ->orderBy('recently_viewed_market_products_count')
                    ->orderBy('id');
                break;

            case 'viewsCountDesc':
                $query
                    ->orderByDesc('recently_viewed_market_products_count')
                    ->orderByDesc('id');
                break;

            case 'lastViewedAtAsc':
                $query
                    ->orderBy('last_viewed_at')
                    ->orderBy('id');
                break;

            default:
                $query
                    ->orderByDesc('last_viewed_at')
                    ->orderByDesc('id');
                break;
        }
    }

    /**
     * Применить сортировку истории просмотров.
     */
    private function applyHistorySort(
        Builder $query,
        string $sort,
        string $locale
    ): void {
        switch ($sort) {
            /** Последний просмотр */
            case 'viewedAtAsc':
                $query
                    ->orderBy('viewed_at')
                    ->orderBy('id');
                break;

            /** ID записи истории */
            case 'idAsc':
                $query->orderBy('id');
                break;

            case 'idDesc':
                $query->orderByDesc('id');
                break;

            /** ID товара */
            case 'productIdAsc':
                $query
                    ->orderBy('market_product_id')
                    ->orderBy('id');
                break;

            case 'productIdDesc':
                $query
                    ->orderByDesc('market_product_id')
                    ->orderByDesc('id');
                break;

            /** Название товара */
            case 'productTitleAsc':
                $query
                    ->orderBy(
                        MarketProductTranslation::query()
                            ->select('title')
                            ->whereColumn(
                                'market_product_id',
                                'market_recently_viewed_products.market_product_id'
                            )
                            ->where('locale', $locale)
                            ->limit(1)
                    )
                    ->orderByDesc('viewed_at');
                break;

            case 'productTitleDesc':
                $query
                    ->orderByDesc(
                        MarketProductTranslation::query()
                            ->select('title')
                            ->whereColumn(
                                'market_product_id',
                                'market_recently_viewed_products.market_product_id'
                            )
                            ->where('locale', $locale)
                            ->limit(1)
                    )
                    ->orderByDesc('viewed_at');
                break;

            /**
             * По умолчанию:
             * последние просмотренные товары первыми.
             *
             * Сюда также попадает viewedAtDesc.
             */
            default:
                $query
                    ->orderByDesc('viewed_at')
                    ->orderByDesc('id');
                break;
        }
    }

}
