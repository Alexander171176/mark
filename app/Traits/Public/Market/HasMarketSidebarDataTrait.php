<?php

namespace App\Traits\Public\Market;

use App\Services\Public\Market\MarketSidebarService;

trait HasMarketSidebarDataTrait
{
    /** Получить данные для сайдбаров публичного маркетплейса. */
    protected function getMarketSidebarData(string $locale): array
    {
        /** @var MarketSidebarService $sidebarService */
        $sidebarService = app(MarketSidebarService::class);

        return $sidebarService->getSidebarData($locale);
    }
}
