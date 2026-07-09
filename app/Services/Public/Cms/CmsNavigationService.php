<?php

namespace App\Services\Public\Cms;

use App\Models\Admin\Cms\CmsPage\CmsPage;
use Illuminate\Support\Collection;

class CmsNavigationService
{
    public function menu(): Collection
    {
        return CmsPage::query()
            ->with([
                'translations',
                'publicMenuChildren',
            ])
            ->withCount(['children'])
            ->forPublic()
            ->inMenu()
            ->root()
            ->ordered()
            ->get();
    }

    public function footer(): Collection
    {
        return CmsPage::query()
            ->with([
                'translations',
                'publicFooterChildren',
            ])
            ->withCount(['children'])
            ->forPublic()
            ->inFooter()
            ->root()
            ->ordered()
            ->get();
    }

    protected function navigationQuery()
    {
        return CmsPage::query()
            ->with([
                'translations',
                'childrenRecursive',
            ])
            ->withCount([
                'children',
            ])
            ->forPublic();
    }
}
