<?php

namespace App\Models\Admin\Form\FormFieldOption;

use App\Models\Admin\Form\FormField\FormField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FormFieldOption extends Model
{
    use HasFactory;

    protected $table = 'form_field_options';

    protected $fillable = [
        'form_field_id',
        'value',
        'activity',
        'is_default',
        'sort',
        'settings',
    ];

    protected $casts = [
        'form_field_id' => 'integer',

        'activity' => 'boolean',
        'is_default' => 'boolean',

        'sort' => 'integer',

        'settings' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Поле формы, которому принадлежит вариант.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(
            FormField::class,
            'form_field_id'
        );
    }

    /**
     * Все переводы варианта.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(
            FormFieldOptionTranslation::class
        );
    }

    /**
     * Перевод для текущей локали приложения.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(
            FormFieldOptionTranslation::class
        )->where(
            'locale',
            app()->getLocale()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Translation helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить перевод указанной локали
     * с fallback на системную локаль.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?FormFieldOptionTranslation {
        $locale ??= app()->getLocale();
        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        $translations = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->get();

        $translation = $translations->firstWhere(
            'locale',
            $locale
        );

        if ($translation) {
            return $translation;
        }

        if ($fallbackLocale !== $locale) {
            $fallback = $translations->firstWhere(
                'locale',
                $fallbackLocale
            );

            if ($fallback) {
                return $fallback;
            }
        }

        return $translations->first();
    }

    /**
     * Получить перевод указанной локали
     * только из уже загруженной relation translations.
     *
     * Метод никогда не выполняет SQL-запрос.
     *
     * Порядок поиска:
     * 1. указанная / текущая локаль;
     * 2. fallback locale приложения;
     * 3. первый доступный перевод.
     */
    public function loadedTranslationOrFallback(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?FormFieldOptionTranslation {
        if (!$this->relationLoaded('translations')) {
            return null;
        }

        $locale ??= app()->getLocale();

        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $this->translations->firstWhere(
            'locale',
            $locale
        );

        if ($translation) {
            return $translation;
        }

        if ($fallbackLocale !== $locale) {
            $fallback = $this->translations->firstWhere(
                'locale',
                $fallbackLocale
            );

            if ($fallback) {
                return $fallback;
            }
        }

        return $this->translations->first();
    }

    /**
     * Получить переведённое название варианта.
     */
    public function getTranslatedLabel(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?string {
        return $this
            ->translationOrFallback(
                $locale,
                $fallbackLocale
            )
            ?->label;
    }

    /**
     * Получить переведённое описание варианта.
     */
    public function getTranslatedDescription(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?string {
        return $this
            ->translationOrFallback(
                $locale,
                $fallbackLocale
            )
            ?->description;
    }

    /*
    |--------------------------------------------------------------------------
    | Settings helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить отдельную настройку варианта.
     */
    public function getSetting(
        string $key,
        mixed $default = null
    ): mixed {
        return data_get(
            $this->settings ?? [],
            $key,
            $default
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Только активные варианты.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('activity', true);
    }

    /**
     * Только варианты, выбранные по умолчанию.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Варианты конкретного поля.
     */
    public function scopeForField(
        Builder $query,
        int $fieldId
    ): Builder {
        return $query->where(
            'form_field_id',
            $fieldId
        );
    }

    /**
     * Поиск варианта по системному значению.
     */
    public function scopeByValue(
        Builder $query,
        string $value
    ): Builder {
        return $query->where(
            'value',
            $value
        );
    }

    /**
     * Сортировка вариантов.
     */
    public function scopeSorted(Builder $query): Builder
    {
        return $query
            ->orderBy('sort')
            ->orderBy('id');
    }

    /**
     * Активные варианты для публичной формы.
     */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->active()
            ->sorted();
    }
}
