<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheTimeTrait
{
    protected function getCacheTime(): int
    {
        return config('cache.default_cache_time', 600);
    }

    /**
     * Очистка кэша по ключам.
     *
     * @param array|null $keys
     * @return void
     */
    protected function clearCache(array $keys = null): void
    {
        if ($keys) {
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        } else {
            Cache::flush();
        }
    }
}
