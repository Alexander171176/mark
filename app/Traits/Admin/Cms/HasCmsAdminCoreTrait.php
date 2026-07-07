<?php

namespace App\Traits\Admin\Cms;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasCmsAdminCoreTrait
{
    protected function availableLocales(): array
    {
        return config('app.available_locales', []);
    }

    protected function baseQuery(): Builder
    {
        $query = $this->modelClass::query();

        $user = auth()->user();

        if ($user && method_exists($user, 'hasRole') && ! $user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    protected function resolveLocale(Request $request): string
    {
        $locale = $request->route('locale')
            ?? $request->query('locale')
            ?? app()->getLocale();

        $locale = $this->normalizeLocale($locale);

        app()->setLocale($locale);

        return $locale;
    }

    protected function normalizeLocale(?string $locale): string
    {
        $availableLocales = $this->availableLocales();
        $fallback = config('app.fallback_locale', 'ru');

        return $locale && in_array($locale, $availableLocales, true)
            ? $locale
            : $fallback;
    }
}
