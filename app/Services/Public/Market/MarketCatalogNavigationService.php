<?php

namespace App\Services\Public\Market;

use App\Models\Admin\Market\MarketCategory\MarketCategory;
use Illuminate\Support\Collection;

class MarketCatalogNavigationService
{
    public function catalog(): Collection
    {
        return MarketCategory::query()
            ->with([
                'translations',
                'publicCatalogChildren',
            ])
            ->withCount([
                'children',
            ])
            ->forMenu()
            ->root()
            ->ordered()
            ->get();
    }
}
