<?php

namespace App\Models\Admin\Market\MarketCategory;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketCategory extends Model
{
    use HasFactory;

    protected $table = 'market_categories';

    protected $fillable = [
        'user_id',
        'parent_id',
        'level',

        'url',
        'icon',

        'in_menu',
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
        'parent_id' => 'integer',
        'level' => 'integer',

        'in_menu' => 'boolean',
        'sort' => 'integer',
        'activity' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',

        'moderated_at' => 'datetime',
        'published_at' => 'datetime',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',

        'views' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** Создатель категории */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Модератор категории */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Родительская категория */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Дочерние категории */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort')
            ->orderByDesc('id');
    }

    /** Дочерние категории рекурсивно */
    public function childrenRecursive(): HasMany
    {
        return $this->children()
            ->with([
                'translations',
                'translation',
                'images',
                'childrenRecursive',
            ])
            ->withCount([
                'children',
                'images',
            ]);
    }

    /** Публичные дочерние категории для каталога */
    public function publicCatalogChildren(): HasMany
    {
        return $this->children()
            ->forMenu()
            ->with([
                'translations',
                'publicCatalogChildren',
            ])
            ->withCount([
                'children',
            ]);
    }

    /** Переводы категории */
    public function translations(): HasMany
    {
        return $this->hasMany(
            MarketCategoryTranslation::class,
            'market_category_id'
        );
    }

    /** Текущий перевод категории */
    public function translation(): HasOne
    {
        return $this->hasOne(
            MarketCategoryTranslation::class,
            'market_category_id'
        )->where('locale', app()->getLocale());
    }

