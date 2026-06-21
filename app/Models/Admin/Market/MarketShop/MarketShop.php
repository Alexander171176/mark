<?php

namespace App\Models\Admin\Market\MarketShop;

use App\Models\Admin\Market\MarketCompany\MarketCompany;
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
        return $query->where('activity', true);
    }

    /** Опубликованные магазины */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('activity', true)
            ->whereNotNull('published_at');
    }

    /** Одобренные магазины */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 1);
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderByDesc('id');
    }

    /** Левая колонка */
    public function scopeLeft(Builder $query): Builder
    {
        return $query->where('left', true);
    }

    /** Главная зона */
    public function scopeMain(Builder $query): Builder
    {
        return $query->where('main', true);
    }

    /** Правая колонка */
    public function scopeRight(Builder $query): Builder
    {
        return $query->where('right', true);
    }

    /** Магазин компании */
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('market_company_id', $companyId);
    }

    /** Окно показа */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull('show_from_at')
                    ->orWhere('show_from_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('show_to_at')
                    ->orWhere('show_to_at', '>=', now());
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

    /** Поиск по магазину, переводам, владельцу и компании */
    public function scopeSearch(Builder $query, ?string $term, ?string $locale = null): Builder
    {
        if (!$term) {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();

        return $query->where(function (Builder $q) use ($term, $locale) {
            $q->where('url', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('status', 'like', "%{$term}%")
                ->orWhere('moderation_note', 'like', "%{$term}%")

                ->orWhereHas('translations', function (Builder $tq) use ($term, $locale) {
                    $tq->where('locale', $locale)
                        ->where(function (Builder $sq) use ($term) {
                            $sq->where('title', 'like', "%{$term}%")
                                ->orWhere('subtitle', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%")
                                ->orWhere('meta_title', 'like', "%{$term}%")
                                ->orWhere('meta_keywords', 'like', "%{$term}%")
                                ->orWhere('meta_desc', 'like', "%{$term}%");
                        });
                })

                ->orWhereHas('owner', function (Builder $oq) use ($term) {
                    $oq->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                })

                ->orWhereHas('company', function (Builder $cq) use ($term) {
                    $cq->where('url', 'like', "%{$term}%")
                        ->orWhere('legal_name', 'like', "%{$term}%")
                        ->orWhere('bin_iin', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%");
                });
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $query, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $query->orderBy('id', 'asc'),
            'idDesc' => $query->orderBy('id', 'desc'),

            'sortAsc' => $query->orderBy('sort', 'asc')->orderBy('id', 'asc'),
            'sortDesc' => $query->orderBy('sort', 'desc')->orderBy('id', 'desc'),

            'titleAsc' => $query
                ->leftJoin('market_shop_translations as mst_sort', function ($join) use ($locale) {
                    $join->on('mst_sort.market_shop_id', '=', 'market_shops.id')
                        ->where('mst_sort.locale', '=', $locale);
                })
                ->orderBy('mst_sort.title', 'asc')
                ->orderByDesc('market_shops.id')
                ->select('market_shops.*'),

            'titleDesc' => $query
                ->leftJoin('market_shop_translations as mst_sort', function ($join) use ($locale) {
                    $join->on('mst_sort.market_shop_id', '=', 'market_shops.id')
                        ->where('mst_sort.locale', '=', $locale);
                })
                ->orderBy('mst_sort.title', 'desc')
                ->orderByDesc('market_shops.id')
                ->select('market_shops.*'),

            'urlAsc' => $query->orderBy('url', 'asc')->orderByDesc('id'),
            'urlDesc' => $query->orderBy('url', 'desc')->orderByDesc('id'),

            'emailAsc' => $query->orderBy('email', 'asc')->orderByDesc('id'),
            'emailDesc' => $query->orderBy('email', 'desc')->orderByDesc('id'),

            'phoneAsc' => $query->orderBy('phone', 'asc')->orderByDesc('id'),
            'phoneDesc' => $query->orderBy('phone', 'desc')->orderByDesc('id'),

            'statusAsc' => $query->orderBy('status', 'asc')->orderByDesc('id'),
            'statusDesc' => $query->orderBy('status', 'desc')->orderByDesc('id'),
            'statusDraft' => $query->where('status', 'draft')->orderByDesc('id'),
            'statusPublished' => $query->where('status', 'published')->orderByDesc('id'),
            'statusArchived' => $query->where('status', 'archived')->orderByDesc('id'),

            'publishedAtAsc' => $query->orderBy('published_at', 'asc')->orderByDesc('id'),
            'publishedAtDesc' => $query->orderBy('published_at', 'desc')->orderByDesc('id'),

            'showFromAtAsc' => $query->orderBy('show_from_at', 'asc')->orderByDesc('id'),
            'showFromAtDesc' => $query->orderBy('show_from_at', 'desc')->orderByDesc('id'),

            'showToAtAsc' => $query->orderBy('show_to_at', 'asc')->orderByDesc('id'),
            'showToAtDesc' => $query->orderBy('show_to_at', 'desc')->orderByDesc('id'),

            'createdAtAsc', 'dateAsc' => $query->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc', 'dateDesc' => $query->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $query->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $query->orderBy('updated_at', 'desc')->orderByDesc('id'),

            'viewsAsc' => $query->orderBy('views', 'asc')->orderByDesc('id'),
            'viewsDesc' => $query->orderBy('views', 'desc')->orderByDesc('id'),

            'imagesAsc' => $query->withCount('images')->orderBy('images_count', 'asc')->orderByDesc('id'),
            'imagesDesc' => $query->withCount('images')->orderBy('images_count', 'desc')->orderByDesc('id'),

            'activityAsc' => $query->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $query->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $query->where('activity', true)->orderByDesc('id'),
            'inactive' => $query->where('activity', false)->orderByDesc('id'),

            'leftAsc' => $query->orderBy('left', 'asc')->orderByDesc('id'),
            'leftDesc' => $query->orderBy('left', 'desc')->orderByDesc('id'),
            'left' => $query->where('left', true)->orderByDesc('id'),
            'noLeft' => $query->where('left', false)->orderByDesc('id'),

            'mainAsc' => $query->orderBy('main', 'asc')->orderByDesc('id'),
            'mainDesc' => $query->orderBy('main', 'desc')->orderByDesc('id'),
            'main' => $query->where('main', true)->orderByDesc('id'),
            'noMain' => $query->where('main', false)->orderByDesc('id'),

            'rightAsc' => $query->orderBy('right', 'asc')->orderByDesc('id'),
            'rightDesc' => $query->orderBy('right', 'desc')->orderByDesc('id'),
            'right' => $query->where('right', true)->orderByDesc('id'),
            'noRight' => $query->where('right', false)->orderByDesc('id'),

            'moderationStatusAsc' => $query->orderBy('moderation_status', 'asc')->orderByDesc('id'),
            'moderationStatusDesc' => $query->orderBy('moderation_status', 'desc')->orderByDesc('id'),
            'moderationPending' => $query->where('moderation_status', 0)->orderByDesc('id'),
            'moderationApproved' => $query->where('moderation_status', 1)->orderByDesc('id'),
            'moderationRejected' => $query->where('moderation_status', 2)->orderByDesc('id'),

            'ownerNameAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_shops.user_id')
                ->orderBy('owner_sort.name', 'asc')
                ->orderByDesc('market_shops.id')
                ->select('market_shops.*'),

            'ownerNameDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_shops.user_id')
                ->orderBy('owner_sort.name', 'desc')
                ->orderByDesc('market_shops.id')
                ->select('market_shops.*'),

            'ownerEmailAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_shops.user_id')
                ->orderBy('owner_sort.email', 'asc')
                ->orderByDesc('market_shops.id')
                ->select('market_shops.*'),

            'ownerEmailDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_shops.user_id')
                ->orderBy('owner_sort.email', 'desc')
                ->orderByDesc('market_shops.id')
                ->select('market_shops.*'),

            'companyLegalNameAsc' => $query
                ->leftJoin('market_companies as company_sort', 'company_sort.id', '=', 'market_shops.market_company_id')
                ->orderBy('company_sort.legal_name', 'asc')
                ->orderByDesc('market_shops.id')
                ->select('market_shops.*'),

            'companyLegalNameDesc' => $query
                ->leftJoin('market_companies as company_sort', 'company_sort.id', '=', 'market_shops.market_company_id')
                ->orderBy('company_sort.legal_name', 'desc')
                ->orderByDesc('market_shops.id')
                ->select('market_shops.*'),

            default => $query->ordered(),
        };
    }
}
