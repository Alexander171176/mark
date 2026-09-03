<?php

namespace App\Models\Admin\Market\MarketBrand;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketBrand extends Model
{
    use HasFactory;

    protected $table = 'market_brands';

    protected $fillable = [
        'user_id',
        'url',
        'website',
        'logo',
        'icon',
        'social_links',
        'sort',
        'activity',
        'left',
        'main',
        'right',
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
        'social_links' => 'array',
        'sort' => 'integer',
        'activity' => 'boolean',
        'left' => 'boolean',
        'main' => 'boolean',
        'right' => 'boolean',
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

    /* =========================================================
     | RELATIONS
     ========================================================= */

    /** Создатель бренда */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /** Модератор бренда */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'moderated_by'
        );
    }

    /** Переводы бренда */
    public function translations(): HasMany
    {
        return $this->hasMany(
            MarketBrandTranslation::class,
            'market_brand_id'
        );
    }

    /**
     * Текущий перевод бренда.
     *
     * Оставляем для публичной и внешней логики.
     * Admin Index использует translations,
     * отфильтрованные Controller по currentLocale.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(
            MarketBrandTranslation::class,
            'market_brand_id'
        )->where(
            'locale',
            app()->getLocale()
        );
    }

    /** Изображения бренда */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketBrandImage::class,
            'market_brand_has_images',
            'market_brand_id',
            'market_brand_image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /** Товары бренда */
    public function products(): HasMany
    {
        return $this->hasMany(
            MarketProduct::class,
            'market_brand_id'
        );
    }

    /* =========================================================
     | BASE SCOPES
     ========================================================= */

    /** Активные бренды */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'market_brands.activity',
            true
        );
    }

    /** Опубликованные бренды */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where(
                'market_brands.status',
                'published'
            )
            ->where(
                'market_brands.activity',
                true
            )
            ->whereNotNull(
                'market_brands.published_at'
            );
    }

    /** Одобренные бренды */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where(
            'market_brands.moderation_status',
            1
        );
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy(
                'market_brands.sort',
                'asc'
            )
            ->orderByDesc(
                'market_brands.id'
            );
    }

    /** Левая рекламная зона */
    public function scopeLeft(Builder $query): Builder
    {
        return $query->where(
            'market_brands.left',
            true
        );
    }

    /** Главная рекламная зона */
    public function scopeMain(Builder $query): Builder
    {
        return $query->where(
            'market_brands.main',
            true
        );
    }

    /** Правая рекламная зона */
    public function scopeRight(Builder $query): Builder
    {
        return $query->where(
            'market_brands.right',
            true
        );
    }

    /** Окно показа */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull(
                    'market_brands.show_from_at'
                )->orWhere(
                    'market_brands.show_from_at',
                    '<=',
                    now()
                );
            })
            ->where(function (Builder $q) {
                $q->whereNull(
                    'market_brands.show_to_at'
                )->orWhere(
                    'market_brands.show_to_at',
                    '>=',
                    now()
                );
            });
    }

    /** Публичные бренды */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /* =========================================================
     | SEARCH
     ========================================================= */

    /**
     * Поиск для Admin Index.
     *
     * Семантика должна совпадать
     * с frontend-поиском Index.vue.
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
                $q->where(
                    'market_brands.url',
                    'like',
                    "%{$term}%"
                )
                    ->orWhere(
                        'market_brands.website',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'market_brands.icon',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'market_brands.status',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'market_brands.moderation_note',
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

    /* =========================================================
     | SORTING
     ========================================================= */

    /** Сортировка по параметру */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale
            ?: app()->getLocale();

        return match ($sort) {
            /* ID */
            'idAsc' => $query->orderBy(
                'market_brands.id',
                'asc'
            ),

            'idDesc' => $query->orderBy(
                'market_brands.id',
                'desc'
            ),

            /* Порядок */
            'sortAsc' => $query
                ->orderBy(
                    'market_brands.sort',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'sortDesc' => $query
                ->orderBy(
                    'market_brands.sort',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Название */
            'titleAsc' => $query
                ->leftJoin(
                    'market_brand_translations as sort_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'sort_translations.market_brand_id',
                            '=',
                            'market_brands.id'
                        )->where(
                            'sort_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'market_brands.*'
                )
                ->orderBy(
                    'sort_translations.title',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'titleDesc' => $query
                ->leftJoin(
                    'market_brand_translations as sort_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'sort_translations.market_brand_id',
                            '=',
                            'market_brands.id'
                        )->where(
                            'sort_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'market_brands.*'
                )
                ->orderBy(
                    'sort_translations.title',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* URL */
            'urlAsc' => $query
                ->orderBy(
                    'market_brands.url',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'urlDesc' => $query
                ->orderBy(
                    'market_brands.url',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Website */
            'websiteAsc' => $query
                ->orderBy(
                    'market_brands.website',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'websiteDesc' => $query
                ->orderBy(
                    'market_brands.website',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Товары */
            'productsAsc' => $query
                ->withCount('products')
                ->orderBy(
                    'products_count',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'productsDesc' => $query
                ->withCount('products')
                ->orderBy(
                    'products_count',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Статус */
            'statusAsc' => $query
                ->orderBy(
                    'market_brands.status',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'statusDesc' => $query
                ->orderBy(
                    'market_brands.status',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'statusDraft' => $query
                ->where(
                    'market_brands.status',
                    'draft'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'statusPublished' => $query
                ->where(
                    'market_brands.status',
                    'published'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'statusArchived' => $query
                ->where(
                    'market_brands.status',
                    'archived'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Публикация */
            'publishedAtAsc' => $query
                ->orderBy(
                    'market_brands.published_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'publishedAtDesc' => $query
                ->orderBy(
                    'market_brands.published_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Начало показа */
            'showFromAtAsc' => $query
                ->orderBy(
                    'market_brands.show_from_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'showFromAtDesc' => $query
                ->orderBy(
                    'market_brands.show_from_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Окончание показа */
            'showToAtAsc' => $query
                ->orderBy(
                    'market_brands.show_to_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'showToAtDesc' => $query
                ->orderBy(
                    'market_brands.show_to_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Создание */
            'createdAtAsc',
            'dateAsc' => $query
                ->orderBy(
                    'market_brands.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'createdAtDesc',
            'dateDesc' => $query
                ->orderBy(
                    'market_brands.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Обновление */
            'updatedAtAsc' => $query
                ->orderBy(
                    'market_brands.updated_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'updatedAtDesc' => $query
                ->orderBy(
                    'market_brands.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Просмотры */
            'viewsAsc' => $query
                ->orderBy(
                    'market_brands.views',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'viewsDesc' => $query
                ->orderBy(
                    'market_brands.views',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /*
             * Изображения.
             *
             * images_count должен быть заранее
             * добавлен Controller через withCount('images').
             */
            'imagesAsc' => $query
                ->orderBy(
                    'images_count',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'imagesDesc' => $query
                ->orderBy(
                    'images_count',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Активность */
            'activityAsc' => $query
                ->orderBy(
                    'market_brands.activity',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'activityDesc' => $query
                ->orderBy(
                    'market_brands.activity',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'activity' => $query
                ->where(
                    'market_brands.activity',
                    true
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'inactive' => $query
                ->where(
                    'market_brands.activity',
                    false
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Левая зона */
            'leftAsc' => $query
                ->orderBy(
                    'market_brands.left',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'leftDesc' => $query
                ->orderBy(
                    'market_brands.left',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'left' => $query
                ->where(
                    'market_brands.left',
                    true
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'noLeft' => $query
                ->where(
                    'market_brands.left',
                    false
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Главная зона */
            'mainAsc' => $query
                ->orderBy(
                    'market_brands.main',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'mainDesc' => $query
                ->orderBy(
                    'market_brands.main',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'main' => $query
                ->where(
                    'market_brands.main',
                    true
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'noMain' => $query
                ->where(
                    'market_brands.main',
                    false
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Правая зона */
            'rightAsc' => $query
                ->orderBy(
                    'market_brands.right',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'rightDesc' => $query
                ->orderBy(
                    'market_brands.right',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'right' => $query
                ->where(
                    'market_brands.right',
                    true
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'noRight' => $query
                ->where(
                    'market_brands.right',
                    false
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Модерация */
            'moderationStatusAsc' => $query
                ->orderBy(
                    'market_brands.moderation_status',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'moderationStatusDesc' => $query
                ->orderBy(
                    'market_brands.moderation_status',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'moderationPending' => $query
                ->where(
                    'market_brands.moderation_status',
                    0
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'moderationApproved' => $query
                ->where(
                    'market_brands.moderation_status',
                    1
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'moderationRejected' => $query
                ->where(
                    'market_brands.moderation_status',
                    2
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            /* Владелец */
            'ownerNameAsc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'market_brands.user_id'
                )
                ->addSelect(
                    'market_brands.*'
                )
                ->orderBy(
                    'owner_sort.name',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'ownerNameDesc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'market_brands.user_id'
                )
                ->addSelect(
                    'market_brands.*'
                )
                ->orderBy(
                    'owner_sort.name',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'ownerEmailAsc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'market_brands.user_id'
                )
                ->addSelect(
                    'market_brands.*'
                )
                ->orderBy(
                    'owner_sort.email',
                    'asc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            'ownerEmailDesc' => $query
                ->leftJoin(
                    'users as owner_sort',
                    'owner_sort.id',
                    '=',
                    'market_brands.user_id'
                )
                ->addSelect(
                    'market_brands.*'
                )
                ->orderBy(
                    'owner_sort.email',
                    'desc'
                )
                ->orderByDesc(
                    'market_brands.id'
                ),

            default => $query->ordered(),
        };
    }
}
