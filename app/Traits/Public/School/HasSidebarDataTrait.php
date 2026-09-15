<?php

namespace App\Traits\Public\School;

use App\Services\Public\School\SchoolSidebarService;

trait HasSidebarDataTrait
{
    /**
     * Получить данные для левой,
     * центральной и правой колонок
     * публичной части школы.
     */
    protected function getSidebarData(
        string $locale
    ): array {
        /** @var SchoolSidebarService $sidebarService */
        $sidebarService =
            app(SchoolSidebarService::class);

        return $sidebarService
            ->getSidebarData($locale);
    }
}
