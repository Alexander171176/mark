<?php

namespace App\Models\Admin\Market\MarketProduct;

use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Market\MarketBrand\MarketBrand;
use App\Models\Admin\Market\MarketCategory\MarketCategory;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
use App\Models\Admin\Market\MarketProductAttributeValue\MarketProductAttributeValue;
use App\Models\Admin\Market\MarketProductBundle\MarketProductBundle;
use App\Models\Admin\Market\MarketProductBundle\MarketProductBundleItem;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariant;
use App\Models\Admin\Market\MarketRecentlyViewedProduct\MarketRecentlyViewedProduct;
use App\Models\Admin\Market\MarketShop\MarketShop;
use App\Models\Admin\Market\MarketTag\MarketTag;
use App\Models\Admin\Review\Review;
use App\Models\User;
use App\Models\User\Like\MarketProductLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MarketProduct extends Model
{
    use HasFactory;

    protected $table = 'market_products';

    protected $fillable = [
        'user_id',

        'market_company_id',
        'market_shop_id',
        'market_brand_id',

        'url',
        'sku',
        'vendor_code',
        'barcode',

        'currency_id',

        'price',
        'old_price',
        'purchase_price',
        'wholesale_price',
        'wholesale_min_quantity',

        'quantity',
        'in_stock',

        'weight',
        'length',
        'width',
        'height',

        'sort',
        'activity',

        'left',
        'main',
        'right',

        'is_new',
        'is_hit',
        'is_sale',

        'status',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'published_at',
        'show_from_at',
        'show_to_at',

        'views',
        'likes_count',
        'rating_avg',
        'rating_count',
    ];

    protected $casts = [
        'user_id' => 'integer',

        'market_company_id' => 'integer',
        'market_shop_id' => 'integer',
        'market_brand_id' => 'integer',

        'currency_id' => 'integer',

        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'wholesale_min_quantity' => 'integer',

        'quantity' => 'integer',
        'in_stock' => 'boolean',

        'weight' => 'decimal:3',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',

        'sort' => 'integer',
        'activity' => 'boolean',

        'left' => 'boolean',
        'main' => 'boolean',
        'right' => 'boolean',

        'is_new' => 'boolean',
        'is_hit' => 'boolean',
        'is_sale' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',
        'moderated_at' => 'datetime',

        'published_at' => 'datetime',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',

        'views' => 'integer',
        'likes_count' => 'integer',
        'rating_avg' => 'decimal:2',
        'rating_count' => 'integer',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Валюта товара */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /** Создатель / владелец товара */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Модератор товара */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Компания-поставщик */
    public function company(): BelongsTo
    {
        return $this->belongsTo(MarketCompany::class, 'market_company_id');
    }

    /** Магазин товара */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(MarketShop::class, 'market_shop_id');
    }

    /** Бренд товара */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(MarketBrand::class, 'market_brand_id');
    }

    /** Переводы товара */
    public function translations(): HasMany
    {
        return $this->hasMany(MarketProductTranslation::class, 'market_product_id');
    }

    /**
     * Текущий перевод товара.
     *
     * Сохраняем relation для публичных и внешних сценариев.
     * Admin Index использует translations, заранее отфильтрованные Controller по locale.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(MarketProductTranslation::class, 'market_product_id')
            ->where('locale', app()->getLocale());
    }

    /**
     * Получить перевод товара:
     * текущая локаль → fallback приложения → null.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallback = null
    ): ?MarketProductTranslation {
        $locale = $locale ?: app()->getLocale();

        $fallback ??= config(
            'app.fallback_locale',
            'ru'
        );

        return $this->translations
            ->firstWhere('locale', $locale)
            ?: $this->translations
                ->firstWhere('locale', $fallback);
    }

    /** Получить название товара из разрешённого перевода */
    public function getTranslatedTitle(
        ?string $locale = null,
        ?string $fallback = null
    ): ?string {
        return $this->translationOrFallback(
            locale: $locale,
            fallback: $fallback
        )?->title;
    }

    /** Изображения товара */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProductImage::class,
            'market_product_has_images',
            'market_product_id',
            'market_product_image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /** Категории товара */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketCategory::class,
            'market_product_has_categories',
            'market_product_id',
            'market_category_id'
        )
            ->withPivot(['main', 'order'])
            ->orderByPivot('order');
    }

    /** Основные категории товара */
    public function mainCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketCategory::class,
            'market_product_has_categories',
            'market_product_id',
            'market_category_id'
        )
            ->withPivot(['main', 'order'])
            ->wherePivot('main', true)
            ->orderByPivot('order');
    }

    /** Теги товара */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketTag::class,
            'market_product_has_tags',
            'market_product_id',
            'market_tag_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /** Значения характеристик товара */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(
            MarketProductAttributeValue::class,
            'market_product_id'
        );
    }

    /**
     * Все варианты товара.
     *
     * Например:
     * - чёрный / S;
     * - чёрный / M;
     * - белый / XL.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(MarketProductVariant::class, 'market_product_id')
            ->orderBy('market_product_variants.sort')
            ->orderByDesc('market_product_variants.id');
    }

    /** Основной вариант товара */
    public function defaultVariant(): HasOne
    {
        return $this->hasOne(MarketProductVariant::class, 'market_product_id')
            ->where('market_product_variants.is_default', true)
            ->orderBy('market_product_variants.sort')
            ->orderByDesc('market_product_variants.id');
    }

    /** Публично доступные варианты товара */
    public function publicVariants(): HasMany
    {
        return $this->hasMany(MarketProductVariant::class, 'market_product_id')
            ->approved()
            ->published()
            ->inShowWindow()
            ->orderBy('market_product_variants.sort')
            ->orderByDesc('market_product_variants.id');
    }

    /**
     * Позиции комплектов, в которых используется товар.
     *
     * Связь включает:
     * - товар без конкретного варианта;
     * - товар с выбранным вариантом.
     */
    public function bundleItems(): HasMany
    {
        return $this->hasMany(MarketProductBundleItem::class, 'market_product_id')
            ->orderBy('market_product_bundle_items.sort')
            ->orderBy('market_product_bundle_items.id');
    }

    /** Активные позиции комплектов, в которых используется товар */
    public function activeBundleItems(): HasMany
    {
        return $this->hasMany(MarketProductBundleItem::class, 'market_product_id')
            ->where('market_product_bundle_items.activity', true)
            ->orderBy('market_product_bundle_items.sort')
            ->orderBy('market_product_bundle_items.id');
    }

    /**
     * Комплекты, в состав которых входит товар.
     *
     * Товар может входить:
     * - самостоятельно;
     * - через конкретный вариант.
     */
    public function productBundles(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProductBundle::class,
            'market_product_bundle_items',
            'market_product_id',
            'market_product_bundle_id'
        )
            ->withPivot([
                'id',
                'market_product_variant_id',
                'quantity',
                'unit_price',
                'discount_type',
                'discount_value',
                'sort',
                'activity',
            ])
            ->withTimestamps()
            ->orderByPivot('sort');
    }

    /** Только активные комплекты, в состав которых входит товар */
    public function activeProductBundles(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProductBundle::class,
            'market_product_bundle_items',
            'market_product_id',
            'market_product_bundle_id'
        )
            ->withPivot([
                'id',
                'market_product_variant_id',
                'quantity',
                'unit_price',
                'discount_type',
                'discount_value',
                'sort',
                'activity',
            ])
            ->wherePivot('activity', true)
            ->withTimestamps()
            ->orderByPivot('sort');
    }

    /** Отзывы товара */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /** Лайки товара */
    public function likes(): HasMany
    {
        return $this->hasMany(MarketProductLike::class, 'market_product_id');
    }

    /** Пользователи, лайкнувшие товар */
    public function likedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'market_product_likes',
            'market_product_id',
            'user_id'
        )->withTimestamps();
    }

    /** История просмотров товара зарегистрированными пользователями */
    public function recentlyViewedByUsers(): HasMany
    {
        return $this->hasMany(
            MarketRecentlyViewedProduct::class,
            'market_product_id'
        );
    }

    /** Рекомендуемые / похожие товары */
    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'market_product_related',
            'market_product_id',
            'related_product_id'
        )
            ->withPivot(['type', 'order', 'activity'])
            ->wherePivot('activity', true)
            ->orderByPivot('order');
    }

    /** Где этот товар используется как рекомендованный */
    public function usedInRelatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'market_product_related',
            'related_product_id',
            'market_product_id'
        )->withPivot(['type', 'order', 'activity']);
    }

    /* ======================== Helpers ======================== */

    /** Товар активен */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Товар одобрен */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Товар опубликован */
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && (bool) $this->activity
            && ! is_null($this->published_at);
    }

    /** Товар находится в окне показа */
    public function isPublishedNow(): bool
    {
        $now = now();

        if ($this->show_from_at && $now->lt($this->show_from_at)) {
            return false;
        }

        if ($this->show_to_at && $now->gt($this->show_to_at)) {
            return false;
        }

        return true;
    }

    /** Есть ли товар в наличии */
    public function hasStock(): bool
    {
        return (bool) $this->in_stock && (int) $this->quantity > 0;
    }

    /** Есть ли старая цена */
    public function hasOldPrice(): bool
    {
        return ! is_null($this->old_price)
            && (float) $this->old_price > (float) $this->price;
    }

    /** Есть ли оптовая цена */
    public function hasWholesalePrice(): bool
    {
        return ! is_null($this->wholesale_price)
            && (float) $this->wholesale_price > 0
            && ! is_null($this->wholesale_min_quantity);
    }

    /** Есть ли у товара варианты */
    public function hasVariants(): bool
    {
        return $this->relationLoaded('variants')
            ? $this->variants->isNotEmpty()
            : $this->variants()->exists();
    }

    /** Количество вариантов товара */
    public function variantsCount(): int
    {
        if (isset($this->variants_count)) {
            return (int) $this->variants_count;
        }

        return $this->relationLoaded('variants')
            ? $this->variants->count()
            : $this->variants()->count();
    }

    /** Получить основной вариант товара */
    public function getDefaultVariant(): ?MarketProductVariant
    {
        if ($this->relationLoaded('defaultVariant')) {
            $variant = $this->getRelation('defaultVariant');

            return $variant instanceof MarketProductVariant
                ? $variant
                : null;
        }

        $variant = $this->defaultVariant()->first();

        return $variant instanceof MarketProductVariant
            ? $variant
            : null;
    }

    /** Есть ли у товара доступные варианты */
    public function hasAvailableVariants(): bool
    {
        return $this->variants()
            ->active()
            ->inStock()
            ->exists();
    }

    /** Используется ли товар хотя бы в одном комплекте */
    public function isUsedInBundles(): bool
    {
        if (isset($this->bundle_items_count)) {
            return (int) $this->bundle_items_count > 0;
        }

        return $this->relationLoaded('bundleItems')
            ? $this->bundleItems->isNotEmpty()
            : $this->bundleItems()->exists();
    }

    /** Количество позиций комплектов с этим товаром */
    public function bundleItemsCount(): int
    {
        if (isset($this->bundle_items_count)) {
            return (int) $this->bundle_items_count;
        }

        return $this->relationLoaded('bundleItems')
            ? $this->bundleItems->count()
            : $this->bundleItems()->count();
    }

    /**
     * Public-сортировка товара по переведённому названию.
     */
    protected function scopePublicSortByTitle(
        Builder $query,
        string $direction,
        string $locale
    ): Builder {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $query
            ->leftJoin(
                'market_product_translations as mpt_current',
                function ($join) use ($locale) {
                    $join
                        ->on(
                            'mpt_current.market_product_id',
                            '=',
                            'market_products.id'
                        )
                        ->where(
                            'mpt_current.locale',
                            '=',
                            $locale
                        );
                }
            )
            ->leftJoin(
                'market_product_translations as mpt_fallback',
                function ($join) use ($fallbackLocale) {
                    $join
                        ->on(
                            'mpt_fallback.market_product_id',
                            '=',
                            'market_products.id'
                        )
                        ->where(
                            'mpt_fallback.locale',
                            '=',
                            $fallbackLocale
                        );
                }
            )
            ->addSelect('market_products.*')
            ->orderByRaw(
                'COALESCE(mpt_current.title, mpt_fallback.title) '
                . ($direction === 'desc' ? 'DESC' : 'ASC')
            )
            ->orderByDesc('market_products.id');

        return $query;
    }

    /**
     * Public-сортировка товара
     * по переведённому названию бренда.
     */
    protected function scopePublicSortByBrand(
        Builder $query,
        string $direction,
        string $locale
    ): Builder {
        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        return $query
            /**
             * Бренд должен быть доступен в Public.
             */
            ->leftJoin(
                'market_brands as mb_public',
                function ($join) {
                    $join
                        ->on(
                            'mb_public.id',
                            '=',
                            'market_products.market_brand_id'
                        )
                        ->where(
                            'mb_public.moderation_status',
                            '=',
                            1
                        )
                        ->where(
                            'mb_public.status',
                            '=',
                            'published'
                        )
                        ->where(
                            'mb_public.activity',
                            '=',
                            true
                        )
                        ->whereNotNull(
                            'mb_public.published_at'
                        )
                        ->where(
                            function ($query) {
                                $query
                                    ->whereNull(
                                        'mb_public.show_from_at'
                                    )
                                    ->orWhere(
                                        'mb_public.show_from_at',
                                        '<=',
                                        now()
                                    );
                            }
                        )
                        ->where(
                            function ($query) {
                                $query
                                    ->whereNull(
                                        'mb_public.show_to_at'
                                    )
                                    ->orWhere(
                                        'mb_public.show_to_at',
                                        '>=',
                                        now()
                                    );
                            }
                        );
                }
            )
            ->leftJoin(
                'market_brand_translations as mbt_current',
                function ($join) use ($locale) {
                    $join
                        ->on(
                            'mbt_current.market_brand_id',
                            '=',
                            'mb_public.id'
                        )
                        ->where(
                            'mbt_current.locale',
                            '=',
                            $locale
                        );
                }
            )
            ->leftJoin(
                'market_brand_translations as mbt_fallback',
                function ($join) use ($fallbackLocale) {
                    $join
                        ->on(
                            'mbt_fallback.market_brand_id',
                            '=',
                            'mb_public.id'
                        )
                        ->where(
                            'mbt_fallback.locale',
                            '=',
                            $fallbackLocale
                        );
                }
            )
            ->addSelect('market_products.*')
            ->orderByRaw(
                'COALESCE(mbt_current.title, mbt_fallback.title) '
                . ($direction === 'desc'
                    ? 'DESC'
                    : 'ASC')
            )
            ->orderByDesc(
                'market_products.id'
            );
    }

    /* ======================== Scopes ======================== */

    /** Только товары, имеющие варианты */
    public function scopeHasVariants(Builder $query): Builder
    {
        return $query->whereHas('variants');
    }

    /** Только товары без вариантов */
    public function scopeWithoutVariants(Builder $query): Builder
    {
        return $query->whereDoesntHave('variants');
    }

    /** Только товары с доступными вариантами */
    public function scopeHasAvailableVariants(Builder $query): Builder
    {
        return $query->whereHas('variants', function (Builder $variantQuery) {
            $variantQuery->active()->inStock();
        });
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('market_products.sort', 'asc')
            ->orderByDesc('market_products.id');
    }

    /** Только активные */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('market_products.activity', true);
    }

    /** Только опубликованные */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('market_products.status', 'published')
            ->where('market_products.activity', true)
            ->whereNotNull('market_products.published_at');
    }

    /** Только одобренные */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('market_products.moderation_status', 1);
    }

    /** Только товары в наличии */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('market_products.in_stock', true);
    }

    /** Новинки */
    public function scopeNew(Builder $query): Builder
    {
        return $query->where('market_products.is_new', true);
    }

    /** Хиты продаж */
    public function scopeHit(Builder $query): Builder
    {
        return $query->where('market_products.is_hit', true);
    }

    /** Распродажа */
    public function scopeSale(Builder $query): Builder
    {
        return $query->where('market_products.is_sale', true);
    }

    /** Левая рекламная зона */
    public function scopeLeft(Builder $query): Builder
    {
        return $query->where('market_products.left', true);
    }

    /** Главная рекламная зона */
    public function scopeMain(Builder $query): Builder
    {
        return $query->where('market_products.main', true);
    }

    /** Правая рекламная зона */
    public function scopeRight(Builder $query): Builder
    {
        return $query->where('market_products.right', true);
    }

    /** Окно показа */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull('market_products.show_from_at')
                    ->orWhere('market_products.show_from_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('market_products.show_to_at')
                    ->orWhere('market_products.show_to_at', '>=', now());
            });
    }

    /** Публичные товары */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /**
     * Поиск товаров в Admin.
     *
     * Семантика должна полностью совпадать
     * с frontend-поиском Index.vue.
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

        return $query->where(function (Builder $q) use ($term, $locale) {
            $q->where('market_products.url', 'like', "%{$term}%")
                ->orWhere('market_products.sku', 'like', "%{$term}%")
                ->orWhere('market_products.vendor_code', 'like', "%{$term}%")
                ->orWhere('market_products.barcode', 'like', "%{$term}%")
                ->orWhere('market_products.status', 'like', "%{$term}%")
                ->orWhere('market_products.moderation_note', 'like', "%{$term}%")

                ->orWhereHas('translations', function (Builder $translationQuery) use ($term, $locale) {
                    $translationQuery
                        ->where('locale', $locale)
                        ->where(function (Builder $translationSearch) use ($term) {
                            $translationSearch
                                ->where('title', 'like', "%{$term}%")
                                ->orWhere('subtitle', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%");
                        });
                })

                ->orWhereHas('company.translations', function (Builder $companyQuery) use ($term, $locale) {
                    $companyQuery
                        ->where('locale', $locale)
                        ->where('title', 'like', "%{$term}%");
                })

                ->orWhereHas('shop.translations', function (Builder $shopQuery) use ($term, $locale) {
                    $shopQuery
                        ->where('locale', $locale)
                        ->where('title', 'like', "%{$term}%");
                })

                ->orWhereHas('brand.translations', function (Builder $brandQuery) use ($term, $locale) {
                    $brandQuery
                        ->where('locale', $locale)
                        ->where('title', 'like', "%{$term}%");
                })

                ->orWhereHas('owner', function (Builder $ownerQuery) use ($term) {
                    $ownerQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
        });
    }

    /**
     * Сортировка административного списка.
     *
     * Важно:
     * - все собственные колонки квалифицированы;
     * - все non-ID сортировки получают tie-break ID DESC;
     * - JOIN-сортировки используют addSelect();
     * - count-сортировки предполагают, что Index Controller
     *   уже добавил соответствующие withCount().
     */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $query->orderBy('market_products.id', 'asc'),
            'idDesc' => $query->orderBy('market_products.id', 'desc'),

            'sortAsc' => $query->orderBy('market_products.sort', 'asc')->orderByDesc('market_products.id'),
            'sortDesc' => $query->orderBy('market_products.sort', 'desc')->orderByDesc('market_products.id'),

            'titleAsc' => $query
                ->leftJoin('market_product_translations as mpt_sort', function ($join) use ($locale) {
                    $join->on('mpt_sort.market_product_id', '=', 'market_products.id')
                        ->where('mpt_sort.locale', '=', $locale);
                })
                ->addSelect('market_products.*')
                ->orderBy('mpt_sort.title', 'asc')
                ->orderByDesc('market_products.id'),

            'titleDesc' => $query
                ->leftJoin('market_product_translations as mpt_sort', function ($join) use ($locale) {
                    $join->on('mpt_sort.market_product_id', '=', 'market_products.id')
                        ->where('mpt_sort.locale', '=', $locale);
                })
                ->addSelect('market_products.*')
                ->orderBy('mpt_sort.title', 'desc')
                ->orderByDesc('market_products.id'),

            'urlAsc' => $query->orderBy('market_products.url', 'asc')->orderByDesc('market_products.id'),
            'urlDesc' => $query->orderBy('market_products.url', 'desc')->orderByDesc('market_products.id'),

            'skuAsc' => $query->orderBy('market_products.sku', 'asc')->orderByDesc('market_products.id'),
            'skuDesc' => $query->orderBy('market_products.sku', 'desc')->orderByDesc('market_products.id'),

            'vendorCodeAsc' => $query->orderBy('market_products.vendor_code', 'asc')->orderByDesc('market_products.id'),
            'vendorCodeDesc' => $query->orderBy('market_products.vendor_code', 'desc')->orderByDesc('market_products.id'),

            'barcodeAsc' => $query->orderBy('market_products.barcode', 'asc')->orderByDesc('market_products.id'),
            'barcodeDesc' => $query->orderBy('market_products.barcode', 'desc')->orderByDesc('market_products.id'),

            'priceAsc' => $query->orderBy('market_products.price', 'asc')->orderByDesc('market_products.id'),
            'priceDesc' => $query->orderBy('market_products.price', 'desc')->orderByDesc('market_products.id'),

            'oldPriceAsc' => $query->orderBy('market_products.old_price', 'asc')->orderByDesc('market_products.id'),
            'oldPriceDesc' => $query->orderBy('market_products.old_price', 'desc')->orderByDesc('market_products.id'),

            'purchasePriceAsc' => $query->orderBy('market_products.purchase_price', 'asc')->orderByDesc('market_products.id'),
            'purchasePriceDesc' => $query->orderBy('market_products.purchase_price', 'desc')->orderByDesc('market_products.id'),

            'wholesalePriceAsc' => $query->orderBy('market_products.wholesale_price', 'asc')->orderByDesc('market_products.id'),
            'wholesalePriceDesc' => $query->orderBy('market_products.wholesale_price', 'desc')->orderByDesc('market_products.id'),

            'quantityAsc' => $query->orderBy('market_products.quantity', 'asc')->orderByDesc('market_products.id'),
            'quantityDesc' => $query->orderBy('market_products.quantity', 'desc')->orderByDesc('market_products.id'),

            'inStockAsc' => $query->orderBy('market_products.in_stock', 'asc')->orderByDesc('market_products.id'),
            'inStockDesc' => $query->orderBy('market_products.in_stock', 'desc')->orderByDesc('market_products.id'),
            'inStock' => $query->where('market_products.in_stock', true)->orderByDesc('market_products.id'),
            'outOfStock' => $query->where('market_products.in_stock', false)->orderByDesc('market_products.id'),

            'weightAsc' => $query->orderBy('market_products.weight', 'asc')->orderByDesc('market_products.id'),
            'weightDesc' => $query->orderBy('market_products.weight', 'desc')->orderByDesc('market_products.id'),

            'lengthAsc' => $query->orderBy('market_products.length', 'asc')->orderByDesc('market_products.id'),
            'lengthDesc' => $query->orderBy('market_products.length', 'desc')->orderByDesc('market_products.id'),

            'widthAsc' => $query->orderBy('market_products.width', 'asc')->orderByDesc('market_products.id'),
            'widthDesc' => $query->orderBy('market_products.width', 'desc')->orderByDesc('market_products.id'),

            'heightAsc' => $query->orderBy('market_products.height', 'asc')->orderByDesc('market_products.id'),
            'heightDesc' => $query->orderBy('market_products.height', 'desc')->orderByDesc('market_products.id'),

            'viewsAsc' => $query->orderBy('market_products.views', 'asc')->orderByDesc('market_products.id'),
            'viewsDesc' => $query->orderBy('market_products.views', 'desc')->orderByDesc('market_products.id'),

            /**
             * likes_count уже хранится в market_products.
             * withCount('likes') здесь не нужен.
             */
            'likesAsc' => $query->orderBy('market_products.likes_count', 'asc')->orderByDesc('market_products.id'),
            'likesDesc' => $query->orderBy('market_products.likes_count', 'desc')->orderByDesc('market_products.id'),

            'ratingAsc' => $query->orderBy('market_products.rating_avg', 'asc')->orderByDesc('market_products.id'),
            'ratingDesc' => $query->orderBy('market_products.rating_avg', 'desc')->orderByDesc('market_products.id'),

            'ratingCountAsc' => $query->orderBy('market_products.rating_count', 'asc')->orderByDesc('market_products.id'),
            'ratingCountDesc' => $query->orderBy('market_products.rating_count', 'desc')->orderByDesc('market_products.id'),

            /**
             * Counts добавляет Index Controller постоянно.
             * Не вызываем withCount повторно внутри сортировки.
             */
            'imagesAsc' => $query->orderBy('images_count', 'asc')->orderByDesc('market_products.id'),
            'imagesDesc' => $query->orderBy('images_count', 'desc')->orderByDesc('market_products.id'),

            'categoriesAsc' => $query->orderBy('categories_count', 'asc')->orderByDesc('market_products.id'),
            'categoriesDesc' => $query->orderBy('categories_count', 'desc')->orderByDesc('market_products.id'),

            'tagsAsc' => $query->orderBy('tags_count', 'asc')->orderByDesc('market_products.id'),
            'tagsDesc' => $query->orderBy('tags_count', 'desc')->orderByDesc('market_products.id'),

            'attributesAsc' => $query->orderBy('attribute_values_count', 'asc')->orderByDesc('market_products.id'),
            'attributesDesc' => $query->orderBy('attribute_values_count', 'desc')->orderByDesc('market_products.id'),

            'variantsAsc' => $query->orderBy('variants_count', 'asc')->orderByDesc('market_products.id'),
            'variantsDesc' => $query->orderBy('variants_count', 'desc')->orderByDesc('market_products.id'),

            'hasVariants' => $query->whereHas('variants')->orderByDesc('market_products.id'),
            'withoutVariants' => $query->whereDoesntHave('variants')->orderByDesc('market_products.id'),

            'reviewsAsc' => $query->orderBy('reviews_count', 'asc')->orderByDesc('market_products.id'),
            'reviewsDesc' => $query->orderBy('reviews_count', 'desc')->orderByDesc('market_products.id'),

            'relatedProductsAsc' => $query->orderBy('related_products_count', 'asc')->orderByDesc('market_products.id'),
            'relatedProductsDesc' => $query->orderBy('related_products_count', 'desc')->orderByDesc('market_products.id'),

            'activityAsc' => $query->orderBy('market_products.activity', 'asc')->orderByDesc('market_products.id'),
            'activityDesc' => $query->orderBy('market_products.activity', 'desc')->orderByDesc('market_products.id'),
            'activity' => $query->where('market_products.activity', true)->orderByDesc('market_products.id'),
            'inactive' => $query->where('market_products.activity', false)->orderByDesc('market_products.id'),

            'leftAsc' => $query->orderBy('market_products.left', 'asc')->orderByDesc('market_products.id'),
            'leftDesc' => $query->orderBy('market_products.left', 'desc')->orderByDesc('market_products.id'),
            'left' => $query->where('market_products.left', true)->orderByDesc('market_products.id'),
            'noLeft' => $query->where('market_products.left', false)->orderByDesc('market_products.id'),

            'mainAsc' => $query->orderBy('market_products.main', 'asc')->orderByDesc('market_products.id'),
            'mainDesc' => $query->orderBy('market_products.main', 'desc')->orderByDesc('market_products.id'),
            'main' => $query->where('market_products.main', true)->orderByDesc('market_products.id'),
            'noMain' => $query->where('market_products.main', false)->orderByDesc('market_products.id'),

            'rightAsc' => $query->orderBy('market_products.right', 'asc')->orderByDesc('market_products.id'),
            'rightDesc' => $query->orderBy('market_products.right', 'desc')->orderByDesc('market_products.id'),
            'right' => $query->where('market_products.right', true)->orderByDesc('market_products.id'),
            'noRight' => $query->where('market_products.right', false)->orderByDesc('market_products.id'),

            'newAsc' => $query->orderBy('market_products.is_new', 'asc')->orderByDesc('market_products.id'),
            'newDesc' => $query->orderBy('market_products.is_new', 'desc')->orderByDesc('market_products.id'),
            'new' => $query->where('market_products.is_new', true)->orderByDesc('market_products.id'),
            'notNew' => $query->where('market_products.is_new', false)->orderByDesc('market_products.id'),

            'hitAsc' => $query->orderBy('market_products.is_hit', 'asc')->orderByDesc('market_products.id'),
            'hitDesc' => $query->orderBy('market_products.is_hit', 'desc')->orderByDesc('market_products.id'),
            'hit' => $query->where('market_products.is_hit', true)->orderByDesc('market_products.id'),
            'notHit' => $query->where('market_products.is_hit', false)->orderByDesc('market_products.id'),

            'saleAsc' => $query->orderBy('market_products.is_sale', 'asc')->orderByDesc('market_products.id'),
            'saleDesc' => $query->orderBy('market_products.is_sale', 'desc')->orderByDesc('market_products.id'),
            'sale' => $query->where('market_products.is_sale', true)->orderByDesc('market_products.id'),
            'notSale' => $query->where('market_products.is_sale', false)->orderByDesc('market_products.id'),

            'statusAsc' => $query->orderBy('market_products.status', 'asc')->orderByDesc('market_products.id'),
            'statusDesc' => $query->orderBy('market_products.status', 'desc')->orderByDesc('market_products.id'),
            'statusDraft' => $query->where('market_products.status', 'draft')->orderByDesc('market_products.id'),
            'statusPublished' => $query->where('market_products.status', 'published')->orderByDesc('market_products.id'),
            'statusArchived' => $query->where('market_products.status', 'archived')->orderByDesc('market_products.id'),

            'moderationStatusAsc' => $query->orderBy('market_products.moderation_status', 'asc')->orderByDesc('market_products.id'),
            'moderationStatusDesc' => $query->orderBy('market_products.moderation_status', 'desc')->orderByDesc('market_products.id'),
            'moderationPending' => $query->where('market_products.moderation_status', 0)->orderByDesc('market_products.id'),
            'moderationApproved' => $query->where('market_products.moderation_status', 1)->orderByDesc('market_products.id'),
            'moderationRejected' => $query->where('market_products.moderation_status', 2)->orderByDesc('market_products.id'),

            'publishedAtAsc' => $query->orderBy('market_products.published_at', 'asc')->orderByDesc('market_products.id'),
            'publishedAtDesc' => $query->orderBy('market_products.published_at', 'desc')->orderByDesc('market_products.id'),

            'showFromAtAsc' => $query->orderBy('market_products.show_from_at', 'asc')->orderByDesc('market_products.id'),
            'showFromAtDesc' => $query->orderBy('market_products.show_from_at', 'desc')->orderByDesc('market_products.id'),

            'showToAtAsc' => $query->orderBy('market_products.show_to_at', 'asc')->orderByDesc('market_products.id'),
            'showToAtDesc' => $query->orderBy('market_products.show_to_at', 'desc')->orderByDesc('market_products.id'),

            'createdAtAsc', 'dateAsc' => $query->orderBy('market_products.created_at', 'asc')->orderByDesc('market_products.id'),
            'createdAtDesc', 'dateDesc' => $query->orderBy('market_products.created_at', 'desc')->orderByDesc('market_products.id'),

            'updatedAtAsc' => $query->orderBy('market_products.updated_at', 'asc')->orderByDesc('market_products.id'),
            'updatedAtDesc' => $query->orderBy('market_products.updated_at', 'desc')->orderByDesc('market_products.id'),

            'companyAsc' => $query
                ->leftJoin('market_company_translations as mct_sort', function ($join) use ($locale) {
                    $join->on('mct_sort.market_company_id', '=', 'market_products.market_company_id')
                        ->where('mct_sort.locale', '=', $locale);
                })
                ->addSelect('market_products.*')
                ->orderBy('mct_sort.title', 'asc')
                ->orderByDesc('market_products.id'),

            'companyDesc' => $query
                ->leftJoin('market_company_translations as mct_sort', function ($join) use ($locale) {
                    $join->on('mct_sort.market_company_id', '=', 'market_products.market_company_id')
                        ->where('mct_sort.locale', '=', $locale);
                })
                ->addSelect('market_products.*')
                ->orderBy('mct_sort.title', 'desc')
                ->orderByDesc('market_products.id'),

            'shopAsc' => $query
                ->leftJoin('market_shop_translations as mst_sort', function ($join) use ($locale) {
                    $join->on('mst_sort.market_shop_id', '=', 'market_products.market_shop_id')
                        ->where('mst_sort.locale', '=', $locale);
                })
                ->addSelect('market_products.*')
                ->orderBy('mst_sort.title', 'asc')
                ->orderByDesc('market_products.id'),

            'shopDesc' => $query
                ->leftJoin('market_shop_translations as mst_sort', function ($join) use ($locale) {
                    $join->on('mst_sort.market_shop_id', '=', 'market_products.market_shop_id')
                        ->where('mst_sort.locale', '=', $locale);
                })
                ->addSelect('market_products.*')
                ->orderBy('mst_sort.title', 'desc')
                ->orderByDesc('market_products.id'),

            'brandAsc' => $query
                ->leftJoin('market_brand_translations as mbt_sort', function ($join) use ($locale) {
                    $join->on('mbt_sort.market_brand_id', '=', 'market_products.market_brand_id')
                        ->where('mbt_sort.locale', '=', $locale);
                })
                ->addSelect('market_products.*')
                ->orderBy('mbt_sort.title', 'asc')
                ->orderByDesc('market_products.id'),

            'brandDesc' => $query
                ->leftJoin('market_brand_translations as mbt_sort', function ($join) use ($locale) {
                    $join->on('mbt_sort.market_brand_id', '=', 'market_products.market_brand_id')
                        ->where('mbt_sort.locale', '=', $locale);
                })
                ->addSelect('market_products.*')
                ->orderBy('mbt_sort.title', 'desc')
                ->orderByDesc('market_products.id'),

            'ownerNameAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_products.user_id')
                ->addSelect('market_products.*')
                ->orderBy('owner_sort.name', 'asc')
                ->orderByDesc('market_products.id'),

            'ownerNameDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_products.user_id')
                ->addSelect('market_products.*')
                ->orderBy('owner_sort.name', 'desc')
                ->orderByDesc('market_products.id'),

            'ownerEmailAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_products.user_id')
                ->addSelect('market_products.*')
                ->orderBy('owner_sort.email', 'asc')
                ->orderByDesc('market_products.id'),

            'ownerEmailDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'market_products.user_id')
                ->addSelect('market_products.*')
                ->orderBy('owner_sort.email', 'desc')
                ->orderByDesc('market_products.id'),

            default => $query->ordered(),
        };
    }

    /**
     * Поиск товаров в Public.
     *
     * Семантика:
     * - current → fallback → null;
     * - fallback используется только при отсутствии current-перевода;
     * - слова от 2 символов;
     * - AND между словами;
     * - OR между публичными полями;
     * - без Admin/internal полей;
     * - бренд участвует только если доступен в Public;
     * - полностью соответствует Public MarketProducts/Index.vue.
     */
    public function scopePublicSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $words = preg_split(
            '/\s+/u',
            $term,
            -1,
            PREG_SPLIT_NO_EMPTY
        );

        $words = array_values(
            array_filter(
                $words ?: [],
                fn (string $word) =>
                    mb_strlen($word) >= 2
            )
        );

        if ($words === []) {
            return $query;
        }

        foreach ($words as $word) {
            $query->where(
                function (Builder $wordQuery) use (
                    $word,
                    $locale,
                    $fallbackLocale
                ) {
                    $like = "%{$word}%";

                    $wordQuery
                        ->where(
                            'market_products.url',
                            'like',
                            $like
                        )
                        ->orWhere(
                            'market_products.sku',
                            'like',
                            $like
                        )
                        ->orWhere(
                            'market_products.vendor_code',
                            'like',
                            $like
                        )
                        ->orWhere(
                            'market_products.barcode',
                            'like',
                            $like
                        )

                        /**
                         * Разрешённый перевод товара:
                         * current → fallback → null.
                         */
                        ->orWhere(
                            function (Builder $translationBlock) use (
                                $like,
                                $locale,
                                $fallbackLocale
                            ) {
                                $translationBlock
                                    ->whereHas(
                                        'translations',
                                        function (Builder $translationQuery) use (
                                            $like,
                                            $locale
                                        ) {
                                            $translationQuery
                                                ->where(
                                                    'locale',
                                                    $locale
                                                )
                                                ->where(
                                                    function (Builder $translationSearch) use ($like) {
                                                        $translationSearch
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
                                    );

                                /**
                                 * Fallback разрешён только если
                                 * current-перевода товара нет.
                                 */
                                if ($fallbackLocale !== $locale) {
                                    $translationBlock
                                        ->orWhere(
                                            function (Builder $fallbackBlock) use (
                                                $like,
                                                $locale,
                                                $fallbackLocale
                                            ) {
                                                $fallbackBlock
                                                    ->whereDoesntHave(
                                                        'translations',
                                                        function (Builder $currentTranslationQuery) use ($locale) {
                                                            $currentTranslationQuery
                                                                ->where(
                                                                    'locale',
                                                                    $locale
                                                                );
                                                        }
                                                    )
                                                    ->whereHas(
                                                        'translations',
                                                        function (Builder $fallbackTranslationQuery) use (
                                                            $like,
                                                            $fallbackLocale
                                                        ) {
                                                            $fallbackTranslationQuery
                                                                ->where(
                                                                    'locale',
                                                                    $fallbackLocale
                                                                )
                                                                ->where(
                                                                    function (Builder $translationSearch) use ($like) {
                                                                        $translationSearch
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
                                                    );
                                            }
                                        );
                                }
                            }
                        )

                        /**
                         * Разрешённый перевод публичного бренда:
                         * current → fallback → null.
                         */
                        ->orWhereHas(
                            'brand',
                            function (Builder $brandQuery) use (
                                $like,
                                $locale,
                                $fallbackLocale
                            ) {
                                $brandQuery
                                    ->forPublic()
                                    ->where(
                                        function (Builder $brandTranslationBlock) use (
                                            $like,
                                            $locale,
                                            $fallbackLocale
                                        ) {
                                            $brandTranslationBlock
                                                ->whereHas(
                                                    'translations',
                                                    function (Builder $brandTranslationQuery) use (
                                                        $like,
                                                        $locale
                                                    ) {
                                                        $brandTranslationQuery
                                                            ->where(
                                                                'locale',
                                                                $locale
                                                            )
                                                            ->where(
                                                                'title',
                                                                'like',
                                                                $like
                                                            );
                                                    }
                                                );

                                            /**
                                             * Fallback бренда разрешён только
                                             * при отсутствии current-перевода.
                                             */
                                            if ($fallbackLocale !== $locale) {
                                                $brandTranslationBlock
                                                    ->orWhere(
                                                        function (Builder $brandFallbackBlock) use (
                                                            $like,
                                                            $locale,
                                                            $fallbackLocale
                                                        ) {
                                                            $brandFallbackBlock
                                                                ->whereDoesntHave(
                                                                    'translations',
                                                                    function (Builder $currentBrandTranslationQuery) use ($locale) {
                                                                        $currentBrandTranslationQuery
                                                                            ->where(
                                                                                'locale',
                                                                                $locale
                                                                            );
                                                                    }
                                                                )
                                                                ->whereHas(
                                                                    'translations',
                                                                    function (Builder $fallbackBrandTranslationQuery) use (
                                                                        $like,
                                                                        $fallbackLocale
                                                                    ) {
                                                                        $fallbackBrandTranslationQuery
                                                                            ->where(
                                                                                'locale',
                                                                                $fallbackLocale
                                                                            )
                                                                            ->where(
                                                                                'title',
                                                                                'like',
                                                                                $like
                                                                            );
                                                                    }
                                                                );
                                                        }
                                                    );
                                            }
                                        }
                                    );
                            }
                        );
                }
            );
        }

        return $query;
    }

    /**
     * Сортировка публичного каталога товаров.
     *
     * Семантика полностью соответствует
     * Public MarketProducts/Index.vue.
     */
    public function scopePublicSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            /**
             * ID.
             */
            'idAsc' =>
            $query->orderBy(
                'market_products.id',
                'asc'
            ),

            'idDesc' =>
            $query->orderBy(
                'market_products.id',
                'desc'
            ),

            /**
             * Ручной порядок.
             */
            'sortAsc' =>
            $query
                ->orderBy(
                    'market_products.sort',
                    'asc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            'sortDesc' =>
            $query
                ->orderBy(
                    'market_products.sort',
                    'desc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            /**
             * Название товара.
             */
            'titleAsc' =>
            $this->scopePublicSortByTitle(
                $query,
                'asc',
                $locale
            ),

            'titleDesc' =>
            $this->scopePublicSortByTitle(
                $query,
                'desc',
                $locale
            ),

            /**
             * Цена.
             */
            'priceAsc' =>
            $query
                ->orderBy(
                    'market_products.price',
                    'asc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            'priceDesc' =>
            $query
                ->orderBy(
                    'market_products.price',
                    'desc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            /**
             * Количество.
             */
            'quantityAsc' =>
            $query
                ->orderBy(
                    'market_products.quantity',
                    'asc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            'quantityDesc' =>
            $query
                ->orderBy(
                    'market_products.quantity',
                    'desc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            /**
             * Просмотры.
             */
            'viewsAsc' =>
            $query
                ->orderBy(
                    'market_products.views',
                    'asc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            'viewsDesc' =>
            $query
                ->orderBy(
                    'market_products.views',
                    'desc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            /**
             * Лайки.
             *
             * likes_count хранится непосредственно
             * в market_products.
             */
            'likesAsc' =>
            $query
                ->orderBy(
                    'market_products.likes_count',
                    'asc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            'likesDesc' =>
            $query
                ->orderBy(
                    'market_products.likes_count',
                    'desc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            /**
             * Средний рейтинг.
             */
            'ratingAsc' =>
            $query
                ->orderBy(
                    'market_products.rating_avg',
                    'asc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            'ratingDesc' =>
            $query
                ->orderBy(
                    'market_products.rating_avg',
                    'desc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            /**
             * Количество оценок.
             */
            'ratingCountAsc' =>
            $query
                ->orderBy(
                    'market_products.rating_count',
                    'asc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            'ratingCountDesc' =>
            $query
                ->orderBy(
                    'market_products.rating_count',
                    'desc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            /**
             * Отзывы.
             *
             * reviews_count должен быть заранее
             * добавлен Public Controller.
             */
            'reviewsAsc' =>
            $query
                ->orderBy(
                    'reviews_count',
                    'asc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            'reviewsDesc' =>
            $query
                ->orderBy(
                    'reviews_count',
                    'desc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            /**
             * Бренд.
             */
            'brandAsc' =>
            $this->scopePublicSortByBrand(
                $query,
                'asc',
                $locale
            ),

            'brandDesc' =>
            $this->scopePublicSortByBrand(
                $query,
                'desc',
                $locale
            ),

            /**
             * Дата публикации.
             */
            'publishedAtAsc' =>
            $query
                ->orderBy(
                    'market_products.published_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            'publishedAtDesc' =>
            $query
                ->orderBy(
                    'market_products.published_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            /**
             * Дата создания.
             */
            'createdAtAsc' =>
            $query
                ->orderBy(
                    'market_products.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            'createdAtDesc' =>
            $query
                ->orderBy(
                    'market_products.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'market_products.id'
                ),

            /**
             * Настройка Public по умолчанию.
             */
            default =>
            $query->ordered(),
        };
    }
}
