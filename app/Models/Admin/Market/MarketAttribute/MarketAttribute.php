<?php

namespace App\Models\Admin\Market\MarketAttribute;

use App\Models\Admin\Market\MarketAttributeGroup\MarketAttributeGroup;
use App\Models\Admin\Market\MarketAttributeValue\MarketAttributeValue;
use App\Models\Admin\Market\MarketProductAttributeValue\MarketProductAttributeValue;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariantValue;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketAttribute extends Model
{
    use HasFactory;

    protected $table = 'market_attributes';

    protected $fillable = [
        'market_attribute_group_id',
        'user_id',

        'code',
        'icon',
        'color',
        'type',
        'unit',

        'required',
        'filterable',
        'use_for_variants',
        'visible',

        'sort',
        'activity',

        'status',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'published_at',
        'show_from_at',
        'show_to_at',
    ];

    protected $casts = [
        'market_attribute_group_id' => 'integer',
        'user_id' => 'integer',

        'required' => 'boolean',
        'filterable' => 'boolean',
        'use_for_variants' => 'boolean',
        'visible' => 'boolean',

        'sort' => 'integer',
        'activity' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',

        'published_at' => 'datetime',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',
        'moderated_at' => 'datetime',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ===================== Relations ===================== */

    /** Группа характеристики */
    public function group(): BelongsTo
    {
        return $this->belongsTo(
            MarketAttributeGroup::class,
            'market_attribute_group_id'
        );
    }

    /** Создатель характеристики */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /** Модератор характеристики */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'moderated_by'
        );
    }

    /** Все переводы характеристики */
    public function translations(): HasMany
    {
        return $this->hasMany(
            MarketAttributeTranslation::class,
            'market_attribute_id'
        );
    }

    /**
     * Перевод текущей локали.
     *
     * В Admin Index relation не используется:
     * Controller самостоятельно загружает translations,
     * ограниченные currentLocale.
     *
     * Relation сохраняется для других частей приложения.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(
            MarketAttributeTranslation::class,
            'market_attribute_id'
        )->where(
            'locale',
            app()->getLocale()
        );
    }

    /** Справочные значения характеристики */
    public function values(): HasMany
    {
        return $this->hasMany(
            MarketAttributeValue::class,
            'market_attribute_id'
        );
    }

    /** Использования характеристики в обычных товарах */
    public function productValues(): HasMany
    {
        return $this->hasMany(
            MarketProductAttributeValue::class,
            'market_attribute_id'
        );
    }

    /**
     * Использования характеристики
     * при формировании вариантов товаров.
     */
    public function variantValues(): HasMany
    {
        return $this->hasMany(
            MarketProductVariantValue::class,
            'market_attribute_id'
        );
    }

    /* ===================== Helpers ===================== */

    /**
     * Характеристика используется
     * при формировании вариантов товаров.
     */
    public function isUsedForVariants(): bool
    {
        return (bool) $this->use_for_variants;
    }

    /**
     * Характеристика имеет справочные значения.
     *
     * Если values уже загружены — дополнительного SQL нет.
     * Иначе выполняется exists().
     */
    public function hasValues(): bool
    {
        return $this->relationLoaded('values')
            ? $this->values->isNotEmpty()
            : $this->values()->exists();
    }

    /* ===================== Base scopes ===================== */

    /**
     * Только характеристики, используемые
     * при формировании вариантов товаров.
     */
    public function scopeForVariants(Builder $query): Builder
    {
        return $query->where(
            'market_attributes.use_for_variants',
            true
        );
    }

    /**
     * Только обычные характеристики,
     * не участвующие в вариантах товаров.
     */
    public function scopeNotForVariants(Builder $query): Builder
    {
        return $query->where(
            'market_attributes.use_for_variants',
            false
        );
    }

    /** Только активные характеристики */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'market_attributes.activity',
            true
        );
    }

    /** Только опубликованные характеристики */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where(
                'market_attributes.status',
                'published'
            )
            ->where(
                'market_attributes.activity',
                true
            )
            ->whereNotNull(
                'market_attributes.published_at'
            );
    }

    /** Только прошедшие модерацию характеристики */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where(
            'market_attributes.moderation_status',
            1
        );
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy(
                'market_attributes.sort',
                'asc'
            )
            ->orderByDesc(
                'market_attributes.id'
            );
    }

    /* ===================== Sorting ===================== */

    /**
     * Сортировка и фильтрация характеристик
     * по параметру Admin Index.
     *
     * Для title и groupTitle используется
     * перевод исключительно указанной locale.
     *
     * values_count должен быть заранее добавлен
     * через withCount('values') в Index Controller.
     *
     * variant_values_count добавляется только
     * при соответствующей сортировке.
     */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            /** ID */
            'idAsc' => $query->orderBy(
                'market_attributes.id',
                'asc'
            ),

            'idDesc' => $query->orderBy(
                'market_attributes.id',
                'desc'
            ),

            /** Sort */
            'sortAsc' => $query
                ->orderBy('market_attributes.sort', 'asc')
                ->orderByDesc('market_attributes.id'),

            'sortDesc' => $query
                ->orderBy('market_attributes.sort', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Название характеристики текущей локали */
            'titleAsc' => $query
                ->leftJoin(
                    'market_attribute_translations as mat_sort',
                    function ($join) use ($locale): void {
                        $join
                            ->on(
                                'mat_sort.market_attribute_id',
                                '=',
                                'market_attributes.id'
                            )
                            ->where(
                                'mat_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect('market_attributes.*')
                ->orderBy('mat_sort.title', 'asc')
                ->orderByDesc('market_attributes.id'),

            'titleDesc' => $query
                ->leftJoin(
                    'market_attribute_translations as mat_sort',
                    function ($join) use ($locale): void {
                        $join
                            ->on(
                                'mat_sort.market_attribute_id',
                                '=',
                                'market_attributes.id'
                            )
                            ->where(
                                'mat_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect('market_attributes.*')
                ->orderBy('mat_sort.title', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Название группы текущей локали */
            'groupTitleAsc' => $query
                ->leftJoin(
                    'market_attribute_group_translations as magt_sort',
                    function ($join) use ($locale): void {
                        $join
                            ->on(
                                'magt_sort.market_attribute_group_id',
                                '=',
                                'market_attributes.market_attribute_group_id'
                            )
                            ->where(
                                'magt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect('market_attributes.*')
                ->orderBy('magt_sort.title', 'asc')
                ->orderByDesc('market_attributes.id'),

            'groupTitleDesc' => $query
                ->leftJoin(
                    'market_attribute_group_translations as magt_sort',
                    function ($join) use ($locale): void {
                        $join
                            ->on(
                                'magt_sort.market_attribute_group_id',
                                '=',
                                'market_attributes.market_attribute_group_id'
                            )
                            ->where(
                                'magt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect('market_attributes.*')
                ->orderBy('magt_sort.title', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Code */
            'codeAsc' => $query
                ->orderBy('market_attributes.code', 'asc')
                ->orderByDesc('market_attributes.id'),

            'codeDesc' => $query
                ->orderBy('market_attributes.code', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Color */
            'colorAsc' => $query
                ->orderBy('market_attributes.color', 'asc')
                ->orderByDesc('market_attributes.id'),

            'colorDesc' => $query
                ->orderBy('market_attributes.color', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Type */
            'typeAsc' => $query
                ->orderBy('market_attributes.type', 'asc')
                ->orderByDesc('market_attributes.id'),

            'typeDesc' => $query
                ->orderBy('market_attributes.type', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Unit */
            'unitAsc' => $query
                ->orderBy('market_attributes.unit', 'asc')
                ->orderByDesc('market_attributes.id'),

            'unitDesc' => $query
                ->orderBy('market_attributes.unit', 'desc')
                ->orderByDesc('market_attributes.id'),

            /**
             * Количество справочных значений.
             *
             * values_count уже должен присутствовать
             * в query через Index Controller.
             */
            'valuesCountAsc' => $query
                ->orderBy('values_count', 'asc')
                ->orderByDesc('market_attributes.id'),

            'valuesCountDesc' => $query
                ->orderBy('values_count', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Required */
            'requiredAsc' => $query
                ->orderBy('market_attributes.required', 'asc')
                ->orderByDesc('market_attributes.id'),

            'requiredDesc' => $query
                ->orderBy('market_attributes.required', 'desc')
                ->orderByDesc('market_attributes.id'),

            'required' => $query
                ->where('market_attributes.required', true)
                ->orderByDesc('market_attributes.id'),

            'notRequired' => $query
                ->where('market_attributes.required', false)
                ->orderByDesc('market_attributes.id'),

            /** Filterable */
            'filterableAsc' => $query
                ->orderBy('market_attributes.filterable', 'asc')
                ->orderByDesc('market_attributes.id'),

            'filterableDesc' => $query
                ->orderBy('market_attributes.filterable', 'desc')
                ->orderByDesc('market_attributes.id'),

            'filterable' => $query
                ->where('market_attributes.filterable', true)
                ->orderByDesc('market_attributes.id'),

            'notFilterable' => $query
                ->where('market_attributes.filterable', false)
                ->orderByDesc('market_attributes.id'),

            /**
             * Количество использований в вариантах.
             *
             * Этот счётчик не нужен каждому Index-запросу,
             * поэтому добавляется только для сортировки.
             */
            'variantValuesCountAsc' => $query
                ->withCount('variantValues')
                ->orderBy('variant_values_count', 'asc')
                ->orderByDesc('market_attributes.id'),

            'variantValuesCountDesc' => $query
                ->withCount('variantValues')
                ->orderBy('variant_values_count', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Использование для вариантов */
            'useForVariantsAsc' => $query
                ->orderBy('market_attributes.use_for_variants', 'asc')
                ->orderByDesc('market_attributes.id'),

            'useForVariantsDesc' => $query
                ->orderBy('market_attributes.use_for_variants', 'desc')
                ->orderByDesc('market_attributes.id'),

            'useForVariants' => $query
                ->where('market_attributes.use_for_variants', true)
                ->orderByDesc('market_attributes.id'),

            'notForVariants' => $query
                ->where('market_attributes.use_for_variants', false)
                ->orderByDesc('market_attributes.id'),

            /** Visible */
            'visibleAsc' => $query
                ->orderBy('market_attributes.visible', 'asc')
                ->orderByDesc('market_attributes.id'),

            'visibleDesc' => $query
                ->orderBy('market_attributes.visible', 'desc')
                ->orderByDesc('market_attributes.id'),

            'visible' => $query
                ->where('market_attributes.visible', true)
                ->orderByDesc('market_attributes.id'),

            'hidden' => $query
                ->where('market_attributes.visible', false)
                ->orderByDesc('market_attributes.id'),

            /** Activity */
            'activityAsc' => $query
                ->orderBy('market_attributes.activity', 'asc')
                ->orderByDesc('market_attributes.id'),

            'activityDesc' => $query
                ->orderBy('market_attributes.activity', 'desc')
                ->orderByDesc('market_attributes.id'),

            'activity' => $query
                ->where('market_attributes.activity', true)
                ->orderByDesc('market_attributes.id'),

            'inactive' => $query
                ->where('market_attributes.activity', false)
                ->orderByDesc('market_attributes.id'),

            /** Status */
            'statusAsc' => $query
                ->orderBy('market_attributes.status', 'asc')
                ->orderByDesc('market_attributes.id'),

            'statusDesc' => $query
                ->orderBy('market_attributes.status', 'desc')
                ->orderByDesc('market_attributes.id'),

            'statusDraft' => $query
                ->where('market_attributes.status', 'draft')
                ->orderByDesc('market_attributes.id'),

            'statusPublished' => $query
                ->where('market_attributes.status', 'published')
                ->orderByDesc('market_attributes.id'),

            'statusArchived' => $query
                ->where('market_attributes.status', 'archived')
                ->orderByDesc('market_attributes.id'),

            /** Даты публикации */
            'publishedAtAsc' => $query
                ->orderBy('market_attributes.published_at', 'asc')
                ->orderByDesc('market_attributes.id'),

            'publishedAtDesc' => $query
                ->orderBy('market_attributes.published_at', 'desc')
                ->orderByDesc('market_attributes.id'),

            'showFromAtAsc' => $query
                ->orderBy('market_attributes.show_from_at', 'asc')
                ->orderByDesc('market_attributes.id'),

            'showFromAtDesc' => $query
                ->orderBy('market_attributes.show_from_at', 'desc')
                ->orderByDesc('market_attributes.id'),

            'showToAtAsc' => $query
                ->orderBy('market_attributes.show_to_at', 'asc')
                ->orderByDesc('market_attributes.id'),

            'showToAtDesc' => $query
                ->orderBy('market_attributes.show_to_at', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Created */
            'createdAtAsc', 'dateAsc' => $query
                ->orderBy('market_attributes.created_at', 'asc')
                ->orderByDesc('market_attributes.id'),

            'createdAtDesc', 'dateDesc' => $query
                ->orderBy('market_attributes.created_at', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Updated */
            'updatedAtAsc' => $query
                ->orderBy('market_attributes.updated_at', 'asc')
                ->orderByDesc('market_attributes.id'),

            'updatedAtDesc' => $query
                ->orderBy('market_attributes.updated_at', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Moderation */
            'moderationPending' => $query
                ->where('market_attributes.moderation_status', 0)
                ->orderByDesc('market_attributes.id'),

            'moderationApproved' => $query
                ->where('market_attributes.moderation_status', 1)
                ->orderByDesc('market_attributes.id'),

            'moderationRejected' => $query
                ->where('market_attributes.moderation_status', 2)
                ->orderByDesc('market_attributes.id'),

            'moderationStatusAsc' => $query
                ->orderBy('market_attributes.moderation_status', 'asc')
                ->orderByDesc('market_attributes.id'),

            'moderationStatusDesc' => $query
                ->orderBy('market_attributes.moderation_status', 'desc')
                ->orderByDesc('market_attributes.id'),

            /** Владелец */
            'ownerNameAsc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'market_attributes.user_id',
                    '=',
                    'owner_sort.id'
                )
                ->addSelect('market_attributes.*')
                ->orderBy('owner_sort.name', 'asc')
                ->orderByDesc('market_attributes.id'),

            'ownerNameDesc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'market_attributes.user_id',
                    '=',
                    'owner_sort.id'
                )
                ->addSelect('market_attributes.*')
                ->orderBy('owner_sort.name', 'desc')
                ->orderByDesc('market_attributes.id'),

            'ownerEmailAsc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'market_attributes.user_id',
                    '=',
                    'owner_sort.id'
                )
                ->addSelect('market_attributes.*')
                ->orderBy('owner_sort.email', 'asc')
                ->orderByDesc('market_attributes.id'),

            'ownerEmailDesc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'market_attributes.user_id',
                    '=',
                    'owner_sort.id'
                )
                ->addSelect('market_attributes.*')
                ->orderBy('owner_sort.email', 'desc')
                ->orderByDesc('market_attributes.id'),

            default => $query->orderByDesc(
                'market_attributes.id'
            ),
        };
    }

    /* ===================== Public ===================== */

    /** Попадает в окно показа */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('market_attributes.show_from_at')
                    ->orWhere(
                        'market_attributes.show_from_at',
                        '<=',
                        now()
                    );
            })
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('market_attributes.show_to_at')
                    ->orWhere(
                        'market_attributes.show_to_at',
                        '>=',
                        now()
                    );
            });
    }

    /** Доступные характеристики для публичной части */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /* ===================== Search ===================== */

    /**
     * Поиск характеристик.
     *
     * Поиск выполняется по:
     * - code;
     * - icon;
     * - color;
     * - type;
     * - unit;
     * - status;
     * - moderation_note;
     * - переводу характеристики текущей locale;
     * - переводу группы текущей locale;
     * - имени/email владельца.
     */
    public function scopeSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();
        $like = "%{$term}%";

        return $query->where(
            function (Builder $query) use ($like, $locale): void {
                $query
                    ->where(
                        'market_attributes.code',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_attributes.icon',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_attributes.color',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_attributes.type',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_attributes.unit',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_attributes.status',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_attributes.moderation_note',
                        'like',
                        $like
                    )

                    /** Перевод характеристики текущей locale */
                    ->orWhereHas(
                        'translations',
                        function (Builder $translationQuery) use ($locale, $like): void {
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (Builder $query) use ($like): void {
                                        $query
                                            ->where('title', 'like', $like)
                                            ->orWhere('subtitle', 'like', $like)
                                            ->orWhere('short', 'like', $like)
                                            ->orWhere('description', 'like', $like);
                                    }
                                );
                        }
                    )

                    /** Перевод группы текущей locale */
                    ->orWhereHas(
                        'group.translations',
                        function (Builder $groupTranslationQuery) use ($locale, $like): void {
                            $groupTranslationQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (Builder $query) use ($like): void {
                                        $query
                                            ->where('title', 'like', $like)
                                            ->orWhere('subtitle', 'like', $like)
                                            ->orWhere('short', 'like', $like);
                                    }
                                );
                        }
                    )

                    /** Владелец */
                    ->orWhereHas(
                        'owner',
                        function (Builder $ownerQuery) use ($like): void {
                            $ownerQuery
                                ->where('name', 'like', $like)
                                ->orWhere('email', 'like', $like);
                        }
                    );
            }
        );
    }
}
