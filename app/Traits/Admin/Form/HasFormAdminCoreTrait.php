<?php

namespace App\Traits\Admin\Form;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasFormAdminCoreTrait
{
    /**
     * Доступные локали приложения.
     *
     * @return array<int, string>
     */
    protected function availableLocales(): array
    {
        return config(
            'app.available_locales',
            []
        );
    }

    /**
     * Базовый запрос административной сущности.
     *
     * Для сущностей, непосредственно принадлежащих
     * пользователю через собственный user_id,
     * применяется owner scope.
     *
     * Для вложенных сущностей:
     *
     * FormField
     * FormFieldOption
     * FormSubmission
     *
     * контроллер должен переопределить baseQuery()
     * и ограничить доступ через родительскую Form.
     */
    protected function baseQuery(): Builder
    {
        $query = $this->modelClass::query();

        if (!$this->usesOwnerScope()) {
            return $query;
        }

        $user = auth()->user();

        if (
            $user
            && method_exists(
                $user,
                'hasRole'
            )
            && !$user->hasRole('admin')
        ) {
            $query->where(
                'user_id',
                $user->id
            );
        }

        return $query;
    }

    /**
     * Использует ли сущность прямое ограничение
     * доступа через собственную колонку user_id.
     */
    protected function usesOwnerScope(): bool
    {
        return $this->ownerScoped;
    }

    /**
     * Определение текущей локали.
     *
     * Приоритет:
     *
     * 1. route locale;
     * 2. query locale;
     * 3. текущая locale приложения;
     * 4. fallback_locale.
     */
    protected function resolveLocale(
        Request $request
    ): string {
        $locale =
            $request->route('locale')
            ?? $request->query('locale')
            ?? app()->getLocale();

        $locale = $this->normalizeLocale(
            is_string($locale)
                ? $locale
                : null
        );

        app()->setLocale(
            $locale
        );

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

        $fallback = (string) config(
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
            ?? $fallback;
    }

    /**
     * Current + fallback локали.
     *
     * Используется компактными Resource,
     * которым нужен fallback без дополнительных
     * SQL-запросов.
     *
     * @return array<int, string>
     */
    protected function resourceLocales(
        ?string $locale = null
    ): array {
        $locale = $this->normalizeLocale(
            $locale ?? app()->getLocale()
        );

        $fallback = $this->normalizeLocale(
            (string) config(
                'app.fallback_locale',
                'ru'
            )
        );

        return array_values(
            array_unique([
                $locale,
                $fallback,
            ])
        );
    }
}
