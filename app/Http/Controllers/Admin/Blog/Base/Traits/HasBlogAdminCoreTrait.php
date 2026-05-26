<?php

namespace App\Http\Controllers\Admin\Blog\Base\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasBlogAdminCoreTrait
{
    /** Доступные локали приложения */
    protected function availableLocales(): array
    {
        return config('app.available_locales', []);
    }

    /** Базовый запрос с учётом прав пользователя */
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

    /** Определение локали */
    protected function resolveLocale(Request $request): string
    {
        $locale = $request->route('locale')
            ?? app()->getLocale()
            ?? $request->query('locale');

        $locale = $this->normalizeLocale($locale);

        app()->setLocale($locale);

        return $locale;
    }

    /** Нормализация локали (если невалидна — fallback) */
    protected function normalizeLocale(?string $locale): string
    {
        $availableLocales = $this->availableLocales();
        $fallback = config('app.fallback_locale');

        return $locale && in_array($locale, $availableLocales, true)
            ? $locale
            : $fallback;
    }

    /** Нормализация параметра сортировки */
    protected function normalizeSortParam(?string $sort): string
    {
        $map = array_merge(
            $this->baseSortMap(),
            $this->extendedSortMap()
        );

        return $map[$sort] ?? ($sort ?: 'sort_asc');
    }

    /** Базовые варианты сортировки (для всех сущностей) */
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

    /** Расширение сортировки (переопределяется в контроллерах) */
    protected function extendedSortMap(): array
    {
        return [];
    }
}
