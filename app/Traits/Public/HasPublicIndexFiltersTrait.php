<?php

namespace App\Traits\Public;

use Illuminate\Http\Request;

trait HasPublicIndexFiltersTrait
{
    /**
     * Определяет perPage с ограничениями.
     */
    protected function resolvePerPage(
        Request $request,
        int $default = 6,
        int $min = 3,
        int $max = 60
    ): int {
        $perPage = (int) $request->integer('per_page', $default);

        return max($min, min($perPage, $max));
    }

    /**
     * Получает поисковую строку.
     */
    protected function resolveSearch(Request $request, string $key = 'q'): string
    {
        return trim((string) $request->query($key, ''));
    }

    /**
     * Получает сортировку.
     */
    protected function resolveSort(Request $request, string $default = 'sort_asc'): string
    {
        return (string) $request->query('sort', $default);
    }

    /**
     * Формирует массив фильтров для фронта.
     */
    protected function buildIndexFilters(
        string $search,
        int $perPage,
        string $sort
    ): array {
        return [
            'q' => $search,
            'per_page' => $perPage,
            'sort' => $sort,
        ];
    }
}
