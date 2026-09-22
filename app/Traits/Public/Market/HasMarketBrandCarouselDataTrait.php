<?php

namespace App\Traits\Public\Market;

use App\Services\Public\Market\MarketBrandCarouselService;

trait HasMarketBrandCarouselDataTrait
{
    /** Получить данные карусели брендов публичного маркетплейса. */
    protected function getMarketBrandCarouselData(
        string $locale
    ): array {
        /** @var MarketBrandCarouselService $carouselService */
        $carouselService = app(
            MarketBrandCarouselService::class
        );

        return $carouselService->getCarouselData(
            $locale
        );
    }
}
