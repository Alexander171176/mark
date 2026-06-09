<?php

namespace App\Traits\Admin\School;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasSchoolAdminCoreTrait
{
    /** Доступные локали приложения */
    protected function availableLocales(): array
    {
        return config('app.available_locales', []);
    }

    /** Базовый запрос */
    protected function baseQuery(): Builder
    {
        return $this->modelClass::query();
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

    /** Нормализация локали */
    protected function normalizeLocale(?string $locale): string
    {
        $availableLocales = $this->availableLocales();
        $fallback = config('app.fallback_locale');

        return $locale && in_array($locale, $availableLocales, true)
            ? $locale
            : $fallback;
    }
}
