<?php

namespace App\Traits\Admin\System\Location;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasLocationAdminCoreTrait
{
    /**
     * Доступные локали приложения.
     */
    protected function availableLocales(): array
    {
        return config(
            'app.available_locales',
            []
        );
    }

    /**
     * Базовый запрос Location.
     */
    protected function baseQuery(): Builder
    {
        return $this->modelClass::query();
    }

    /**
     * Определение текущей локали.
     */
    protected function resolveLocale(
        Request $request
    ): string {
        $locale =
            $request->route('locale')
            ?? $request->query('locale')
            ?? app()->getLocale();

        $locale = $this->normalizeLocale(
            $locale
        );

        app()->setLocale($locale);

        return $locale;
    }

    /**
     * Нормализация локали.
     */
    protected function normalizeLocale(
        ?string $locale
    ): string {
        $availableLocales =
            $this->availableLocales();

        $fallback =
            config(
                'app.fallback_locale',
                'ru'
            );

        if (
            $locale
            && in_array(
                $locale,
                $availableLocales,
                true
            )
        ) {
            return $locale;
        }

        if (
            in_array(
                $fallback,
                $availableLocales,
                true
            )
        ) {
            return $fallback;
        }

        return $availableLocales[0]
            ?? 'ru';
    }
}
