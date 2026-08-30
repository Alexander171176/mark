<?php

namespace App\Models\Admin\Market\MarketAttributeValue;

use App\Models\Admin\Market\MarketAttribute\MarketAttribute;
use App\Models\Admin\Market\MarketProductAttributeValue\MarketProductAttributeValue;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariantValue;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketAttributeValue extends Model
{
    use HasFactory;

    protected $table = 'market_attribute_values';

    protected $fillable = [
        'market_attribute_id',

        'code',
        'icon',
        'color',

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
        'market_attribute_id' => 'integer',

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

    /** Характеристика */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(
            MarketAttribute::class,
            'market_attribute_id'
        );
    }

    /** Модератор */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'moderated_by'
        );
    }

    /** Переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(
            MarketAttributeValueTranslation::class,
            'market_attribute_value_id'
        );
    }

    /**
     * Текущий перевод.
     *
     * Relation сохраняем для других частей приложения.
     * Admin Index использует translations,
     * заранее ограниченные currentLocale.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(
            MarketAttributeValueTranslation::class,
            'market_attribute_value_id'
        )->where(
            'locale',
            app()->getLocale()
        );
    }

    /**
     * Использования справочного значения
     * в обычных товарах.
     */
    public function productValues(): HasMany
    {
        return $this->hasMany(
            MarketProductAttributeValue::class,
            'market_attribute_value_id'
        );
    }

    /**
     * Использования значения характеристики
     * в вариантах товаров.
     */
    public function variantValues(): HasMany
    {
        return $this->hasMany(
            MarketProductVariantValue::class,
            'market_attribute_value_id'
        );
    }

    /* ===================== Helpers ===================== */

    /**
     * Используется ли значение характеристики
     * хотя бы в одном варианте товара.
     */
    public function isUsedInVariants(): bool
    {
        return $this->relationLoaded('variantValues')
            ? $this->variantValues->isNotEmpty()
            : $this->variantValues()->exists();
    }

    /* ===================== Scopes ===================== */

    /** Только активные */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'market_attribute_values.activity',
            true
        );
    }

    /** Только опубликованные */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where(
                'market_attribute_values.status',
                'published'
            )
            ->where(
                'market_attribute_values.activity',
                true
            )
            ->whereNotNull(
                'market_attribute_values.published_at'
            );
    }

    /** Только прошедшие модерацию */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where(
            'market_attribute_values.moderation_status',
            1
        );
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy(
                'market_attribute_values.sort',
                'asc'
            )
            ->orderByDesc(
                'market_attribute_values.id'
            );
    }

    /** Сортировка по параметру списка */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $query->orderBy(
                'market_attribute_values.id',
                'asc'
            ),

            'idDesc' => $query->orderBy(
                'market_attribute_values.id',
                'desc'
            ),

            'sortAsc' => $query
                ->orderBy(
                    'market_attribute_values.sort',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'sortDesc' => $query
                ->orderBy(
                    'market_attribute_values.sort',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'titleAsc' => $query
                ->leftJoin(
                    'market_attribute_value_translations as mavt_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'market_attribute_values.id',
                                '=',
                                'mavt_sort.market_attribute_value_id'
                            )
                            ->where(
                                'mavt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect('market_attribute_values.*')
                ->orderBy(
                    'mavt_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'titleDesc' => $query
                ->leftJoin(
                    'market_attribute_value_translations as mavt_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'market_attribute_values.id',
                                '=',
                                'mavt_sort.market_attribute_value_id'
                            )
                            ->where(
                                'mavt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect('market_attribute_values.*')
                ->orderBy(
                    'mavt_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'attributeTitleAsc' => $query
                ->leftJoin(
                    'market_attribute_translations as mat_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'market_attribute_values.market_attribute_id',
                                '=',
                                'mat_sort.market_attribute_id'
                            )
                            ->where(
                                'mat_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect('market_attribute_values.*')
                ->orderBy(
                    'mat_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'attributeTitleDesc' => $query
                ->leftJoin(
                    'market_attribute_translations as mat_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'market_attribute_values.market_attribute_id',
                                '=',
                                'mat_sort.market_attribute_id'
                            )
                            ->where(
                                'mat_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect('market_attribute_values.*')
                ->orderBy(
                    'mat_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            /**
             * variant_values_count не нужен
             * обычному Index и считается только
             * при соответствующей сортировке.
             */
            'variantValuesCountAsc' => $query
                ->withCount('variantValues')
                ->orderBy(
                    'variant_values_count',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'variantValuesCountDesc' => $query
                ->withCount('variantValues')
                ->orderBy(
                    'variant_values_count',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'codeAsc' => $query
                ->orderBy(
                    'market_attribute_values.code',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'codeDesc' => $query
                ->orderBy(
                    'market_attribute_values.code',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'colorAsc' => $query
                ->orderBy(
                    'market_attribute_values.color',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'colorDesc' => $query
                ->orderBy(
                    'market_attribute_values.color',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'activityAsc' => $query
                ->orderBy(
                    'market_attribute_values.activity',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'activityDesc' => $query
                ->orderBy(
                    'market_attribute_values.activity',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'activity' => $query
                ->where(
                    'market_attribute_values.activity',
                    true
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'inactive' => $query
                ->where(
                    'market_attribute_values.activity',
                    false
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'statusAsc' => $query
                ->orderBy(
                    'market_attribute_values.status',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'statusDesc' => $query
                ->orderBy(
                    'market_attribute_values.status',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'statusDraft' => $query
                ->where(
                    'market_attribute_values.status',
                    'draft'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'statusPublished' => $query
                ->where(
                    'market_attribute_values.status',
                    'published'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'statusArchived' => $query
                ->where(
                    'market_attribute_values.status',
                    'archived'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'publishedAtAsc' => $query
                ->orderBy(
                    'market_attribute_values.published_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'publishedAtDesc' => $query
                ->orderBy(
                    'market_attribute_values.published_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'showFromAtAsc' => $query
                ->orderBy(
                    'market_attribute_values.show_from_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'showFromAtDesc' => $query
                ->orderBy(
                    'market_attribute_values.show_from_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'showToAtAsc' => $query
                ->orderBy(
                    'market_attribute_values.show_to_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'showToAtDesc' => $query
                ->orderBy(
                    'market_attribute_values.show_to_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'createdAtAsc', 'dateAsc' => $query
                ->orderBy(
                    'market_attribute_values.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'createdAtDesc', 'dateDesc' => $query
                ->orderBy(
                    'market_attribute_values.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'updatedAtAsc' => $query
                ->orderBy(
                    'market_attribute_values.updated_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'updatedAtDesc' => $query
                ->orderBy(
                    'market_attribute_values.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'moderationPending' => $query
                ->where(
                    'market_attribute_values.moderation_status',
                    0
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'moderationApproved' => $query
                ->where(
                    'market_attribute_values.moderation_status',
                    1
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'moderationRejected' => $query
                ->where(
                    'market_attribute_values.moderation_status',
                    2
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'moderationStatusAsc' => $query
                ->orderBy(
                    'market_attribute_values.moderation_status',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            'moderationStatusDesc' => $query
                ->orderBy(
                    'market_attribute_values.moderation_status',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_values.id'
                ),

            default => $query->orderByDesc(
                'market_attribute_values.id'
            ),
        };
    }

    /** Попадает в окно показа */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull(
                    'market_attribute_values.show_from_at'
                )
                    ->orWhere(
                        'market_attribute_values.show_from_at',
                        '<=',
                        now()
                    );
            })
            ->where(function (Builder $q) {
                $q->whereNull(
                    'market_attribute_values.show_to_at'
                )
                    ->orWhere(
                        'market_attribute_values.show_to_at',
                        '>=',
                        now()
                    );
            });
    }

    /** Публичные значения */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /** Поиск */
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

        return $query->where(function (Builder $q) use ($term, $locale) {
            $q->where(
                'market_attribute_values.code',
                'like',
                "%{$term}%"
            )
                ->orWhere(
                    'market_attribute_values.color',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'market_attribute_values.status',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'market_attribute_values.moderation_note',
                    'like',
                    "%{$term}%"
                )

                /** Перевод значения */
                ->orWhereHas(
                    'translations',
                    function (Builder $tq) use ($term, $locale) {
                        $tq
                            ->where(
                                'locale',
                                $locale
                            )
                            ->where(function (Builder $sq) use ($term) {
                                $sq
                                    ->where(
                                        'title',
                                        'like',
                                        "%{$term}%"
                                    )
                                    ->orWhere(
                                        'subtitle',
                                        'like',
                                        "%{$term}%"
                                    )
                                    ->orWhere(
                                        'short',
                                        'like',
                                        "%{$term}%"
                                    )
                                    ->orWhere(
                                        'description',
                                        'like',
                                        "%{$term}%"
                                    );
                            });
                    }
                )

                /** Родительская характеристика */
                ->orWhereHas(
                    'attribute',
                    function (Builder $aq) use ($term, $locale) {
                        $aq
                            ->where(
                                'market_attributes.code',
                                'like',
                                "%{$term}%"
                            )
                            ->orWhere(
                                'market_attributes.type',
                                'like',
                                "%{$term}%"
                            )
                            ->orWhere(
                                'market_attributes.unit',
                                'like',
                                "%{$term}%"
                            )
                            ->orWhereHas(
                                'translations',
                                function (Builder $tq) use ($term, $locale) {
                                    $tq
                                        ->where(
                                            'locale',
                                            $locale
                                        )
                                        ->where(function (Builder $sq) use ($term) {
                                            $sq
                                                ->where(
                                                    'title',
                                                    'like',
                                                    "%{$term}%"
                                                )
                                                ->orWhere(
                                                    'subtitle',
                                                    'like',
                                                    "%{$term}%"
                                                )
                                                ->orWhere(
                                                    'short',
                                                    'like',
                                                    "%{$term}%"
                                                )
                                                ->orWhere(
                                                    'description',
                                                    'like',
                                                    "%{$term}%"
                                                );
                                        });
                                }
                            );
                    }
                )

                /** Модератор */
                ->orWhereHas(
                    'moderator',
                    function (Builder $mq) use ($term) {
                        $mq
                            ->where(
                                'name',
                                'like',
                                "%{$term}%"
                            )
                            ->orWhere(
                                'email',
                                'like',
                                "%{$term}%"
                            );
                    }
                );
        });
    }
}
