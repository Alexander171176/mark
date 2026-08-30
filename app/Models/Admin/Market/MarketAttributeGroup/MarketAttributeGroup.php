<?php

namespace App\Models\Admin\Market\MarketAttributeGroup;

use App\Models\Admin\Market\MarketAttribute\MarketAttribute;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketAttributeGroup extends Model
{
    use HasFactory;

    protected $table = 'market_attribute_groups';

    protected $fillable = [
        'user_id',

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
        'user_id' => 'integer',
        'moderated_by' => 'integer',

        'sort' => 'integer',
        'activity' => 'boolean',

        'moderation_status' => 'integer',

        'published_at' => 'datetime',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',
        'moderated_at' => 'datetime',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Создатель группы */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /** Модератор группы */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'moderated_by'
        );
    }

    /** Все переводы группы */
    public function translations(): HasMany
    {
        return $this->hasMany(
            MarketAttributeGroupTranslation::class,
            'market_attribute_group_id'
        );
    }

    /**
     * Перевод текущей локали.
     *
     * В Admin Index relation не используется:
     * Controller самостоятельно загружает translations,
     * ограниченные currentLocale.
     *
     * Relation сохраняется для других частей приложения,
     * где может потребоваться прямой current-locale relation.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(
            MarketAttributeGroupTranslation::class,
            'market_attribute_group_id'
        )->where(
            'locale',
            app()->getLocale()
        );
    }

    /** Характеристики группы */
    public function attributes(): HasMany
    {
        return $this->hasMany(
            MarketAttribute::class,
            'market_attribute_group_id'
        );
    }

    /* ======================== Base scopes ======================== */

    /** Только активные группы */
    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'market_attribute_groups.activity',
            true
        );
    }

    /** Только опубликованные группы */
    public function scopePublished(
        Builder $query
    ): Builder {
        return $query
            ->where(
                'market_attribute_groups.status',
                'published'
            )
            ->where(
                'market_attribute_groups.activity',
                true
            )
            ->whereNotNull(
                'market_attribute_groups.published_at'
            );
    }

    /** Только прошедшие модерацию группы */
    public function scopeApproved(
        Builder $query
    ): Builder {
        return $query->where(
            'market_attribute_groups.moderation_status',
            1
        );
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderBy(
                'market_attribute_groups.sort',
                'asc'
            )
            ->orderByDesc(
                'market_attribute_groups.id'
            );
    }

    /* ======================== Sorting ======================== */

    /**
     * Сортировка и фильтрация групп характеристик
     * по параметру Admin Index.
     *
     * Для title используется перевод
     * исключительно указанной locale.
     *
     * attributes_count должен быть заранее
     * добавлен через withCount('attributes')
     * в Index Controller.
     */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale
            ?: app()->getLocale();

        return match ($sort) {
            /** ID */
            'idAsc' =>
            $query->orderBy(
                'market_attribute_groups.id',
                'asc'
            ),

            'idDesc' =>
            $query->orderBy(
                'market_attribute_groups.id',
                'desc'
            ),

            /** Sort */
            'sortAsc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.sort',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'sortDesc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.sort',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Title текущей локали */
            'titleAsc' =>
            $query
                ->leftJoin(
                    'market_attribute_group_translations as magt_sort',
                    function ($join) use ($locale): void {
                        $join
                            ->on(
                                'magt_sort.market_attribute_group_id',
                                '=',
                                'market_attribute_groups.id'
                            )
                            ->where(
                                'magt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'market_attribute_groups.*'
                )
                ->orderBy(
                    'magt_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'titleDesc' =>
            $query
                ->leftJoin(
                    'market_attribute_group_translations as magt_sort',
                    function ($join) use ($locale): void {
                        $join
                            ->on(
                                'magt_sort.market_attribute_group_id',
                                '=',
                                'market_attribute_groups.id'
                            )
                            ->where(
                                'magt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect(
                    'market_attribute_groups.*'
                )
                ->orderBy(
                    'magt_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Code */
            'codeAsc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.code',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'codeDesc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.code',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Color */
            'colorAsc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.color',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'colorDesc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.color',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /**
             * Количество характеристик.
             *
             * attributes_count уже должен присутствовать
             * в query через Index Controller.
             */
            'attributesCountAsc' =>
            $query
                ->orderBy(
                    'attributes_count',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'attributesCountDesc' =>
            $query
                ->orderBy(
                    'attributes_count',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Activity */
            'activityAsc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.activity',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'activityDesc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.activity',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'activity' =>
            $query
                ->where(
                    'market_attribute_groups.activity',
                    true
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'inactive' =>
            $query
                ->where(
                    'market_attribute_groups.activity',
                    false
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Status */
            'statusAsc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.status',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'statusDesc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.status',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'statusDraft' =>
            $query
                ->where(
                    'market_attribute_groups.status',
                    'draft'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'statusPublished' =>
            $query
                ->where(
                    'market_attribute_groups.status',
                    'published'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'statusArchived' =>
            $query
                ->where(
                    'market_attribute_groups.status',
                    'archived'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Published at */
            'publishedAtAsc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.published_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'publishedAtDesc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.published_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Show from */
            'showFromAtAsc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.show_from_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'showFromAtDesc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.show_from_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Show to */
            'showToAtAsc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.show_to_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'showToAtDesc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.show_to_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Created at */
            'createdAtAsc', 'dateAsc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'createdAtDesc', 'dateDesc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Updated at */
            'updatedAtAsc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.updated_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'updatedAtDesc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Moderation */
            'moderationPending' =>
            $query
                ->where(
                    'market_attribute_groups.moderation_status',
                    0
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'moderationApproved' =>
            $query
                ->where(
                    'market_attribute_groups.moderation_status',
                    1
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'moderationRejected' =>
            $query
                ->where(
                    'market_attribute_groups.moderation_status',
                    2
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'moderationStatusAsc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.moderation_status',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'moderationStatusDesc' =>
            $query
                ->orderBy(
                    'market_attribute_groups.moderation_status',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Owner name */
            'ownerNameAsc' =>
            $query
                ->leftJoin(
                    'users as owner_sort',
                    'market_attribute_groups.user_id',
                    '=',
                    'owner_sort.id'
                )
                ->addSelect(
                    'market_attribute_groups.*'
                )
                ->orderBy(
                    'owner_sort.name',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'ownerNameDesc' =>
            $query
                ->leftJoin(
                    'users as owner_sort',
                    'market_attribute_groups.user_id',
                    '=',
                    'owner_sort.id'
                )
                ->addSelect(
                    'market_attribute_groups.*'
                )
                ->orderBy(
                    'owner_sort.name',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Owner email */
            'ownerEmailAsc' =>
            $query
                ->leftJoin(
                    'users as owner_sort',
                    'market_attribute_groups.user_id',
                    '=',
                    'owner_sort.id'
                )
                ->addSelect(
                    'market_attribute_groups.*'
                )
                ->orderBy(
                    'owner_sort.email',
                    'asc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            'ownerEmailDesc' =>
            $query
                ->leftJoin(
                    'users as owner_sort',
                    'market_attribute_groups.user_id',
                    '=',
                    'owner_sort.id'
                )
                ->addSelect(
                    'market_attribute_groups.*'
                )
                ->orderBy(
                    'owner_sort.email',
                    'desc'
                )
                ->orderByDesc(
                    'market_attribute_groups.id'
                ),

            /** Default */
            default =>
            $query->orderByDesc(
                'market_attribute_groups.id'
            ),
        };
    }

    /* ======================== Publication ======================== */

    /** Попадает в текущее окно показа */
    public function scopeInShowWindow(
        Builder $query
    ): Builder {
        return $query
            ->where(
                function (Builder $query): void {
                    $query
                        ->whereNull(
                            'market_attribute_groups.show_from_at'
                        )
                        ->orWhere(
                            'market_attribute_groups.show_from_at',
                            '<=',
                            now()
                        );
                }
            )
            ->where(
                function (Builder $query): void {
                    $query
                        ->whereNull(
                            'market_attribute_groups.show_to_at'
                        )
                        ->orWhere(
                            'market_attribute_groups.show_to_at',
                            '>=',
                            now()
                        );
                }
            );
    }

    /** Публичные группы */
    public function scopeForPublic(
        Builder $query
    ): Builder {
        return $query
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /* ======================== Search ======================== */

    /**
     * Поиск групп характеристик.
     *
     * Семантика должна совпадать
     * с frontend Index:
     * - code;
     * - icon;
     * - color;
     * - status;
     * - moderation_note;
     * - title текущего перевода;
     * - subtitle текущего перевода;
     * - short текущего перевода;
     * - имя владельца;
     * - email владельца.
     *
     * Для переводимых полей используется
     * исключительно указанная locale.
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

        $like = "%{$term}%";

        return $query->where(
            function (
                Builder $query
            ) use (
                $like,
                $locale
            ): void {
                $query
                    ->where(
                        'market_attribute_groups.code',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_attribute_groups.icon',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_attribute_groups.color',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_attribute_groups.status',
                        'like',
                        $like
                    )
                    ->orWhere(
                        'market_attribute_groups.moderation_note',
                        'like',
                        $like
                    )

                    /** Перевод текущей локали */
                    ->orWhereHas(
                        'translations',
                        function (
                            Builder $translationQuery
                        ) use (
                            $locale,
                            $like
                        ): void {
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (
                                        Builder $query
                                    ) use (
                                        $like
                                    ): void {
                                        $query
                                            ->where(
                                                'title',
                                                'like',
                                                $like
                                            )
                                            ->orWhere(
                                                'subtitle',
                                                'like',
                                                $like
                                            )
                                            ->orWhere(
                                                'short',
                                                'like',
                                                $like
                                            );
                                    }
                                );
                        }
                    )

                    /** Владелец */
                    ->orWhereHas(
                        'owner',
                        function (
                            Builder $ownerQuery
                        ) use (
                            $like
                        ): void {
                            $ownerQuery
                                ->where(
                                    'name',
                                    'like',
                                    $like
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    $like
                                );
                        }
                    );
            }
        );
    }
}
