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

    /*
    |--------------------------------------------------------------------------
    | Mass assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'form_field_id',
        'value',

        'activity',
        'is_default',

        'sort',

        'settings',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'form_field_id' => 'integer',

        'activity' => 'boolean',
        'is_default' => 'boolean',

        'sort' => 'integer',

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
            FormFieldOptionTranslation::class,
            'form_field_option_id'
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
            FormFieldOptionTranslation::class,
            'form_field_option_id'
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
    ): ?FormFieldOptionTranslation {
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
    ): ?FormFieldOptionTranslation {
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
    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'form_field_options.activity',
            true
        );
    }

    /**
     * Только варианты,
     * выбранные по умолчанию.
     */
    public function scopeDefault(
        Builder $query
    ): Builder {
        return $query->where(
            'form_field_options.is_default',
            true
        );
    }

    /**
     * Варианты конкретного поля.
     */
    public function scopeForField(
        Builder $query,
        int $fieldId
    ): Builder {
        return $query->where(
            'form_field_options.form_field_id',
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
            'form_field_options.value',
            $value
        );
    }

    /**
     * Сортировка по умолчанию.
     *
     * Для вариантов сохраняем естественный
     * порядок конструктора:
     *
     * sort ASC → id ASC.
     */
    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderBy(
                'form_field_options.sort',
                'asc'
            )
            ->orderBy(
                'form_field_options.id',
                'asc'
            );
    }

    /**
     * Обратная совместимость
     * со старым названием scopeSorted().
     *
     * Новый административный контракт
     * использует ordered().
     */
    public function scopeSorted(
        Builder $query
    ): Builder {
        return $query->ordered();
    }

    /**
     * Активные варианты для публичной формы.
     */
    public function scopeForPublic(
        Builder $query
    ): Builder {
        return $query
            ->active()
            ->ordered();
    }

    /**
     * Сортировка и фильтрация
     * по единому параметру Admin Index.
     *
     * Контракт:
     *
     * idAsc / idDesc
     * sortAsc / sortDesc
     * labelAsc / labelDesc
     * valueAsc / valueDesc
     *
     * activityAsc / activityDesc
     * activity / inactive
     *
     * defaultAsc / defaultDesc
     * default / notDefault
     *
     * fieldIdAsc / fieldIdDesc
     * fieldLabelAsc / fieldLabelDesc
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
                'form_field_options.id',
                'asc'
            ),

            'idDesc' => $query->orderBy(
                'form_field_options.id',
                'desc'
            ),

            /*
            |--------------------------------------------------------------------------
            | Sort
            |--------------------------------------------------------------------------
            */

            'sortAsc' => $query
                ->orderBy(
                    'form_field_options.sort',
                    'asc'
                )
                ->orderBy(
                    'form_field_options.id',
                    'asc'
                ),

            'sortDesc' => $query
                ->orderBy(
                    'form_field_options.sort',
                    'desc'
                )
                ->orderByDesc(
                    'form_field_options.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Название варианта
            |--------------------------------------------------------------------------
            */

            'labelAsc' => $query
                ->leftJoin(
                    'form_field_option_translations as sort_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'form_field_options.id',
                            '=',
                            'sort_translations.form_field_option_id'
                        )->where(
                            'sort_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'form_field_options.*'
                )
                ->orderBy(
                    'sort_translations.label',
                    'asc'
                )
                ->orderBy(
                    'form_field_options.id',
                    'asc'
                ),

            'labelDesc' => $query
                ->leftJoin(
                    'form_field_option_translations as sort_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'form_field_options.id',
                            '=',
                            'sort_translations.form_field_option_id'
                        )->where(
                            'sort_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'form_field_options.*'
                )
                ->orderBy(
                    'sort_translations.label',
                    'desc'
                )
                ->orderByDesc(
                    'form_field_options.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Системное значение
            |--------------------------------------------------------------------------
            */

            'valueAsc' => $query
                ->orderBy(
                    'form_field_options.value',
                    'asc'
                )
                ->orderBy(
                    'form_field_options.id',
                    'asc'
                ),

            'valueDesc' => $query
                ->orderBy(
                    'form_field_options.value',
                    'desc'
                )
                ->orderByDesc(
                    'form_field_options.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Активность
            |--------------------------------------------------------------------------
            */

            'activityAsc' => $query
                ->orderBy(
                    'form_field_options.activity',
                    'asc'
                )
                ->orderBy(
                    'form_field_options.id',
                    'asc'
                ),

            'activityDesc' => $query
                ->orderBy(
                    'form_field_options.activity',
                    'desc'
                )
                ->orderByDesc(
                    'form_field_options.id'
                ),

            'activity' => $query
                ->where(
                    'form_field_options.activity',
                    true
                )
                ->ordered(),

            'inactive' => $query
                ->where(
                    'form_field_options.activity',
                    false
                )
                ->ordered(),

            /*
            |--------------------------------------------------------------------------
            | Значение по умолчанию
            |--------------------------------------------------------------------------
            */

            'defaultAsc' => $query
                ->orderBy(
                    'form_field_options.is_default',
                    'asc'
                )
                ->orderBy(
                    'form_field_options.id',
                    'asc'
                ),

            'defaultDesc' => $query
                ->orderBy(
                    'form_field_options.is_default',
                    'desc'
                )
                ->orderByDesc(
                    'form_field_options.id'
                ),

            'default' => $query
                ->where(
                    'form_field_options.is_default',
                    true
                )
                ->ordered(),

            'notDefault' => $query
                ->where(
                    'form_field_options.is_default',
                    false
                )
                ->ordered(),

            /*
            |--------------------------------------------------------------------------
            | Родительское поле: ID
            |--------------------------------------------------------------------------
            */

            'fieldIdAsc' => $query
                ->orderBy(
                    'form_field_options.form_field_id',
                    'asc'
                )
                ->orderBy(
                    'form_field_options.id',
                    'asc'
                ),

            'fieldIdDesc' => $query
                ->orderBy(
                    'form_field_options.form_field_id',
                    'desc'
                )
                ->orderByDesc(
                    'form_field_options.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Родительское поле: название
            |--------------------------------------------------------------------------
            */

            'fieldLabelAsc' => $query
                ->leftJoin(
                    'form_fields as sort_fields',
                    'form_field_options.form_field_id',
                    '=',
                    'sort_fields.id'
                )
                ->leftJoin(
                    'form_field_translations as sort_field_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'sort_fields.id',
                            '=',
                            'sort_field_translations.form_field_id'
                        )->where(
                            'sort_field_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'form_field_options.*'
                )
                ->orderBy(
                    'sort_field_translations.label',
                    'asc'
                )
                ->orderBy(
                    'form_field_options.id',
                    'asc'
                ),

            'fieldLabelDesc' => $query
                ->leftJoin(
                    'form_fields as sort_fields',
                    'form_field_options.form_field_id',
                    '=',
                    'sort_fields.id'
                )
                ->leftJoin(
                    'form_field_translations as sort_field_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'sort_fields.id',
                            '=',
                            'sort_field_translations.form_field_id'
                        )->where(
                            'sort_field_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'form_field_options.*'
                )
                ->orderBy(
                    'sort_field_translations.label',
                    'desc'
                )
                ->orderByDesc(
                    'form_field_options.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Родительская форма: ID
            |--------------------------------------------------------------------------
            */

            'formIdAsc' => $query
                ->leftJoin(
                    'form_fields as sort_fields',
                    'form_field_options.form_field_id',
                    '=',
                    'sort_fields.id'
                )
                ->addSelect(
                    'form_field_options.*'
                )
                ->orderBy(
                    'sort_fields.form_id',
                    'asc'
                )
                ->orderBy(
                    'form_field_options.id',
                    'asc'
                ),

            'formIdDesc' => $query
                ->leftJoin(
                    'form_fields as sort_fields',
                    'form_field_options.form_field_id',
                    '=',
                    'sort_fields.id'
                )
                ->addSelect(
                    'form_field_options.*'
                )
                ->orderBy(
                    'sort_fields.form_id',
                    'desc'
                )
                ->orderByDesc(
                    'form_field_options.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Родительская форма: название
            |--------------------------------------------------------------------------
            */

            'formTitleAsc' => $query
                ->leftJoin(
                    'form_fields as sort_fields',
                    'form_field_options.form_field_id',
                    '=',
                    'sort_fields.id'
                )
                ->leftJoin(
                    'forms as sort_forms',
                    'sort_fields.form_id',
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
                    'form_field_options.*'
                )
                ->orderBy(
                    'sort_form_translations.title',
                    'asc'
                )
                ->orderBy(
                    'form_field_options.id',
                    'asc'
                ),

            'formTitleDesc' => $query
                ->leftJoin(
                    'form_fields as sort_fields',
                    'form_field_options.form_field_id',
                    '=',
                    'sort_fields.id'
                )
                ->leftJoin(
                    'forms as sort_forms',
                    'sort_fields.form_id',
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
                    'form_field_options.*'
                )
                ->orderBy(
                    'sort_form_translations.title',
                    'desc'
                )
                ->orderByDesc(
                    'form_field_options.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Дата создания
            |--------------------------------------------------------------------------
            */

            'createdAtAsc',
            'dateAsc' => $query
                ->orderBy(
                    'form_field_options.created_at',
                    'asc'
                )
                ->orderBy(
                    'form_field_options.id',
                    'asc'
                ),

            'createdAtDesc',
            'dateDesc' => $query
                ->orderBy(
                    'form_field_options.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'form_field_options.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Дата обновления
            |--------------------------------------------------------------------------
            */

            'updatedAtAsc' => $query
                ->orderBy(
                    'form_field_options.updated_at',
                    'asc'
                )
                ->orderBy(
                    'form_field_options.id',
                    'asc'
                ),

            'updatedAtDesc' => $query
                ->orderBy(
                    'form_field_options.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'form_field_options.id'
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
     * - по системному value варианта;
     * - по переводу варианта текущей локали;
     * - по системному имени родительского поля;
     * - по типу родительского поля;
     * - по названию родительского поля;
     * - по коду родительской формы;
     * - по названию родительской формы.
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
                | Собственное системное значение
                |--------------------------------------------------------------------------
                */

                $q->where(
                    'form_field_options.value',
                    'like',
                    "%{$term}%"
                )

                    /*
                    |--------------------------------------------------------------------------
                    | Переводы варианта
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
                    | Родительское поле
                    |--------------------------------------------------------------------------
                    */

                    ->orWhereHas(
                        'field',
                        function (
                            Builder $fieldQuery
                        ) use (
                            $term,
                            $locale
                        ) {
                            $fieldQuery
                                ->where(
                                    function (
                                        Builder $fieldSearch
                                    ) use ($term) {
                                        $fieldSearch
                                            ->where(
                                                'form_fields.name',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'form_fields.type',
                                                'like',
                                                "%{$term}%"
                                            );
                                    }
                                )

                                /*
                                |--------------------------------------------------------------------------
                                | Перевод родительского поля
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
                                                'label',
                                                'like',
                                                "%{$term}%"
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
        );
    }
}
