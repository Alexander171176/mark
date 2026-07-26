<?php

namespace App\Models\Admin\Market\MarketProductVariant;

use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketProductBundle\MarketProductBundle;
use App\Models\Admin\Market\MarketProductBundle\MarketProductBundleItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketProductVariant extends Model
{
    use HasFactory;

    protected $table = 'market_product_variants';

    protected $fillable = [
        'market_product_id',
        'currency_id',

        'code',
        'sku',
        'vendor_code',
        'barcode',

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

        'is_default',

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
        'market_product_id' => 'integer',
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

        'is_default' => 'boolean',

        'sort' => 'integer',
        'activity' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',
        'moderated_at' => 'datetime',

        'published_at' => 'datetime',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /**
     * Родительский товар.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(
            MarketProduct::class,
            'market_product_id'
        );
    }

    /**
     * Собственная валюта варианта.
     *
     * Если связь отсутствует, используется валюта товара.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class,
            'currency_id'
        );
    }

    /**
     * Модератор варианта.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'moderated_by'
        );
    }

    /**
     * Все переводы варианта.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(
            MarketProductVariantTranslation::class,
            'market_product_variant_id'
        );
    }

    /**
     * Перевод для текущей локали приложения.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(
            MarketProductVariantTranslation::class,
            'market_product_variant_id'
        )->where('locale', app()->getLocale());
    }

    /**
     * Значения характеристик, формирующие вариант.
     *
     * Например:
     * - цвет: чёрный;
     * - размер: XL.
     */
    public function values(): HasMany
    {
        return $this->hasMany(
            MarketProductVariantValue::class,
            'market_product_variant_id'
        )
            ->orderBy('sort')
            ->orderBy('id');
    }

    /**
     * Изображения варианта.
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProductVariantImage::class,
            'market_product_variant_has_images',
            'market_product_variant_id',
            'market_product_variant_image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /**
     * Позиции комплектов, в которых используется вариант.
     */
    public function bundleItems(): HasMany
    {
        return $this->hasMany(
            MarketProductBundleItem::class,
            'market_product_variant_id'
        )
            ->orderBy('sort')
            ->orderBy('id');
    }

    /**
     * Активные позиции комплектов,
     * в которых используется вариант.
     */
    public function activeBundleItems(): HasMany
    {
        return $this->hasMany(
            MarketProductBundleItem::class,
            'market_product_variant_id'
        )
            ->where('activity', true)
            ->orderBy('sort')
            ->orderBy('id');
    }

    /**
     * Комплекты, в состав которых входит вариант.
     */
    public function productBundles(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProductBundle::class,
            'market_product_bundle_items',
            'market_product_variant_id',
            'market_product_bundle_id'
        )
            ->withPivot([
                'id',
                'market_product_id',
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

    /**
     * Только активные комплекты,
     * в состав которых входит вариант.
     */
    public function activeProductBundles(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProductBundle::class,
            'market_product_bundle_items',
            'market_product_variant_id',
            'market_product_bundle_id'
        )
            ->withPivot([
                'id',
                'market_product_id',
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

    /* ======================== Translations ======================== */

    /**
     * Получить перевод с резервной локалью.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallback = null
    ): ?MarketProductVariantTranslation {
        $locale = $locale ?: app()->getLocale();

        $fallback = $fallback
            ?: config('app.fallback_locale', 'ru');

        /*
         * Если переводы ещё не загружены, загружаем их один раз.
         */
        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }

    /**
     * Получить переведённое название варианта.
     */
    public function getTranslatedTitle(
        ?string $locale = null,
        ?string $fallback = null
    ): ?string {
        return $this->translationOrFallback(
            locale: $locale,
            fallback: $fallback
        )?->title;
    }

    /**
     * Получить отображаемое название варианта.
     *
     * При отсутствии собственного перевода используется:
     * название товара + код варианта.
     */
    public function getDisplayTitle(
        ?string $locale = null,
        ?string $fallback = null
    ): string {
        $variantTitle = $this->getTranslatedTitle(
            locale: $locale,
            fallback: $fallback
        );

        if (filled($variantTitle)) {
            return $variantTitle;
        }

        if (! $this->relationLoaded('product')) {
            $this->load('product.translations');
        }

        $productTitle = $this->product?->getTranslatedTitle(
            locale: $locale,
            fallback: $fallback
        );

        $suffix = $this->code
            ?: $this->sku
                ?: "ID: {$this->id}";

        return filled($productTitle)
            ? "{$productTitle} — {$suffix}"
            : $suffix;
    }

    /* ======================== Inheritance ======================== */

    /**
     * Эффективная валюта:
     * собственная валюта варианта или валюта товара.
     */
    public function effectiveCurrency(): ?Currency
    {
        if (! $this->relationLoaded('currency')) {
            $this->load('currency');
        }

        if ($this->currency) {
            return $this->currency;
        }

        if (! $this->relationLoaded('product')) {
            $this->load('product.currency');
        }

        return $this->product?->currency;
    }

    /**
     * Эффективный ID валюты.
     */
    public function effectiveCurrencyId(): ?int
    {
        return $this->currency_id
            ?: $this->product?->currency_id;
    }

    /**
     * Эффективная текущая цена.
     */
    public function effectivePrice(): ?string
    {
        return $this->price !== null
            ? $this->price
            : $this->product?->price;
    }

    /**
     * Эффективная старая цена.
     */
    public function effectiveOldPrice(): ?string
    {
        return $this->old_price !== null
            ? $this->old_price
            : $this->product?->old_price;
    }

    /**
     * Эффективная закупочная цена.
     */
    public function effectivePurchasePrice(): ?string
    {
        return $this->purchase_price !== null
            ? $this->purchase_price
            : $this->product?->purchase_price;
    }

    /**
     * Эффективная оптовая цена.
     */
    public function effectiveWholesalePrice(): ?string
    {
        return $this->wholesale_price !== null
            ? $this->wholesale_price
            : $this->product?->wholesale_price;
    }

    /**
     * Эффективное минимальное оптовое количество.
     */
    public function effectiveWholesaleMinQuantity(): ?int
    {
        return $this->wholesale_min_quantity !== null
            ? (int) $this->wholesale_min_quantity
            : $this->product?->wholesale_min_quantity;
    }

    /**
     * Эффективный вес.
     */
    public function effectiveWeight(): ?string
    {
        return $this->weight !== null
            ? $this->weight
            : $this->product?->weight;
    }

    /**
     * Эффективная длина.
     */
    public function effectiveLength(): ?string
    {
        return $this->length !== null
            ? $this->length
            : $this->product?->length;
    }

    /**
     * Эффективная ширина.
     */
    public function effectiveWidth(): ?string
    {
        return $this->width !== null
            ? $this->width
            : $this->product?->width;
    }

    /**
     * Эффективная высота.
     */
    public function effectiveHeight(): ?string
    {
        return $this->height !== null
            ? $this->height
            : $this->product?->height;
    }

    /**
     * Использует ли вариант собственную цену.
     */
    public function hasOwnPrice(): bool
    {
        return $this->price !== null;
    }

    /**
     * Использует ли вариант собственную валюту.
     */
    public function hasOwnCurrency(): bool
    {
        return $this->currency_id !== null;
    }

    /**
     * Использует ли вариант собственные физические параметры.
     */
    public function hasOwnDimensions(): bool
    {
        return $this->weight !== null
            || $this->length !== null
            || $this->width !== null
            || $this->height !== null;
    }

    /* ======================== Helpers ======================== */

    /**
     * Вариант активен.
     */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /**
     * Вариант является основным.
     */
    public function isDefault(): bool
    {
        return (bool) $this->is_default;
    }

    /**
     * Вариант одобрен.
     */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /**
     * Вариант ожидает модерации.
     */
    public function isPending(): bool
    {
        return (int) $this->moderation_status === 0;
    }

    /**
     * Вариант отклонён.
     */
    public function isRejected(): bool
    {
        return (int) $this->moderation_status === 2;
    }

    /**
     * Вариант опубликован.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && (bool) $this->activity
            && $this->published_at !== null;
    }

    /**
     * Вариант находится в текущем окне показа.
     */
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

    /**
     * Вариант находится в наличии.
     */
    public function hasStock(): bool
    {
        return (bool) $this->in_stock
            && (int) $this->quantity > 0;
    }

    /**
     * У варианта или товара есть корректная старая цена.
     */
    public function hasOldPrice(): bool
    {
        $price = $this->effectivePrice();
        $oldPrice = $this->effectiveOldPrice();

        return $price !== null
            && $oldPrice !== null
            && (float) $oldPrice > (float) $price;
    }

    /**
     * У варианта или товара настроена оптовая цена.
     */
    public function hasWholesalePrice(): bool
    {
        $price = $this->effectiveWholesalePrice();
        $minimum = $this->effectiveWholesaleMinQuantity();

        return $price !== null
            && (float) $price > 0
            && $minimum !== null
            && $minimum > 0;
    }

    /**
     * Используется ли вариант хотя бы в одном комплекте.
     */
    public function isUsedInBundles(): bool
    {
        if (isset($this->bundle_items_count)) {
            return (int) $this->bundle_items_count > 0;
        }

        return $this->relationLoaded('bundleItems')
            ? $this->bundleItems->isNotEmpty()
            : $this->bundleItems()->exists();
    }

    /**
     * Количество позиций комплектов с этим вариантом.
     */
    public function bundleItemsCount(): int
    {
        if (isset($this->bundle_items_count)) {
            return (int) $this->bundle_items_count;
        }

        return $this->relationLoaded('bundleItems')
            ? $this->bundleItems->count()
            : $this->bundleItems()->count();
    }

    /* ======================== Scopes ======================== */

    /**
     * Сортировка по умолчанию.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('market_product_variants.sort')
            ->orderByDesc('market_product_variants.id');
    }

    /**
     * Варианты конкретного товара.
     */
    public function scopeForProduct(
        Builder $query,
        int $productId
    ): Builder {
        return $query->where(
            'market_product_variants.market_product_id',
            $productId
        );
    }

    /**
     * Только активные варианты.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'market_product_variants.activity',
            true
        );
    }

    /**
     * Только неактивные варианты.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where(
            'market_product_variants.activity',
            false
        );
    }

    /**
     * Основной вариант товара.
     */
    public function scopeDefaultVariant(Builder $query): Builder
    {
        return $query->where(
            'market_product_variants.is_default',
            true
        );
    }

    /**
     * Только опубликованные варианты.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where(
                'market_product_variants.status',
                'published'
            )
            ->where(
                'market_product_variants.activity',
                true
            )
            ->whereNotNull(
                'market_product_variants.published_at'
            );
    }

    /**
     * Только одобренные варианты.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where(
            'market_product_variants.moderation_status',
            1
        );
    }

    /**
     * Только варианты в наличии.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query
            ->where(
                'market_product_variants.in_stock',
                true
            )
            ->where(
                'market_product_variants.quantity',
                '>',
                0
            );
    }

    /**
     * Только варианты без остатка.
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where(function (Builder $stockQuery) {
            $stockQuery
                ->where(
                    'market_product_variants.in_stock',
                    false
                )
                ->orWhere(
                    'market_product_variants.quantity',
                    '<=',
                    0
                );
        });
    }

    /**
     * Текущее окно показа.
     */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $fromQuery) {
                $fromQuery
                    ->whereNull(
                        'market_product_variants.show_from_at'
                    )
                    ->orWhere(
                        'market_product_variants.show_from_at',
                        '<=',
                        now()
                    );
            })
            ->where(function (Builder $toQuery) {
                $toQuery
                    ->whereNull(
                        'market_product_variants.show_to_at'
                    )
                    ->orWhere(
                        'market_product_variants.show_to_at',
                        '>=',
                        now()
                    );
            });
    }

    /**
     * Публично доступные варианты.
     *
     * Публичность родительского товара следует проверять
     * одновременно с публичностью варианта.
     */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->inShowWindow()
            ->whereHas('product', function (Builder $productQuery) {
                $productQuery->forPublic();
            });
    }

    /**
     * Поиск вариантов.
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

        return $query->where(function (Builder $searchQuery) use (
            $term,
            $locale
        ) {
            $searchQuery
                ->where(
                    'market_product_variants.code',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'market_product_variants.sku',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'market_product_variants.vendor_code',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'market_product_variants.barcode',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'market_product_variants.status',
                    'like',
                    "%{$term}%"
                )
                ->orWhere(
                    'market_product_variants.moderation_note',
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
                            ->where('locale', $locale)
                            ->where(function (Builder $textQuery) use ($term) {
                                $textQuery
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
                                    )
                                    ->orWhere(
                                        'meta_title',
                                        'like',
                                        "%{$term}%"
                                    )
                                    ->orWhere(
                                        'meta_keywords',
                                        'like',
                                        "%{$term}%"
                                    )
                                    ->orWhere(
                                        'meta_desc',
                                        'like',
                                        "%{$term}%"
                                    );
                            });
                    }
                )
                ->orWhereHas(
                    'product.translations',
                    function (Builder $productTranslationQuery) use (
                        $term,
                        $locale
                    ) {
                        $productTranslationQuery
                            ->where('locale', $locale)
                            ->where(
                                'title',
                                'like',
                                "%{$term}%"
                            );
                    }
                )
                ->orWhereHas(
                    'product.owner',
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
        });
    }

    /**
     * Сортировка и фильтрация вариантов товаров.
     *
     * Все ключи этого scope должны полностью совпадать с:
     * - SortSelect.vue;
     * - sortedVariants в Index.vue.
     */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale ?: app()->getLocale();

        /**
         * Обычная сортировка по полю варианта.
         */
        $orderByVariantField = function (
            string $field,
            string $direction
        ) use ($query): Builder {
            return $query
                ->orderBy(
                    "market_product_variants.{$field}",
                    $direction
                )
                ->orderByDesc('market_product_variants.id');
        };

        /**
         * Добавить родительский товар для сортировки
         * по эффективным наследуемым значениям.
         */
        $joinProduct = function () use ($query): Builder {
            return $query
                ->leftJoin(
                    'market_products as mpv_product_sort',
                    'mpv_product_sort.id',
                    '=',
                    'market_product_variants.market_product_id'
                )
                ->select('market_product_variants.*');
        };

        /**
         * Сортировка по эффективному значению:
         * собственное поле варианта либо поле товара.
         */
        $orderByEffectiveField = function (
            string $field,
            string $direction
        ) use ($joinProduct): Builder {
            return $joinProduct()
                ->orderByRaw(
                    "COALESCE(
                    market_product_variants.{$field},
                    mpv_product_sort.{$field}
                ) {$direction}"
                )
                ->orderByDesc('market_product_variants.id');
        };

        return match ($sort) {
            /** ID */
            'idAsc' => $orderByVariantField('id', 'asc'),
            'idDesc' => $orderByVariantField('id', 'desc'),

            /** Ручная сортировка */
            'sortAsc' => $orderByVariantField('sort', 'asc'),
            'sortDesc' => $orderByVariantField('sort', 'desc'),

            /** Название варианта */
            'titleAsc', 'titleDesc' => $query
                ->leftJoin(
                    'market_product_variant_translations as mpvt_sort',
                    function ($join) use ($locale): void {
                        $join
                            ->on(
                                'mpvt_sort.market_product_variant_id',
                                '=',
                                'market_product_variants.id'
                            )
                            ->where(
                                'mpvt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->orderBy(
                    'mpvt_sort.title',
                    $sort === 'titleAsc' ? 'asc' : 'desc'
                )
                ->orderByDesc('market_product_variants.id')
                ->select('market_product_variants.*'),

            /** Название родительского товара */
            'productTitleAsc', 'productTitleDesc' => $query
                ->leftJoin(
                    'market_products as mpv_product_title_sort',
                    'mpv_product_title_sort.id',
                    '=',
                    'market_product_variants.market_product_id'
                )
                ->leftJoin(
                    'market_product_translations as mpt_variant_sort',
                    function ($join) use ($locale): void {
                        $join
                            ->on(
                                'mpt_variant_sort.market_product_id',
                                '=',
                                'mpv_product_title_sort.id'
                            )
                            ->where(
                                'mpt_variant_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->orderBy(
                    'mpt_variant_sort.title',
                    $sort === 'productTitleAsc' ? 'asc' : 'desc'
                )
                ->orderByDesc('market_product_variants.id')
                ->select('market_product_variants.*'),

            /** Торговые коды */
            'codeAsc' => $orderByVariantField('code', 'asc'),
            'codeDesc' => $orderByVariantField('code', 'desc'),

            'skuAsc' => $orderByVariantField('sku', 'asc'),
            'skuDesc' => $orderByVariantField('sku', 'desc'),

            'vendorCodeAsc' => $orderByVariantField('vendor_code', 'asc'),
            'vendorCodeDesc' => $orderByVariantField('vendor_code', 'desc'),

            'barcodeAsc' => $orderByVariantField('barcode', 'asc'),
            'barcodeDesc' => $orderByVariantField('barcode', 'desc'),

            /** Эффективные цены */
            'priceAsc' => $orderByEffectiveField('price', 'asc'),
            'priceDesc' => $orderByEffectiveField('price', 'desc'),

            'oldPriceAsc' => $orderByEffectiveField('old_price', 'asc'),
            'oldPriceDesc' => $orderByEffectiveField('old_price', 'desc'),

            'purchasePriceAsc' => $orderByEffectiveField(
                'purchase_price',
                'asc'
            ),

            'purchasePriceDesc' => $orderByEffectiveField(
                'purchase_price',
                'desc'
            ),

            'wholesalePriceAsc' => $orderByEffectiveField(
                'wholesale_price',
                'asc'
            ),

            'wholesalePriceDesc' => $orderByEffectiveField(
                'wholesale_price',
                'desc'
            ),

            'wholesaleMinQuantityAsc' => $orderByEffectiveField(
                'wholesale_min_quantity',
                'asc'
            ),

            'wholesaleMinQuantityDesc' => $orderByEffectiveField(
                'wholesale_min_quantity',
                'desc'
            ),

            /** Остаток и наличие */
            'quantityAsc' => $orderByVariantField('quantity', 'asc'),
            'quantityDesc' => $orderByVariantField('quantity', 'desc'),

            'inStockAsc' => $orderByVariantField('in_stock', 'asc'),
            'inStockDesc' => $orderByVariantField('in_stock', 'desc'),

            'inStock' => $query
                ->inStock()
                ->orderByDesc('market_product_variants.id'),

            'outOfStock' => $query
                ->outOfStock()
                ->orderByDesc('market_product_variants.id'),

            /** Эффективные физические параметры */
            'weightAsc' => $orderByEffectiveField('weight', 'asc'),
            'weightDesc' => $orderByEffectiveField('weight', 'desc'),

            'lengthAsc' => $orderByEffectiveField('length', 'asc'),
            'lengthDesc' => $orderByEffectiveField('length', 'desc'),

            'widthAsc' => $orderByEffectiveField('width', 'asc'),
            'widthDesc' => $orderByEffectiveField('width', 'desc'),

            'heightAsc' => $orderByEffectiveField('height', 'asc'),
            'heightDesc' => $orderByEffectiveField('height', 'desc'),

            /** Основной вариант */
            'defaultAsc' => $orderByVariantField('is_default', 'asc'),
            'defaultDesc' => $orderByVariantField('is_default', 'desc'),

            'default' => $query
                ->where(
                    'market_product_variants.is_default',
                    true
                )
                ->orderByDesc('market_product_variants.id'),

            'notDefault' => $query
                ->where(
                    'market_product_variants.is_default',
                    false
                )
                ->orderByDesc('market_product_variants.id'),

            /** Количество значений характеристик */
            'valuesAsc' => $query
                ->withCount('values')
                ->orderBy('values_count', 'asc')
                ->orderByDesc('market_product_variants.id'),

            'valuesDesc' => $query
                ->withCount('values')
                ->orderBy('values_count', 'desc')
                ->orderByDesc('market_product_variants.id'),

            /** Количество изображений */
            'imagesAsc' => $query
                ->withCount('images')
                ->orderBy('images_count', 'asc')
                ->orderByDesc('market_product_variants.id'),

            'imagesDesc' => $query
                ->withCount('images')
                ->orderBy('images_count', 'desc')
                ->orderByDesc('market_product_variants.id'),

            /** Активность */
            'activityAsc' => $orderByVariantField('activity', 'asc'),
            'activityDesc' => $orderByVariantField('activity', 'desc'),

            'activity' => $query
                ->active()
                ->orderByDesc('market_product_variants.id'),

            'inactive' => $query
                ->inactive()
                ->orderByDesc('market_product_variants.id'),

            /** Статус публикации */
            'statusAsc' => $orderByVariantField('status', 'asc'),
            'statusDesc' => $orderByVariantField('status', 'desc'),

            'statusDraft' => $query
                ->where(
                    'market_product_variants.status',
                    'draft'
                )
                ->orderByDesc('market_product_variants.id'),

            'statusPublished' => $query
                ->where(
                    'market_product_variants.status',
                    'published'
                )
                ->orderByDesc('market_product_variants.id'),

            'statusArchived' => $query
                ->where(
                    'market_product_variants.status',
                    'archived'
                )
                ->orderByDesc('market_product_variants.id'),

            /** Статус модерации */
            'moderationStatusAsc' => $orderByVariantField(
                'moderation_status',
                'asc'
            ),

            'moderationStatusDesc' => $orderByVariantField(
                'moderation_status',
                'desc'
            ),

            'moderationPending' => $query
                ->where(
                    'market_product_variants.moderation_status',
                    0
                )
                ->orderByDesc('market_product_variants.id'),

            'moderationApproved' => $query
                ->where(
                    'market_product_variants.moderation_status',
                    1
                )
                ->orderByDesc('market_product_variants.id'),

            'moderationRejected' => $query
                ->where(
                    'market_product_variants.moderation_status',
                    2
                )
                ->orderByDesc('market_product_variants.id'),

            /** Даты */
            'publishedAtAsc' => $orderByVariantField(
                'published_at',
                'asc'
            ),

            'publishedAtDesc' => $orderByVariantField(
                'published_at',
                'desc'
            ),

            'showFromAtAsc' => $orderByVariantField(
                'show_from_at',
                'asc'
            ),

            'showFromAtDesc' => $orderByVariantField(
                'show_from_at',
                'desc'
            ),

            'showToAtAsc' => $orderByVariantField(
                'show_to_at',
                'asc'
            ),

            'showToAtDesc' => $orderByVariantField(
                'show_to_at',
                'desc'
            ),

            'createdAtAsc' => $orderByVariantField(
                'created_at',
                'asc'
            ),

            'createdAtDesc' => $orderByVariantField(
                'created_at',
                'desc'
            ),

            'updatedAtAsc' => $orderByVariantField(
                'updated_at',
                'asc'
            ),

            'updatedAtDesc' => $orderByVariantField(
                'updated_at',
                'desc'
            ),

            /** Сортировка по умолчанию */
            default => $query
                ->orderByDesc('market_product_variants.id'),
        };
    }
}
