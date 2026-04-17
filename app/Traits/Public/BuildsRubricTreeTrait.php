<?php

namespace App\Traits\Public;

use App\Services\Public\Blog\RubricTreeService;

trait BuildsRubricTreeTrait
{
    /**
     * Получить дерево рубрик для публичной части.
     *
     * @param string $locale
     * @return array
     */
    protected function getRubricTree(string $locale): array
    {
        /** @var RubricTreeService $rubricTreeService */
        $rubricTreeService = app(RubricTreeService::class);

        return $rubricTreeService->getTree($locale);
    }
}
