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

    /*
    |--------------------------------------------------------------------------
    | Mass assignment
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'form_id' => 'integer',

        'activity' => 'boolean',
        'required' => 'boolean',
        'readonly' => 'boolean',
        'disabled' => 'boolean',

        'sort' => 'integer',

        'validation' => 'array',
        'settings' => 'array',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        return $this->belongsTo(
            Form::class,
            'form_id'
        );
    }

    /**
     * Все переводы поля.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(
            FormFieldTranslation::class,
            'form_field_id'
        );
    }

    /**
     * Перевод для текущей локали приложения.
     *
     * Relation оставляем для публичных
     * и внешних сценариев.
     *
     * Admin Index использует translations
     * с заранее ограниченным набором локалей.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(
            FormFieldTranslation::class,
            'form_field_id'
        )->where(
            'locale',
            app()->getLocale()
        );
    }

    /**
     * Все варианты выбора поля.
     *
     * Используется для:
     * - select;
     * - radio;
     * - checkbox_group.
     */
    public function options(): HasMany
    {
        return $this->hasMany(
            FormFieldOption::class,
            'form_field_id'
        )
            ->orderBy(
                'sort',
                'asc'
            )
            ->orderBy(
                'id',
                'asc'
            );
    }

    /**
     * Активные варианты выбора.
     */
    public function activeOptions(): HasMany
    {
        return $this->hasMany(
            FormFieldOption::class,
            'form_field_id'
        )
            ->where(
                'activity',
                true
            )
            ->orderBy(
                'sort',
                'asc'
            )
            ->orderBy(
                'id',
                'asc'
            );
    }

    /**
     * Исторические значения этого поля
     * в отправленных заявках.
     *
     * Связь может быть потеряна после удаления поля,
     * но snapshot данных в form_submission_values
     * сохраняется.
     */
    public function submissionValues(): HasMany
    {
        return $this->hasMany(
            FormSubmissionValue::class,
            'form_field_id'
        );
    }

    /**
     * Файлы, загруженные через это поле.
     *
     * После удаления поля связь может быть потеряна,
     * но snapshot данных файла сохраняется.
     */
    public function submissionFiles(): HasMany
    {
        return $this->hasMany(
            FormSubmissionFile::class,
            'form_field_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Translation helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить перевод указанной локали.
     *
     * Порядок поиска:
     * 1. указанная / текущая локаль;
     * 2. fallback locale приложения;
     * 3. первый доступный перевод.
     *
     * Если relation translations уже загружена,
     * дополнительный SQL-запрос не выполняется.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?FormFieldTranslation {
        $locale ??= app()->getLocale();

        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        $translations = $this->relationLoaded(
            'translations'
        )
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
        if (
            !$this->relationLoaded(
                'translations'
            )
        ) {
            return null;
        }

        $locale ??= app()->getLocale();

        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $this->translations
            ->firstWhere(
                'locale',
                $locale
            );

        if ($translation) {
            return $translation;
        }

        if ($fallbackLocale !== $locale) {
            $fallback = $this->translations
                ->firstWhere(
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
            ->translationOrFallback(
                $locale,
                $fallbackLocale
            )
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
        return config(
            "forms.field_types.{$this->type}"
        );
    }

    /**
     * Поддерживает ли тип поля варианты выбора.
     *
     * Например:
     * - select;
     * - radio;
     * - checkbox_group.
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
    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'form_fields.activity',
            true
        );
    }

    /**
     * Только включённые поля.
     */
    public function scopeEnabled(
        Builder $query
    ): Builder {
        return $query->where(
            'form_fields.disabled',
            false
        );
    }

    /**
     * Только обязательные поля.
     */
    public function scopeRequired(
        Builder $query
    ): Builder {
        return $query->where(
            'form_fields.required',
            true
        );
    }

    /**
     * Поля конкретной формы.
     */
    public function scopeForForm(
        Builder $query,
        int $formId
    ): Builder {
        return $query->where(
            'form_fields.form_id',
            $formId
        );
    }

    /**
     * Поля определённого типа.
     */
    public function scopeOfType(
        Builder $query,
        string $type
    ): Builder {
        return $query->where(
            'form_fields.type',
            $type
        );
    }

    /**
     * Поля, доступные для публичного рендера.
     *
     * disabled-поле может отображаться пользователю,
     * поэтому здесь проверяется только activity.
     */
    public function scopeForPublic(
        Builder $query
    ): Builder {
        return $query->active();
    }

    /**
     * Сортировка по умолчанию.
     */
    /**
     * Сортировка по умолчанию.
     */
    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderBy(
                'form_fields.sort',
                'asc'
            )
            ->orderByDesc(
                'form_fields.id'
            );
    }

    /**
     * Обратная совместимость
     * со старым названием scopeSorted().
     *
     * Основной административный контракт
     * использует ordered().
     */
    public function scopeSorted(
        Builder $query
    ): Builder {
        return $query->ordered();
    }

    /**
     * Сортировка и фильтрация
     * по единому параметру списка.
     *
     * Контракт:
     *
     * idAsc / idDesc
     * sortAsc / sortDesc
     * labelAsc / labelDesc
     * nameAsc / nameDesc
     * typeAsc / typeDesc
     * widthAsc / widthDesc
     *
     * activityAsc / activityDesc
     * activity / inactive
     *
     * requiredAsc / requiredDesc
     * required / optional
     *
     * readonlyAsc / readonlyDesc
     * readonly / editable
     *
     * disabledAsc / disabledDesc
     * disabled / enabled
     *
     * optionsAsc / optionsDesc
     *
     * formIdAsc / formIdDesc
     * formTitleAsc / formTitleDesc
     *
     * createdAtAsc / createdAtDesc
     * dateAsc / dateDesc
     * updatedAtAsc / updatedAtDesc
     */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale
            ?: app()->getLocale();

        return match ($sort) {
            /*
            |--------------------------------------------------------------------------
            | ID
            |--------------------------------------------------------------------------
            */

            'idAsc' => $query->orderBy(
                'form_fields.id',
                'asc'
            ),

            'idDesc' => $query->orderBy(
                'form_fields.id',
                'desc'
            ),

            /*
            |--------------------------------------------------------------------------
            | Sort
            |--------------------------------------------------------------------------
            */

            'sortAsc' => $query
                ->orderBy(
                    'form_fields.sort',
                    'asc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            'sortDesc' => $query
                ->orderBy(
                    'form_fields.sort',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Название / label
            |--------------------------------------------------------------------------
            */

            'labelAsc' => $query
                ->leftJoin(
                    'form_field_translations as sort_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'form_fields.id',
                            '=',
                            'sort_translations.form_field_id'
                        )->where(
                            'sort_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'form_fields.*'
                )
                ->orderBy(
                    'sort_translations.label',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'labelDesc' => $query
                ->leftJoin(
                    'form_field_translations as sort_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'form_fields.id',
                            '=',
                            'sort_translations.form_field_id'
                        )->where(
                            'sort_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'form_fields.*'
                )
                ->orderBy(
                    'sort_translations.label',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Системное имя
            |--------------------------------------------------------------------------
            */

            'nameAsc' => $query
                ->orderBy(
                    'form_fields.name',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'nameDesc' => $query
                ->orderBy(
                    'form_fields.name',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Тип поля
            |--------------------------------------------------------------------------
            */

            'typeAsc' => $query
                ->orderBy(
                    'form_fields.type',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'typeDesc' => $query
                ->orderBy(
                    'form_fields.type',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Ширина
            |--------------------------------------------------------------------------
            */

            'widthAsc' => $query
                ->orderBy(
                    'form_fields.width',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'widthDesc' => $query
                ->orderBy(
                    'form_fields.width',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Активность
            |--------------------------------------------------------------------------
            */

            'activityAsc' => $query
                ->orderBy(
                    'form_fields.activity',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'activityDesc' => $query
                ->orderBy(
                    'form_fields.activity',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            'activity' => $query
                ->where(
                    'form_fields.activity',
                    true
                )
                ->ordered(),

            'inactive' => $query
                ->where(
                    'form_fields.activity',
                    false
                )
                ->ordered(),

            /*
            |--------------------------------------------------------------------------
            | Обязательность
            |--------------------------------------------------------------------------
            */

            'requiredAsc' => $query
                ->orderBy(
                    'form_fields.required',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'requiredDesc' => $query
                ->orderBy(
                    'form_fields.required',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            'required' => $query
                ->where(
                    'form_fields.required',
                    true
                )
                ->ordered(),

            'optional' => $query
                ->where(
                    'form_fields.required',
                    false
                )
                ->ordered(),

            /*
            |--------------------------------------------------------------------------
            | Только чтение
            |--------------------------------------------------------------------------
            */

            'readonlyAsc' => $query
                ->orderBy(
                    'form_fields.readonly',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'readonlyDesc' => $query
                ->orderBy(
                    'form_fields.readonly',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            'readonly' => $query
                ->where(
                    'form_fields.readonly',
                    true
                )
                ->ordered(),

            'editable' => $query
                ->where(
                    'form_fields.readonly',
                    false
                )
                ->ordered(),

            /*
            |--------------------------------------------------------------------------
            | Disabled
            |--------------------------------------------------------------------------
            */

            'disabledAsc' => $query
                ->orderBy(
                    'form_fields.disabled',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'disabledDesc' => $query
                ->orderBy(
                    'form_fields.disabled',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            'disabled' => $query
                ->where(
                    'form_fields.disabled',
                    true
                )
                ->ordered(),

            'enabled' => $query
                ->where(
                    'form_fields.disabled',
                    false
                )
                ->ordered(),

            /*
            |--------------------------------------------------------------------------
            | Количество вариантов
            |--------------------------------------------------------------------------
            */

            'optionsAsc' => $query
                ->withCount(
                    'options'
                )
                ->orderBy(
                    'options_count',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'optionsDesc' => $query
                ->withCount(
                    'options'
                )
                ->orderBy(
                    'options_count',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Родительская форма: ID
            |--------------------------------------------------------------------------
            */

            'formIdAsc' => $query
                ->orderBy(
                    'form_fields.form_id',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'formIdDesc' => $query
                ->orderBy(
                    'form_fields.form_id',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Родительская форма: название
            |--------------------------------------------------------------------------
            */

            'formTitleAsc' => $query
                ->leftJoin(
                    'forms as sort_forms',
                    'form_fields.form_id',
                    '=',
                    'sort_forms.id'
                )
                ->leftJoin(
                    'form_translations as sort_form_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'sort_forms.id',
                            '=',
                            'sort_form_translations.form_id'
                        )->where(
                            'sort_form_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'form_fields.*'
                )
                ->orderBy(
                    'sort_form_translations.title',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'formTitleDesc' => $query
                ->leftJoin(
                    'forms as sort_forms',
                    'form_fields.form_id',
                    '=',
                    'sort_forms.id'
                )
                ->leftJoin(
                    'form_translations as sort_form_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'sort_forms.id',
                            '=',
                            'sort_form_translations.form_id'
                        )->where(
                            'sort_form_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'form_fields.*'
                )
                ->orderBy(
                    'sort_form_translations.title',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Дата создания
            |--------------------------------------------------------------------------
            */

            'createdAtAsc',
            'dateAsc' => $query
                ->orderBy(
                    'form_fields.created_at',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'createdAtDesc',
            'dateDesc' => $query
                ->orderBy(
                    'form_fields.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Дата обновления
            |--------------------------------------------------------------------------
            */

            'updatedAtAsc' => $query
                ->orderBy(
                    'form_fields.updated_at',
                    'asc'
                )
                ->orderBy(
                    'form_fields.id',
                    'asc'
                ),

            'updatedAtDesc' => $query
                ->orderBy(
                    'form_fields.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'form_fields.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Сортировка по умолчанию
            |--------------------------------------------------------------------------
            */

            default => $query->ordered(),
        };
    }

    /**
     * Поиск для Admin Index.
     *
     * Поиск выполняется:
     * - по системному имени поля;
     * - по типу;
     * - по переводу текущей локали;
     * - по коду родительской формы;
     * - по названию родительской формы
     *   в текущей локали.
     */
    public function scopeSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim(
            (string) $term
        );

        if ($term === '') {
            return $query;
        }

        $locale = $locale
            ?: app()->getLocale();

        return $query->where(
            function (Builder $q) use (
                $term,
                $locale
            ) {
                /*
                |--------------------------------------------------------------------------
                | Собственные поля
                |--------------------------------------------------------------------------
                */

                $q->where(
                    'form_fields.name',
                    'like',
                    "%{$term}%"
                )
                    ->orWhere(
                        'form_fields.type',
                        'like',
                        "%{$term}%"
                    )

                    /*
                    |--------------------------------------------------------------------------
                    | Переводы поля
                    |--------------------------------------------------------------------------
                    */

                    ->orWhereHas(
                        'translations',
                        function (
                            Builder $translationQuery
                        ) use (
                            $term,
                            $locale
                        ) {
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (
                                        Builder $searchQuery
                                    ) use ($term) {
                                        $searchQuery
                                            ->where(
                                                'label',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'placeholder',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'description',
                                                'like',
                                                "%{$term}%"
                                            );
                                    }
                                );
                        }
                    )

                    /*
                    |--------------------------------------------------------------------------
                    | Родительская форма
                    |--------------------------------------------------------------------------
                    */

                    ->orWhereHas(
                        'form',
                        function (
                            Builder $formQuery
                        ) use (
                            $term,
                            $locale
                        ) {
                            $formQuery
                                ->where(
                                    'forms.code',
                                    'like',
                                    "%{$term}%"
                                )
                                ->orWhereHas(
                                    'translations',
                                    function (
                                        Builder $translationQuery
                                    ) use (
                                        $term,
                                        $locale
                                    ) {
                                        $translationQuery
                                            ->where(
                                                'locale',
                                                $locale
                                            )
                                            ->where(
                                                'title',
                                                'like',
                                                "%{$term}%"
                                            );
                                    }
                                );
                        }
                    );
            }
        );
    }
}
