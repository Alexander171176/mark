<?php

namespace App\Models\Admin\Market\MarketShop;

use App\Models\Admin\Market\MarketCompany\MarketCompany;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketShop extends Model
{
    use HasFactory;

    protected $table = 'market_shops';

    protected $fillable = [
        'market_company_id',
        'user_id',
        'url',
        'email',
        'phone',
        'logo',
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
        'market_company_id' => 'integer',
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

    /** Компания-владелец магазина */
    public function company(): BelongsTo
    {
        return $this->belongsTo(MarketCompany::class, 'market_company_id');
    }

    /** Товары магазина */
    public function products(): HasMany
    {
        return $this->hasMany(MarketProduct::class, 'market_shop_id');
    }

    /** Создатель магазина */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Модератор магазина */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Переводы магазина */
    public function translations(): HasMany
    {
        return $this->hasMany(MarketShopTranslation::class, 'market_shop_id');
    }

    /** Текущий перевод магазина */
    public function translation(): HasOne
    {
        return $this->hasOne(MarketShopTranslation::class, 'market_shop_id')
            ->where('locale', app()->getLocale());
    }

    /** Изображения магазина */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketShopImage::class,
            'market_shop_has_images',
            'market_shop_id',
            'market_shop_image_id'
        )->withPivot('order')
            ->orderByPivot('order');
    }

    /** Активные магазины */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('market_shops.activity', true);
    }

    /** Опубликованные магазины */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('market_shops.status', 'published')
            ->where('market_shops.activity', true)
            ->whereNotNull('market_shops.published_at');
    }

    /** Одобренные магазины */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('market_shops.moderation_status', 1);
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('market_shops.sort')
            ->orderByDesc('market_shops.id');
    }

    /** Левая колонка */
    public function scopeLeft(Builder $query): Builder
    {
        return $query->where('market_shops.left', true);
    }

    /** Главная зона */
    public function scopeMain(Builder $query): Builder
    {
        return $query->where('market_shops.main', true);
    }

    /** Правая колонка */
    public function scopeRight(Builder $query): Builder
    {
        return $query->where('market_shops.right', true);
    }

    /** Магазин компании */
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('market_shops.market_company_id', $companyId);
    }

    /** Окно показа */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull('market_shops.show_from_at')
                    ->orWhere('market_shops.show_from_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('market_shops.show_to_at')
                    ->orWhere('market_shops.show_to_at', '>=', now());
            });
    }

    /** Публичные магазины */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /**
     * Поиск по магазину, переводу текущей локали,
     * владельцу и компании.
     */
    public function scopeSearch(Builder $query, ?string $term, ?string $locale = null): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();

        return $query->where(function (Builder $q) use ($term, $locale) {
            $q->where('market_shops.url', 'like', "%{$term}%")
                ->orWhere('market_shops.email', 'like', "%{$term}%")
                ->orWhere('market_shops.phone', 'like', "%{$term}%")
                ->orWhere('market_shops.status', 'like', "%{$term}%")
                ->orWhere('market_shops.moderation_note', 'like', "%{$term}%")

                ->orWhereHas('translations', function (Builder $translationQuery) use ($term, $locale) {
                    $translationQuery
                        ->where('locale', $locale)
                        ->where(function (Builder $q) use ($term) {
                            $q->where('title', 'like', "%{$term}%")
                                ->orWhere('subtitle', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%");
                        });
                })

                ->orWhereHas('owner', function (Builder $ownerQuery) use ($term) {
                    $ownerQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                })

                ->orWhereHas('company', function (Builder $companyQuery) use ($term, $locale) {
                    $companyQuery
                        ->where(function (Builder $q) use ($term) {
                            $q->where('url', 'like', "%{$term}%")
                                ->orWhere('legal_name', 'like', "%{$term}%")
                                ->orWhere('email', 'like', "%{$term}%")
                                ->orWhere('phone', 'like', "%{$term}%");
                        })
                        ->orWhereHas('translations', function (Builder $translationQuery) use ($term, $locale) {
                            $translationQuery
                                ->where('locale', $locale)
                                ->where('title', 'like', "%{$term}%");
                        });
                });
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $query, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $query->orderBy('market_shops.id', 'asc'),
            'idDesc' => $query->orderBy('market_shops.id', 'desc'),

            'sortAsc' => $query
                ->orderBy('market_shops.sort', 'asc')
                ->orderByDesc('market_shops.id'),

            'sortDesc' => $query
                ->orderBy('market_shops.sort', 'desc')
                ->orderByDesc('market_shops.id'),

            'titleAsc' => $query
                ->leftJoin('market_shop_translations as mst_sort', function ($join) use ($locale) {
                    $join->on('mst_sort.market_shop_id', '=', 'market_shops.id')
                        ->where('mst_sort.locale', '=', $locale);
                })
                ->orderBy('mst_sort.title', 'asc')
                ->orderByDesc('market_shops.id')
                ->addSelect('market_shops.*'),

            'titleDesc' => $query
                ->leftJoin('market_shop_translations as mst_sort', function ($join) use ($locale) {
                    $join->on('mst_sort.market_shop_id', '=', 'market_shops.id')
                        ->where('mst_sort.locale', '=', $locale);
                })
                ->orderBy('mst_sort.title', 'desc')
                ->orderByDesc('market_shops.id')
                ->addSelect('market_shops.*'),

            'urlAsc' => $query
                ->orderBy('market_shops.url', 'asc')
                ->orderByDesc('market_shops.id'),

            'urlDesc' => $query
                ->orderBy('market_shops.url', 'desc')
                ->orderByDesc('market_shops.id'),

            'emailAsc' => $query
                ->orderBy('market_shops.email', 'asc')
                ->orderByDesc('market_shops.id'),

            'emailDesc' => $query
                ->orderBy('market_shops.email', 'desc')
                ->orderByDesc('market_shops.id'),

            'phoneAsc' => $query
                ->orderBy('market_shops.phone', 'asc')
                ->orderByDesc('market_shops.id'),

            'phoneDesc' => $query
                ->orderBy('market_shops.phone', 'desc')
                ->orderByDesc('market_shops.id'),

            'statusAsc' => $query
                ->orderBy('market_shops.status', 'asc')
                ->orderByDesc('market_shops.id'),

            'statusDesc' => $query
                ->orderBy('market_shops.status', 'desc')
                ->orderByDesc('market_shops.id'),

            'statusDraft' => $query
                ->where('market_shops.status', 'draft')
                ->orderByDesc('market_shops.id'),

            'statusPublished' => $query
                ->where('market_shops.status', 'published')
                ->orderByDesc('market_shops.id'),

            'statusArchived' => $query
                ->where('market_shops.status', 'archived')
                ->orderByDesc('market_shops.id'),

            'publishedAtAsc' => $query
                ->orderBy('market_shops.published_at', 'asc')
                ->orderByDesc('market_shops.id'),

            'publishedAtDesc' => $query
                ->orderBy('market_shops.published_at', 'desc')
                ->orderByDesc('market_shops.id'),

            'showFromAtAsc' => $query
                ->orderBy('market_shops.show_from_at', 'asc')
                ->orderByDesc('market_shops.id'),

            'showFromAtDesc' => $query
                ->orderBy('market_shops.show_from_at', 'desc')
                ->orderByDesc('market_shops.id'),

            'showToAtAsc' => $query
                ->orderBy('market_shops.show_to_at', 'asc')
                ->orderByDesc('market_shops.id'),

            'showToAtDesc' => $query
                ->orderBy('market_shops.show_to_at', 'desc')
                ->orderByDesc('market_shops.id'),

            'createdAtAsc', 'dateAsc' => $query
                ->orderBy('market_shops.created_at', 'asc')
                ->orderByDesc('market_shops.id'),

            'createdAtDesc', 'dateDesc' => $query
                ->orderBy('market_shops.created_at', 'desc')
                ->orderByDesc('market_shops.id'),

            'updatedAtAsc' => $query
                ->orderBy('market_shops.updated_at', 'asc')
                ->orderByDesc('market_shops.id'),

            'updatedAtDesc' => $query
                ->orderBy('market_shops.updated_at', 'desc')
                ->orderByDesc('market_shops.id'),

            'viewsAsc' => $query
                ->orderBy('market_shops.views', 'asc')
                ->orderByDesc('market_shops.id'),

            'viewsDesc', 'views' => $query
                ->orderBy('market_shops.views', 'desc')
                ->orderByDesc('market_shops.id'),

            /**
             * images_count загружается Controller через withCount('images').
             */
            'imagesAsc' => $query
                ->orderBy('images_count', 'asc')
                ->orderByDesc('market_shops.id'),

            'imagesDesc' => $query
                ->orderBy('images_count', 'desc')
                ->orderByDesc('market_shops.id'),

            /**
             * products_count используется только при явной
             * сортировке по количеству товаров.
             */
            'productsAsc' => $query
                ->withCount('products')
                ->orderBy('products_count', 'asc')
                ->orderByDesc('market_shops.id'),

            'productsDesc' => $query
                ->withCount('products')
                ->orderBy('products_count', 'desc')
                ->orderByDesc('market_shops.id'),

            'activityAsc' => $query
                ->orderBy('market_shops.activity', 'asc')
                ->orderByDesc('market_shops.id'),

            'activityDesc' => $query
                ->orderBy('market_shops.activity', 'desc')
                ->orderByDesc('market_shops.id'),

            'activity' => $query
                ->where('market_shops.activity', true)
                ->orderByDesc('market_shops.id'),

            'inactive' => $query
                ->where('market_shops.activity', false)
                ->orderByDesc('market_shops.id'),

            'leftAsc' => $query
                ->orderBy('market_shops.left', 'asc')
                ->orderByDesc('market_shops.id'),

            'leftDesc' => $query
                ->orderBy('market_shops.left', 'desc')
                ->orderByDesc('market_shops.id'),

            'left' => $query
                ->where('market_shops.left', true)
                ->orderByDesc('market_shops.id'),

            'noLeft' => $query
                ->where('market_shops.left', false)
                ->orderByDesc('market_shops.id'),

            'mainAsc' => $query
                ->orderBy('market_shops.main', 'asc')
                ->orderByDesc('market_shops.id'),

            'mainDesc' => $query
                ->orderBy('market_shops.main', 'desc')
                ->orderByDesc('market_shops.id'),

            'main' => $query
                ->where('market_shops.main', true)
                ->orderByDesc('market_shops.id'),

            'noMain' => $query
                ->where('market_shops.main', false)
                ->orderByDesc('market_shops.id'),

            'rightAsc' => $query
                ->orderBy('market_shops.right', 'asc')
                ->orderByDesc('market_shops.id'),

            'rightDesc' => $query
                ->orderBy('market_shops.right', 'desc')
                ->orderByDesc('market_shops.id'),

            'right' => $query
                ->where('market_shops.right', true)
                ->orderByDesc('market_shops.id'),

            'noRight' => $query
                ->where('market_shops.right', false)
                ->orderByDesc('market_shops.id'),

            'moderationStatusAsc' => $query
                ->orderBy('market_shops.moderation_status', 'asc')
                ->orderByDesc('market_shops.id'),

            'moderationStatusDesc' => $query
                ->orderBy('market_shops.moderation_status', 'desc')
                ->orderByDesc('market_shops.id'),

            'moderationPending' => $query
                ->where('market_shops.moderation_status', 0)
                ->orderByDesc('market_shops.id'),

            'moderationApproved' => $query
                ->where('market_shops.moderation_status', 1)
                ->orderByDesc('market_shops.id'),

            'moderationRejected' => $query
                ->where('market_shops.moderation_status', 2)
                ->orderByDesc('market_shops.id'),

            'ownerNameAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_shops.user_id')
                ->orderBy('owner_sort.name', 'asc')
                ->orderByDesc('market_shops.id')
                ->addSelect('market_shops.*'),

            'ownerNameDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_shops.user_id')
                ->orderBy('owner_sort.name', 'desc')
                ->orderByDesc('market_shops.id')
                ->addSelect('market_shops.*'),

            'ownerEmailAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_shops.user_id')
                ->orderBy('owner_sort.email', 'asc')
                ->orderByDesc('market_shops.id')
                ->addSelect('market_shops.*'),

            'ownerEmailDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_shops.user_id')
                ->orderBy('owner_sort.email', 'desc')
                ->orderByDesc('market_shops.id')
                ->addSelect('market_shops.*'),

            'companyLegalNameAsc' => $query
                ->leftJoin(
                    'market_companies as company_sort',
                    'company_sort.id',
                    '=',
                    'market_shops.market_company_id'
                )
                ->orderBy('company_sort.legal_name', 'asc')
                ->orderByDesc('market_shops.id')
                ->addSelect('market_shops.*'),

            'companyLegalNameDesc' => $query
                ->leftJoin(
                    'market_companies as company_sort',
                    'company_sort.id',
                    '=',
                    'market_shops.market_company_id'
                )
                ->orderBy('company_sort.legal_name', 'desc')
                ->orderByDesc('market_shops.id')
                ->addSelect('market_shops.*'),

            default => $query->ordered(),
        };
    }
}
