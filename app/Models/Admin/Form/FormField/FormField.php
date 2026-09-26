<?php

namespace App\Models\Admin\Form\FormField;

use App\Models\Admin\Form\Form\Form;
use App\Models\Admin\Form\FormFieldOption\FormFieldOption;
use App\Models\Admin\Form\FormSubmissionFile\FormSubmissionFile;
use App\Models\Admin\Form\FormSubmissionValue\FormSubmissionValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FormField extends Model
{
    use HasFactory;

    protected $table = 'form_fields';

    protected $fillable = [
        'form_id',
        'name',
        'type',
        'activity',
        'required',
        'readonly',
        'disabled',
        'sort',
        'default_value',
        'validation',
        'width',
        'settings',
    ];

    protected $casts = [
        'form_id' => 'integer',

        'activity' => 'boolean',
        'required' => 'boolean',
        'readonly' => 'boolean',
        'disabled' => 'boolean',

        'sort' => 'integer',

        'validation' => 'array',
        'settings' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Форма, которой принадлежит поле.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Все переводы поля.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(FormFieldTranslation::class);
    }

    /**
     * Перевод для текущей локали приложения.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(FormFieldTranslation::class)
            ->where('locale', app()->getLocale());
    }

    /**
     * Все варианты выбора поля.
     *
     * Используется для:
     * select
     * radio
     * checkbox_group
     */
    public function options(): HasMany
    {
        return $this->hasMany(FormFieldOption::class)
            ->orderBy('sort')
            ->orderBy('id');
    }

    /**
     * Активные варианты выбора.
     */
    public function activeOptions(): HasMany
    {
        return $this->hasMany(FormFieldOption::class)
            ->where('activity', true)
            ->orderBy('sort')
            ->orderBy('id');
    }

    /**
     * Исторические значения этого поля
     * в отправленных заявках.
     *
     * Связь может быть потеряна после удаления поля,
     * но snapshot данных в form_submission_values сохранится.
     */
    public function submissionValues(): HasMany
    {
        return $this->hasMany(FormSubmissionValue::class);
    }

    /**
     * Файлы, загруженные через это поле.
     */
    public function submissionFiles(): HasMany
    {
        return $this->hasMany(FormSubmissionFile::class);
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
    ): ?FormFieldTranslation {
        $locale ??= app()->getLocale();
        $fallbackLocale ??= config('app.fallback_locale', 'ru');

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
    ): ?FormFieldTranslation {
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
     * Получить переведённое название поля.
     */
    public function getTranslatedLabel(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?string {
        return $this
            ->translationOrFallback($locale, $fallbackLocale)
            ?->label;
    }

    /*
    |--------------------------------------------------------------------------
    | Field type helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить конфигурацию текущего типа поля.
     */
    public function getTypeConfig(): ?array
    {
        return config("forms.field_types.{$this->type}");
    }

    /**
     * Поддерживает ли тип поля варианты выбора.
     *
     * Например:
     * select
     * radio
     * checkbox_group
     */
    public function supportsOptions(): bool
    {
        return (bool) (
            $this->getTypeConfig()['has_options']
            ?? false
        );
    }

    /**
     * Поддерживает ли поле множественные значения.
     */
    public function supportsMultiple(): bool
    {
        return (bool) (
            $this->getTypeConfig()['supports_multiple']
            ?? false
        );
    }

    /**
     * Является ли поле файловым.
     */
    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    /**
     * Является ли поле скрытым.
     */
    public function isHidden(): bool
    {
        return $this->type === 'hidden';
    }

    /**
     * Является ли поле обычным checkbox.
     */
    public function isCheckbox(): bool
    {
        return $this->type === 'checkbox';
    }

    /**
     * Является ли поле группой checkbox.
     */
    public function isCheckboxGroup(): bool
    {
        return $this->type === 'checkbox_group';
    }

    /*
    |--------------------------------------------------------------------------
    | Settings helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить отдельную настройку поля.
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

    /**
     * Разрешена ли множественная загрузка файлов.
     */
    public function allowsMultipleFiles(): bool
    {
        if (!$this->isFile()) {
            return false;
        }

        return (bool) $this->getSetting(
            'multiple',
            false
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Только активные поля.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('activity', true);
    }

    /**
     * Только включённые поля.
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('disabled', false);
    }

    /**
     * Только обязательные поля.
     */
    public function scopeRequired(Builder $query): Builder
    {
        return $query->where('required', true);
    }

    /**
     * Поля конкретной формы.
     */
    public function scopeForForm(
        Builder $query,
        int $formId
    ): Builder {
        return $query->where('form_id', $formId);
    }

    /**
     * Поля определённого типа.
     */
    public function scopeOfType(
        Builder $query,
        string $type
    ): Builder {
        return $query->where('type', $type);
    }

    /**
     * Поля, доступные для публичного рендера.
     *
     * disabled-поле может отображаться пользователю,
     * поэтому здесь проверяется только activity.
     */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query->active();
    }

    /**
     * Сортировка полей.
     */
    public function scopeSorted(Builder $query): Builder
    {
        return $query
            ->orderBy('sort')
            ->orderBy('id');
    }
}
