<?php

namespace App\Models\Admin\Market\MarketTag;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketTag extends Model
{
    use HasFactory;

    protected $table = 'market_tags';

    protected $fillable = [
        'user_id',

        'url',
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

        'views',
    ];

    protected $casts = [
        'user_id' => 'integer',

        'sort' => 'integer',
        'activity' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',

        'published_at' => 'datetime',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',

        'moderated_at' => 'datetime',

        'views' => 'integer',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ===================== Relations ===================== */

    /** Создатель тега */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /** Модератор тега */
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
            MarketTagTranslation::class,
            'market_tag_id'
        );
    }

    /**
     * Текущий перевод.
     *
     * Relation оставляем для публичных и внешних сценариев.
     * Admin Index использует translations с фильтром currentLocale.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(
            MarketTagTranslation::class,
            'market_tag_id'
        )->where(
            'locale',
            app()->getLocale()
        );
    }

    /** Товары тега */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProduct::class,
            'market_product_has_tags',
            'market_tag_id',
            'market_product_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /* ===================== Scopes ===================== */

    /** Только активные */
    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'market_tags.activity',
            true
        );
    }

    /** Только опубликованные */
    public function scopePublished(
        Builder $query
    ): Builder {
        return $query
            ->where(
                'market_tags.status',
                'published'
            )
            ->where(
                'market_tags.activity',
                true
            )
            ->whereNotNull(
                'market_tags.published_at'
            );
    }

    /** Только прошедшие модерацию */
    public function scopeApproved(
        Builder $query
    ): Builder {
        return $query->where(
            'market_tags.moderation_status',
            1
        );
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderBy(
                'market_tags.sort',
                'asc'
            )
            ->orderByDesc(
                'market_tags.id'
            );
    }

    /** Сортировка по параметру списка */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale
            ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $query->orderBy(
                'market_tags.id',
                'asc'
            ),

            'idDesc' => $query->orderBy(
                'market_tags.id',
                'desc'
            ),

            'sortAsc' => $query
                ->orderBy(
                    'market_tags.sort',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'sortDesc' => $query
                ->orderBy(
                    'market_tags.sort',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'titleAsc' => $query
                ->leftJoin(
                    'market_tag_translations as sort_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'market_tags.id',
                            '=',
                            'sort_translations.market_tag_id'
                        )->where(
                            'sort_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect('market_tags.*')
                ->orderBy(
                    'sort_translations.title',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'titleDesc' => $query
                ->leftJoin(
                    'market_tag_translations as sort_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'market_tags.id',
                            '=',
                            'sort_translations.market_tag_id'
                        )->where(
                            'sort_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect('market_tags.*')
                ->orderBy(
                    'sort_translations.title',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'urlAsc' => $query
                ->orderBy(
                    'market_tags.url',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'urlDesc' => $query
                ->orderBy(
                    'market_tags.url',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'colorAsc' => $query
                ->orderBy(
                    'market_tags.color',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'colorDesc' => $query
                ->orderBy(
                    'market_tags.color',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'activityAsc' => $query
                ->orderBy(
                    'market_tags.activity',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'activityDesc' => $query
                ->orderBy(
                    'market_tags.activity',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'activity' => $query
                ->where(
                    'market_tags.activity',
                    true
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'inactive' => $query
                ->where(
                    'market_tags.activity',
                    false
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'viewsAsc' => $query
                ->orderBy(
                    'market_tags.views',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'viewsDesc' => $query
                ->orderBy(
                    'market_tags.views',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            /*
             * products_count не загружается постоянно.
             * Он добавляется только для конкретной сортировки.
             */
            'productsAsc' => $query
                ->withCount('products')
                ->orderBy(
                    'products_count',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'productsDesc' => $query
                ->withCount('products')
                ->orderBy(
                    'products_count',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'statusAsc' => $query
                ->orderBy(
                    'market_tags.status',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'statusDesc' => $query
                ->orderBy(
                    'market_tags.status',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'statusDraft' => $query
                ->where(
                    'market_tags.status',
                    'draft'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'statusPublished' => $query
                ->where(
                    'market_tags.status',
                    'published'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'statusArchived' => $query
                ->where(
                    'market_tags.status',
                    'archived'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'publishedAtAsc' => $query
                ->orderBy(
                    'market_tags.published_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'publishedAtDesc' => $query
                ->orderBy(
                    'market_tags.published_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'showFromAtAsc' => $query
                ->orderBy(
                    'market_tags.show_from_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'showFromAtDesc' => $query
                ->orderBy(
                    'market_tags.show_from_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'showToAtAsc' => $query
                ->orderBy(
                    'market_tags.show_to_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'showToAtDesc' => $query
                ->orderBy(
                    'market_tags.show_to_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'createdAtAsc',
            'dateAsc' => $query
                ->orderBy(
                    'market_tags.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'createdAtDesc',
            'dateDesc' => $query
                ->orderBy(
                    'market_tags.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'updatedAtAsc' => $query
                ->orderBy(
                    'market_tags.updated_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'updatedAtDesc' => $query
                ->orderBy(
                    'market_tags.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'moderationPending' => $query
                ->where(
                    'market_tags.moderation_status',
                    0
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'moderationApproved' => $query
                ->where(
                    'market_tags.moderation_status',
                    1
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'moderationRejected' => $query
                ->where(
                    'market_tags.moderation_status',
                    2
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'moderationStatusAsc' => $query
                ->orderBy(
                    'market_tags.moderation_status',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'moderationStatusDesc' => $query
                ->orderBy(
                    'market_tags.moderation_status',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'ownerNameAsc' => $query
                ->leftJoin(
                    'users as sort_users',
                    'market_tags.user_id',
                    '=',
                    'sort_users.id'
                )
                ->addSelect('market_tags.*')
                ->orderBy(
                    'sort_users.name',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'ownerNameDesc' => $query
                ->leftJoin(
                    'users as sort_users',
                    'market_tags.user_id',
                    '=',
                    'sort_users.id'
                )
                ->addSelect('market_tags.*')
                ->orderBy(
                    'sort_users.name',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'ownerEmailAsc' => $query
                ->leftJoin(
                    'users as sort_users',
                    'market_tags.user_id',
                    '=',
                    'sort_users.id'
                )
                ->addSelect('market_tags.*')
                ->orderBy(
                    'sort_users.email',
                    'asc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            'ownerEmailDesc' => $query
                ->leftJoin(
                    'users as sort_users',
                    'market_tags.user_id',
                    '=',
                    'sort_users.id'
                )
                ->addSelect('market_tags.*')
                ->orderBy(
                    'sort_users.email',
                    'desc'
                )
                ->orderByDesc(
                    'market_tags.id'
                ),

            default => $query->ordered(),
        };
    }

    /** Попадает в окно показа */
    public function scopeInShowWindow(
        Builder $query
    ): Builder {
        return $query
            ->where(
                function (Builder $q) {
                    $q->whereNull(
                        'market_tags.show_from_at'
                    )->orWhere(
                        'market_tags.show_from_at',
                        '<=',
                        now()
                    );
                }
            )
            ->where(
                function (Builder $q) {
                    $q->whereNull(
                        'market_tags.show_to_at'
                    )->orWhere(
                        'market_tags.show_to_at',
                        '>=',
                        now()
                    );
                }
            );
    }

    /** Публичные теги */
    public function scopeForPublic(
        Builder $query
    ): Builder {
        return $query
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /**
     * Поиск для Admin Index.
     *
     * Семантика должна совпадать
     * с локальным поиском Index.vue.
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

        $locale = $locale
            ?: app()->getLocale();

        return $query->where(
            function (Builder $q) use (
                $term,
                $locale
            ) {
                $q->where(
                    'market_tags.url',
                    'like',
                    "%{$term}%"
                )
                    ->orWhere(
                        'market_tags.icon',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'market_tags.color',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'market_tags.status',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'market_tags.moderation_note',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhereHas(
                        'translations',
                        function (Builder $translationQuery) use (
                            $term,
                            $locale
                        ) {
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (Builder $searchQuery) use ($term) {
                                        $searchQuery
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
                                    }
                                );
                        }
                    )
                    ->orWhereHas(
                        'owner',
                        function (Builder $ownerQuery) use ($term) {
                            $ownerQuery
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
            }
        );
    }
}
