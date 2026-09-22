<?php

namespace App\Services\Public\Market;

use App\Http\Resources\Public\Market\MarketBrand\MarketBrandSharedResource;
use App\Models\Admin\Market\MarketBrand\MarketBrand;
use App\Services\SiteSettings\PublicSettingsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MarketBrandCarouselService
{
    /** Время жизни кэша карусели брендов. */
    protected int $ttl = 600;

    /**
     * Получить данные карусели брендов.
     */
    public function getCarouselData(
        string $locale
    ): array {
        if (! $this->isEnabled()) {
            return [
                'marketBrandCarousel' => [],
            ];
        }

        return [
            'marketBrandCarousel' =>
                MarketBrandSharedResource::collection(
                    $this->getBrands(
                        $locale
                    )
                ),
        ];
    }

    /**
     * Проверить, включена ли
     * карусель брендов.
     */
    public function isEnabled(): bool
    {
        return app(
            PublicSettingsService::class
        )->bool(
            'publicMarketBrandCarouselEnabled',
            true
        );
    }

    /**
     * Получить публичные бренды
     * с учётом кэша.
     */
    protected function getBrands(
        string $locale
    ): Collection {
        return Cache::remember(
            $this->getCacheKey($locale),
            $this->ttl,
            fn () => $this->buildBrands(
                $locale
            )
        );
    }

    /**
     * Собрать публичные бренды.
     *
     * left / main / right здесь
     * намеренно не используются.
     */
    protected function buildBrands(
        string $locale
    ): Collection {
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

        return MarketBrand::query()
            ->forPublic()
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
                'products' =>
                    fn ($query) =>
                    $query->forPublic(),
            ])
            ->orderBy(
                'market_brands.sort'
            )
            ->orderBy(
                'market_brands.id'
            )
            ->get();
    }

    /**
     * Очистить кэш одной локали.
     */
    public function forget(
        string $locale
    ): void {
        Cache::forget(
            $this->getCacheKey(
                $locale
            )
        );
    }

    /**
     * Очистить кэш всех локалей.
     */
    public function forgetAll(): void
    {
        foreach (
            config(
                'app.available_locales',
                []
            ) as $locale
        ) {
            $this->forget(
                $locale
            );
        }
    }

    /**
     * Ключ кэша карусели брендов.
     */
    protected function getCacheKey(
        string $locale
    ): string {
        return "market_brand_carousel_{$locale}";
    }
}
