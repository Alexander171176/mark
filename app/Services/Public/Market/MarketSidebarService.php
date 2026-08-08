<?php

namespace App\Services\Public\Market;

use App\Http\Resources\Admin\Market\MarketTag\MarketTagSharedResource;
use App\Models\Admin\Market\MarketTag\MarketTag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MarketSidebarService
{
    /** Время жизни кэша sidebar-данных. */
    protected int $ttl = 600;

    /** Получить данные для публичных сайдбаров маркетплейса. */
    public function getSidebarData(string $locale): array
    {
        return Cache::remember(
            $this->getCacheKey($locale),
            $this->ttl,
            fn () => $this->buildSidebarData($locale)
        );
    }

    /** Очистить кэш одной локали. */
    public function forget(string $locale): void
    {
        Cache::forget($this->getCacheKey($locale));
    }

    /** Очистить кэш всех локалей. */
    public function forgetAll(): void
    {
        foreach (config('app.available_locales', []) as $locale) {
            $this->forget($locale);
        }
    }

    /** Собрать данные для сайдбаров маркетплейса. */
    protected function buildSidebarData(string $locale): array
    {
        return [
            'tags' => MarketTagSharedResource::collection(
                $this->getTags($locale)
            ),
        ];
    }

    /** Облако тегов маркетплейса. */
    protected function getTags(string $locale): Collection
    {
        return MarketTag::query()
            ->forPublic()
            ->with('translations')
            ->withCount('products')
            ->sortByParam('sortAsc', $locale)
            ->get();
    }

    /** Ключ кэша sidebar-данных. */
    protected function getCacheKey(string $locale): string
    {
        return "market_sidebar_data_{$locale}";
    }
}
