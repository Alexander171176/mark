<?php

namespace App\Models\Admin\Market\MarketBrand;

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

    /** Создатель бренда */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Модератор бренда */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Переводы бренда */
    public function translations(): HasMany
    {
        return $this->hasMany(MarketBrandTranslation::class, 'market_brand_id');
    }

    /** Текущий перевод бренда */
    public function translation(): HasOne
    {
        return $this->hasOne(MarketBrandTranslation::class, 'market_brand_id')
            ->where('locale', app()->getLocale());
    }

    /** Изображения бренда */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketBrandImage::class,
            'market_brand_has_images',
            'market_brand_id',
            'market_brand_image_id'
        )->withPivot('order')
            ->orderByPivot('order');
    }

    /** Активные бренды */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('activity', true);
    }

    /** Опубликованные бренды */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('activity', true)
            ->whereNotNull('published_at');
    }

    /** Одобренные бренды */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 1);
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderByDesc('id');
    }

    /** Левая рекламная зона */
    public function scopeLeft(Builder $query): Builder
    {
        return $query->where('left', true);
    }

    /** Главная рекламная зона */
    public function scopeMain(Builder $query): Builder
    {
        return $query->where('main', true);
    }

    /** Правая рекламная зона */
    public function scopeRight(Builder $query): Builder
    {
        return $query->where('right', true);
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

    /** Публичные бренды */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /** Поиск по бренду, переводам и владельцу */
    public function scopeSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null
    ): Builder {
        if (!$term) {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();

        return $query->where(function (Builder $q) use ($term, $locale) {
            $q->where('url', 'like', "%{$term}%")
                ->orWhere('website', 'like', "%{$term}%")
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
                });
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $query->orderBy('id', 'asc'),
            'idDesc' => $query->orderBy('id', 'desc'),

            'sortAsc' => $query->orderBy('sort', 'asc')->orderBy('id', 'asc'),
            'sortDesc' => $query->orderBy('sort', 'desc')->orderBy('id', 'desc'),

            'titleAsc' => $query
                ->leftJoin('market_brand_translations as mbt_sort', function ($join) use ($locale) {
                    $join->on('mbt_sort.market_brand_id', '=', 'market_brands.id')
                        ->where('mbt_sort.locale', '=', $locale);
                })
                ->orderBy('mbt_sort.title', 'asc')
                ->orderByDesc('market_brands.id')
                ->select('market_brands.*'),

            'titleDesc' => $query
                ->leftJoin('market_brand_translations as mbt_sort', function ($join) use ($locale) {
                    $join->on('mbt_sort.market_brand_id', '=', 'market_brands.id')
                        ->where('mbt_sort.locale', '=', $locale);
                })
                ->orderBy('mbt_sort.title', 'desc')
                ->orderByDesc('market_brands.id')
                ->select('market_brands.*'),

            'urlAsc' => $query->orderBy('url', 'asc')->orderByDesc('id'),
            'urlDesc' => $query->orderBy('url', 'desc')->orderByDesc('id'),

            'websiteAsc' => $query->orderBy('website', 'asc')->orderByDesc('id'),
            'websiteDesc' => $query->orderBy('website', 'desc')->orderByDesc('id'),

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
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_brands.user_id')
                ->orderBy('owner_sort.name', 'asc')
                ->orderByDesc('market_brands.id')
                ->select('market_brands.*'),

            'ownerNameDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_brands.user_id')
                ->orderBy('owner_sort.name', 'desc')
                ->orderByDesc('market_brands.id')
                ->select('market_brands.*'),

            'ownerEmailAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_brands.user_id')
                ->orderBy('owner_sort.email', 'asc')
                ->orderByDesc('market_brands.id')
                ->select('market_brands.*'),

            'ownerEmailDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_brands.user_id')
                ->orderBy('owner_sort.email', 'desc')
                ->orderByDesc('market_brands.id')
                ->select('market_brands.*'),

            default => $query->ordered(),
        };
    }
}
