<?php

namespace App\Traits\Public\Market;

use App\Services\Public\Market\MarketCategoryTreeService;

trait BuildsMarketCategoryTreeTrait
{
    /** Получить дерево категорий публичного маркетплейса. */
    protected function getMarketCategoryTree(string $locale): array
    {
        /** @var MarketCategoryTreeService $categoryTreeService */
        $categoryTreeService = app(MarketCategoryTreeService::class);

        return $categoryTreeService->getTree($locale);
    }
}
