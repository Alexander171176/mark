<?php

namespace App\Services\Public\Market;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketRecentlyViewedProduct\MarketRecentlyViewedProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MarketRecentlyViewedProductService
{
    /**
     * Максимальное количество просмотренных товаров,
     * которое храним для одного пользователя.
     */
    protected int $historyLimit = 20;

    /**
     * Количество товаров,
     * которое обычно показываем пользователю.
     */
    protected int $displayLimit = 12;

    /**
     * Запомнить просмотр товара авторизованным пользователем.
     */
    public function remember(int $userId, int $productId): void
    {
        DB::transaction(function () use ($userId, $productId) {
            MarketRecentlyViewedProduct::query()
                ->updateOrCreate(
                    [
                        'user_id' => $userId,
                        'market_product_id' => $productId,
                    ],
                    [
                        'viewed_at' => now(),
                    ]
                );

            $this->trimHistory($userId);
        });
    }

    /**
     * Получить последние просмотренные товары пользователя.
     *
     * Текущий товар можно исключить из результата,
     * чтобы он не отображался на своей же странице.
     */
    public function getProducts(
        int $userId,
        ?int $excludeProductId = null,
        ?int $limit = null,
        ?string $locale = null
    ): Collection {
        $locale = $locale ?: app()->getLocale();
        $limit = $limit ?: $this->displayLimit;

        return MarketProduct::query()
            ->forPublic()
            ->whereHas(
                'recentlyViewedByUsers',
                function ($query) use ($userId) {
                    $query->where(
                        'user_id',
                        $userId
                    );
                }
            )
            ->when(
                $excludeProductId,
                fn ($query) => $query->where(
                    'market_products.id',
                    '!=',
                    $excludeProductId
                )
            )
            ->with([
                'translations',
                'images',
                'currency',

                'company.translations',
                'shop.translations',
                'brand.translations',
            ])
            ->withCount([
                'likes',
                'reviews',
                'variants',
                'images',
            ])
            ->withExists([
                'likes as already_liked' => fn ($query) => $query
                    ->where('user_id', $userId),
            ])
            ->orderByDesc(
                MarketRecentlyViewedProduct::query()
                    ->select('viewed_at')
                    ->whereColumn(
                        'market_product_id',
                        'market_products.id'
                    )
                    ->where(
                        'user_id',
                        $userId
                    )
                    ->limit(1)
            )
            ->limit($limit)
            ->get();
    }

    /**
     * Получить записи истории пользователя.
     *
     * Метод пригодится для административной части.
     */
    public function getHistory(
        int $userId,
        ?int $limit = null
    ): Collection {
        $query = MarketRecentlyViewedProduct::query()
            ->where(
                'user_id',
                $userId
            )
            ->with([
                'product.translations',
                'product.images',
                'product.currency',
            ])
            ->orderByDesc('viewed_at');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Объединить гостевую историю с историей пользователя.
     *
     * ID должны приходить в порядке:
     * от самого нового просмотра к самому старому.
     */
    public function mergeGuestHistory(
        int $userId,
        array $productIds
    ): void {
        $productIds = collect($productIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->take($this->historyLimit)
            ->values();

        if ($productIds->isEmpty()) {
            return;
        }

        /**
         * Не переносим в историю удалённые
         * или несуществующие товары.
         */
        $existingProductIds = MarketProduct::query()
            ->whereIn(
                'id',
                $productIds
            )
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        if ($existingProductIds->isEmpty()) {
            return;
        }

        DB::transaction(function () use (
            $userId,
            $productIds,
            $existingProductIds
        ) {
            $now = now();

            foreach ($productIds as $position => $productId) {
                if (!$existingProductIds->contains($productId)) {
                    continue;
                }

                /**
                 * Восстанавливаем относительный порядок
                 * гостевой истории.
                 *
                 * Первый ID — самый свежий.
                 */
                $viewedAt = $now->copy()->subSeconds($position);

                $existing = MarketRecentlyViewedProduct::query()
                    ->where('user_id', $userId)
                    ->where(
                        'market_product_id',
                        $productId
                    )
                    ->first();

                /**
                 * Если в БД уже есть более свежий просмотр,
                 * не перезаписываем его старой гостевой датой.
                 */
                if (
                    $existing
                    && $existing->viewed_at
                    && $existing->viewed_at->greaterThan($viewedAt)
                ) {
                    continue;
                }

                MarketRecentlyViewedProduct::query()
                    ->updateOrCreate(
                        [
                            'user_id' => $userId,
                            'market_product_id' => $productId,
                        ],
                        [
                            'viewed_at' => $viewedAt,
                        ]
                    );
            }

            $this->trimHistory($userId);
        });
    }

    /**
     * Очистить историю просмотренных товаров пользователя.
     */
    public function clear(int $userId): void
    {
        MarketRecentlyViewedProduct::query()
            ->where(
                'user_id',
                $userId
            )
            ->delete();
    }

    /**
     * Ограничить размер истории пользователя.
     *
     * Старые записи удаляются.
     */
    protected function trimHistory(int $userId): void
    {
        $idsToKeep = MarketRecentlyViewedProduct::query()
            ->where(
                'user_id',
                $userId
            )
            ->orderByDesc('viewed_at')
            ->orderByDesc('id')
            ->limit($this->historyLimit)
            ->pluck('id');

        MarketRecentlyViewedProduct::query()
            ->where(
                'user_id',
                $userId
            )
            ->when(
                $idsToKeep->isNotEmpty(),
                fn ($query) => $query->whereNotIn(
                    'id',
                    $idsToKeep
                )
            )
            ->delete();
    }
}
