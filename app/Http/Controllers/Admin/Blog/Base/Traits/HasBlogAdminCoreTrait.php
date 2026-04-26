<?php

namespace App\Http\Controllers\Admin\Blog\Base\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasBlogAdminCoreTrait
{
    protected function availableLocales(): array
    {
        return config('app.available_locales', ['ru']);
    }

    protected function baseQuery(): Builder
    {
        $query = $this->modelClass::query();
        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && !$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    protected function normalizeLocale(?string $locale): string
    {
        $availableLocales = $this->availableLocales();
        $fallback = config('app.fallback_locale', 'ru');

        return $locale && in_array($locale, $availableLocales, true)
            ? $locale
            : $fallback;
    }

    protected function normalizeSortParam(?string $sort): string
    {
        return match ($sort) {
            'idAsc' => 'date_asc',
            'idDesc' => 'date_desc',
            'sortAsc' => 'sort_asc',
            'sortDesc' => 'sort_desc',
            'titleAsc', 'nameAsc' => 'title_asc',
            'titleDesc', 'nameDesc' => 'title_desc',
            default => $sort ?: 'sort_asc',
        };
    }
}
