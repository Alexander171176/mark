<?php

namespace App\Traits\Public;

use Illuminate\Http\Request;

trait HasPublicIndexFiltersTrait
{
    /** Определяет количество элементов на странице с ограничениями. */
    protected function resolvePerPage(
        Request $request,
        int $default = 6,
        int $min = 3,
        int $max = 60
    ): int {
        $perPage = (int) $request->integer('per_page', $default);

        return max($min, min($perPage, $max));
    }

    /** Получает поисковую строку. */
    protected function resolveSearch(Request $request, string $key = 'q'): string
    {
        return trim((string) $request->query($key, ''));
    }

    /** Получает параметр сортировки. */
    protected function resolveSort(Request $request, string $default = 'sortAsc'): string
    {
        return trim((string) $request->query('sort', $default));
    }

    /** Получает режим отображения списка. */
    protected function resolveView(Request $request, string $default = 'grid'): string
    {
        return trim((string) $request->query('view', $default));
    }

    /** Получает режим обработки данных: server/frontend/auto. */
    protected function resolveProcessingMode(string $default = 'server'): string
    {
        return in_array($default, ['server', 'frontend', 'auto'], true)
            ? $default
            : 'server';
    }

    /** Формирует массив фильтров для передачи на фронт. */
    protected function buildIndexFilters(
        string $search,
        int $perPage,
        string $sort,
        string $view = 'grid',
        string $processingMode = 'server'
    ): array {
        return [
            'q' => $search,
            'per_page' => $perPage,
            'sort' => $sort,
            'view' => $view,
            'processing_mode' => $processingMode,
        ];
    }
}
