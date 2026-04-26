<?php

namespace App\Http\Controllers\Admin\Blog\Base\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasBlogAdminCoreTrait
{
    /**
     * Доступные локали приложения
     */
    protected function availableLocales(): array
    {
        return config('app.available_locales', ['ru']);
    }

    /**
     * Базовый запрос с учётом прав пользователя
     */
    protected function baseQuery(): Builder
    {
        $query = $this->modelClass::query();
        $user = auth()->user();

        // Если не админ — показываем только свои записи
        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    /**
     * Нормализация локали (если невалидна — fallback)
     */
    protected function normalizeLocale(?string $locale): string
    {
        $availableLocales = $this->availableLocales();
        $fallback = config('app.fallback_locale', 'ru');

        return $locale && in_array($locale, $availableLocales, true)
            ? $locale
            : $fallback;
    }

    /**
     * Нормализация параметра сортировки
     */
    protected function normalizeSortParam(?string $sort): string
    {
        $map = array_merge(
            $this->baseSortMap(),
            $this->extendedSortMap()
        );

        return $map[$sort] ?? ($sort ?: 'sort_asc');
    }

    /**
     * Базовые варианты сортировки (для всех сущностей)
     */
    protected function baseSortMap(): array
    {
        return [
            'idAsc' => 'date_asc',
            'idDesc' => 'date_desc',

            'sortAsc' => 'sort_asc',
            'sortDesc' => 'sort_desc',

            'titleAsc' => 'title_asc',
            'titleDesc' => 'title_desc',

            'nameAsc' => 'name_asc',
            'nameDesc' => 'name_desc',
        ];
    }

    /**
     * Расширение сортировки (переопределяется в контроллерах)
     */
    protected function extendedSortMap(): array
    {
        return [];
    }
}