    /** Изображения категории */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketCategoryImage::class,
            'market_category_has_images',
            'market_category_id',
            'market_category_image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /** Товары категории */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProduct::class,
            'market_product_has_categories',
            'market_category_id',
            'market_product_id'
        )
            ->withPivot([
                'main',
                'order',
            ])
            ->orderByPivot('order');
    }

    /** Перевод с fallback */
    public function translationOrFallback(
        ?string $locale = null,
        string $fallback = 'ru'
    ): ?MarketCategoryTranslation {
        $locale = $locale ?: app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }

    /** Получить title из текущего перевода */
    public function getTranslatedTitle(
        ?string $locale = null,
        string $fallback = 'ru'
    ): ?string {
        return $this->translationOrFallback(
            locale: $locale,
            fallback: $fallback
        )?->title;
    }

    /** Активные категории */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('activity', true);
    }

    /** Опубликованные категории */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where('activity', true)
            ->whereNotNull('published_at');
    }

    /** Одобренные категории */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 1);
    }

    /** Только опубликованные и одобренные */
    public function scopeApprovedPublished(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published();
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderByDesc('id');
    }

    /** Корневые категории */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** Категории для меню */
    public function scopeInMenu(Builder $query): Builder
    {
        return $query->where('in_menu', true);
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

    /** Публичные категории */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /** Публичные категории меню */
    public function scopeForMenu(Builder $query): Builder
    {
        return $query
            ->forPublic()
            ->inMenu()
            ->ordered();
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
            $q->where('market_categories.url', 'like', "%{$term}%")
                ->orWhere('market_categories.icon', 'like', "%{$term}%")
                ->orWhere('market_categories.status', 'like', "%{$term}%")
                ->orWhere('market_categories.moderation_note', 'like', "%{$term}%")

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

                ->orWhereHas('parent.translations', function (Builder $pq) use ($term, $locale) {
                    $pq->where('locale', $locale)
                        ->where(function (Builder $sq) use ($term) {
                            $sq->where('title', 'like', "%{$term}%")
                                ->orWhere('subtitle', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%");
                        });
                })

                ->orWhereHas('owner', function (Builder $oq) use ($term) {
                    $oq->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                })

                ->orWhereHas('moderator', function (Builder $mq) use ($term) {
                    $mq->where('name', 'like', "%{$term}%")
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
            'idAsc' => $query->orderBy('market_categories.id', 'asc'),
            'idDesc' => $query->orderBy('market_categories.id', 'desc'),

            'sortAsc' => $query->orderBy('market_categories.sort', 'asc')->orderByDesc('market_categories.id'),
            'sortDesc' => $query->orderBy('market_categories.sort', 'desc')->orderByDesc('market_categories.id'),

            'levelAsc' => $query->orderBy('market_categories.level', 'asc')->orderByDesc('market_categories.id'),
            'levelDesc' => $query->orderBy('market_categories.level', 'desc')->orderByDesc('market_categories.id'),

            'parentAsc' => $query->orderBy('market_categories.parent_id', 'asc')->orderByDesc('market_categories.id'),
            'parentDesc' => $query->orderBy('market_categories.parent_id', 'desc')->orderByDesc('market_categories.id'),

            'urlAsc' => $query->orderBy('market_categories.url', 'asc')->orderByDesc('market_categories.id'),
            'urlDesc' => $query->orderBy('market_categories.url', 'desc')->orderByDesc('market_categories.id'),

            'viewsAsc' => $query->orderBy('market_categories.views', 'asc')->orderByDesc('market_categories.id'),
            'viewsDesc' => $query->orderBy('market_categories.views', 'desc')->orderByDesc('market_categories.id'),

            'activityAsc' => $query->orderBy('market_categories.activity', 'asc')->orderByDesc('market_categories.id'),
            'activityDesc' => $query->orderBy('market_categories.activity', 'desc')->orderByDesc('market_categories.id'),
            'activity' => $query->where('market_categories.activity', true)->orderByDesc('market_categories.id'),
            'inactive' => $query->where('market_categories.activity', false)->orderByDesc('market_categories.id'),

            'inMenuAsc' => $query->orderBy('market_categories.in_menu', 'asc')->orderByDesc('market_categories.id'),
            'inMenuDesc' => $query->orderBy('market_categories.in_menu', 'desc')->orderByDesc('market_categories.id'),
            'inMenu' => $query->where('market_categories.in_menu', true)->orderByDesc('market_categories.id'),
            'notInMenu' => $query->where('market_categories.in_menu', false)->orderByDesc('market_categories.id'),

            'statusAsc' => $query->orderBy('market_categories.status', 'asc')->orderByDesc('market_categories.id'),
            'statusDesc' => $query->orderBy('market_categories.status', 'desc')->orderByDesc('market_categories.id'),
            'statusDraft' => $query->where('market_categories.status', 'draft')->orderByDesc('market_categories.id'),
            'statusPublished' => $query->where('market_categories.status', 'published')->orderByDesc('market_categories.id'),
            'statusArchived' => $query->where('market_categories.status', 'archived')->orderByDesc('market_categories.id'),

            'ownerNameAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_categories.user_id')
                ->orderBy('owner_sort.name', 'asc')
                ->orderByDesc('market_categories.id')
                ->select('market_categories.*'),

            'ownerNameDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_categories.user_id')
                ->orderBy('owner_sort.name', 'desc')
                ->orderByDesc('market_categories.id')
                ->select('market_categories.*'),

            'ownerEmailAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_categories.user_id')
                ->orderBy('owner_sort.email', 'asc')
                ->orderByDesc('market_categories.id')
                ->select('market_categories.*'),

            'ownerEmailDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_categories.user_id')
                ->orderBy('owner_sort.email', 'desc')
                ->orderByDesc('market_categories.id')
                ->select('market_categories.*'),

            'moderationStatusAsc' => $query->orderBy('market_categories.moderation_status', 'asc')->orderByDesc('market_categories.id'),
            'moderationStatusDesc' => $query->orderBy('market_categories.moderation_status', 'desc')->orderByDesc('market_categories.id'),
            'moderationPending' => $query->where('market_categories.moderation_status', 0)->orderByDesc('market_categories.id'),
            'moderationApproved' => $query->where('market_categories.moderation_status', 1)->orderByDesc('market_categories.id'),
            'moderationRejected' => $query->where('market_categories.moderation_status', 2)->orderByDesc('market_categories.id'),

            'publishedAtAsc' => $query->orderBy('market_categories.published_at', 'asc')->orderByDesc('market_categories.id'),
            'publishedAtDesc' => $query->orderBy('market_categories.published_at', 'desc')->orderByDesc('market_categories.id'),

            'createdAtAsc', 'dateAsc' => $query->orderBy('market_categories.created_at', 'asc')->orderByDesc('market_categories.id'),
            'createdAtDesc', 'dateDesc' => $query->orderBy('market_categories.created_at', 'desc')->orderByDesc('market_categories.id'),

            'updatedAtAsc' => $query->orderBy('market_categories.updated_at', 'asc')->orderByDesc('market_categories.id'),
            'updatedAtDesc' => $query->orderBy('market_categories.updated_at', 'desc')->orderByDesc('market_categories.id'),

            'imagesAsc' => $query->withCount('images')->orderBy('images_count', 'asc')->orderByDesc('market_categories.id'),
            'imagesDesc' => $query->withCount('images')->orderBy('images_count', 'desc')->orderByDesc('market_categories.id'),

            'productsAsc' => $query
                ->withCount('products')
                ->orderBy('products_count', 'asc')
                ->orderByDesc('market_categories.id'),

            'productsDesc' => $query
                ->withCount('products')
                ->orderBy('products_count', 'desc')
                ->orderByDesc('market_categories.id'),

            'childrenAsc' => $query->withCount('children')->orderBy('children_count', 'asc')->orderByDesc('market_categories.id'),
            'childrenDesc' => $query->withCount('children')->orderBy('children_count', 'desc')->orderByDesc('market_categories.id'),

            'titleAsc' => $query
                ->leftJoin('market_category_translations as mct_sort', function ($join) use ($locale) {
                    $join->on('mct_sort.market_category_id', '=', 'market_categories.id')
                        ->where('mct_sort.locale', '=', $locale);
                })
                ->orderBy('mct_sort.title', 'asc')
                ->orderByDesc('market_categories.id')
                ->select('market_categories.*'),

            'titleDesc' => $query
                ->leftJoin('market_category_translations as mct_sort', function ($join) use ($locale) {
                    $join->on('mct_sort.market_category_id', '=', 'market_categories.id')
                        ->where('mct_sort.locale', '=', $locale);
                })
                ->orderBy('mct_sort.title', 'desc')
                ->orderByDesc('market_categories.id')
                ->select('market_categories.*'),

            default => $query->ordered(),
        };
    }

    /** Категория корневая */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /** Есть дочерние категории */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /** Категория активна */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Категория одобрена */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }
}
