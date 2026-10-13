<?php

namespace App\Traits\Admin\Slider;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasSliderAdminCoreTrait
{
    protected function availableLocales(): array
    {
        return array_values(array_unique(array_filter(
            (array) config('app.available_locales', []),
            fn ($locale) => is_string($locale) && $locale !== ''
        )));
    }

    protected function isAdmin(): bool
    {
        return (bool) auth()->user()?->hasRole('admin');
    }

    /**
     * Для дочерних моделей переопределить метод и ограничить доступ через slider.user_id.
     */
    protected function baseQuery(): Builder
    {
        $user = auth()->user();
        abort_unless($user, 403);

        $query = $this->modelClass::query();

        if (! $this->isAdmin()) {
            $query->where($query->getModel()->qualifyColumn('user_id'), $user->getAuthIdentifier());
        }

        return $query;
    }

    protected function normalizeLocale(?string $locale): string
    {
        $available = $this->availableLocales();
        $fallback = (string) config('app.fallback_locale', 'ru');

        if ($locale !== null && in_array($locale, $available, true)) {
            return $locale;
        }

        return in_array($fallback, $available, true)
            ? $fallback
            : ($available[0] ?? $fallback);
    }

    protected function resolveLocale(Request $request): string
    {
        $candidate = $request->route('locale') ?? $request->query('locale') ?? app()->getLocale();
        $locale = $this->normalizeLocale(is_string($candidate) ? $candidate : null);
        app()->setLocale($locale);

        return $locale;
    }

    protected function resourceLocales(?string $locale = null): array
    {
        return array_values(array_unique([
            $this->normalizeLocale($locale ?? app()->getLocale()),
            $this->normalizeLocale((string) config('app.fallback_locale', 'ru')),
        ]));
    }
}
