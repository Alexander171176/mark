<?php

namespace App\Traits\Public;

use App\Services\Public\Blog\BlogSidebarService;

trait HasSidebarDataTrait
{
    /** Получить данные для левой, центральной и правой колонок публичной части. */
    protected function getSidebarData(string $locale): array
    {
        /** @var BlogSidebarService $sidebarService */
        $sidebarService = app(BlogSidebarService::class);

        return $sidebarService->getSidebarData($locale);
    }
}
