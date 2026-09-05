<?php

namespace App\Models\Admin\Market\MarketProductBundle;

use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
use App\Models\Admin\Market\MarketShop\MarketShop;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketProductBundle extends Model
{
    use HasFactory;

    protected $table = 'market_product_bundles';

    protected $fillable = [
        'user_id',

        'market_company_id',
        'market_shop_id',

        'url',
        'sku',
        'vendor_code',
        'barcode',

        'currency_id',
        'calculate_price',

        'price',
        'old_price',
        'purchase_price',
        'wholesale_price',
        'wholesale_min_quantity',

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

        'currency_id' => 'integer',
        'calculate_price' => 'boolean',

        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'wholesale_min_quantity' => 'integer',

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

    /** Создатель / владелец комплекта. */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /** Модератор комплекта. */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'moderated_by'
        );
    }

    /** Компания-поставщик. */
    public function company(): BelongsTo
    {
        return $this->belongsTo(
            MarketCompany::class,
            'market_company_id'
        );
    }

    /** Магазин комплекта. */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(
            MarketShop::class,
            'market_shop_id'
        );
    }

    /** Валюта комплекта. */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(
            Currency::class,
            'currency_id'
        );
    }

    /** Все переводы комплекта. */
    public function translations(): HasMany
    {
        return $this->hasMany(
            MarketProductBundleTranslation::class,
            'market_product_bundle_id'
        );
    }

    /** Перевод текущей локали. */
    public function translation(): HasOne
    {
        return $this->hasOne(
            MarketProductBundleTranslation::class,
            'market_product_bundle_id'
        )
            ->where('locale', app()->getLocale());
    }

    /** Все позиции комплекта. */
    public function items(): HasMany
    {
        return $this->hasMany(
            MarketProductBundleItem::class,
            'market_product_bundle_id'
        )
            ->orderBy('market_product_bundle_items.sort')
            ->orderBy('market_product_bundle_items.id');
    }

    /** Только активные позиции комплекта. */
    public function activeItems(): HasMany
    {
        return $this->hasMany(
            MarketProductBundleItem::class,
            'market_product_bundle_id'
        )
            ->where('market_product_bundle_items.activity', true)
            ->orderBy('market_product_bundle_items.sort')
            ->orderBy('market_product_bundle_items.id');
    }

    /** Изображения комплекта. */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProductBundleImage::class,
            'market_product_bundle_has_images',
            'market_product_bundle_id',
            'market_product_bundle_image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /* ======================== Translations ======================== */

    /**
     * Получить перевод указанной локали
     * с резервной локалью.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallback = null
    ): ?MarketProductBundleTranslation {
        $locale = $locale ?: app()->getLocale();
        $fallback = $fallback ?: config('app.fallback_locale', 'ru');

        if (! $this->relationLoaded('translations')) {
            $this->load('translations');
        }

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }

    /** Получить название комплекта. */
    public function getTranslatedTitle(
        ?string $locale = null,
        ?string $fallback = null
    ): ?string {
        return $this->translationOrFallback(
            locale: $locale,
            fallback: $fallback
        )?->title;
    }

    /* ======================== Price helpers ======================== */

    /** Цена комплекта рассчитывается автоматически. */
    public function usesCalculatedPrice(): bool
    {
        return (bool) $this->calculate_price;
    }

    /** Цена комплекта задаётся вручную. */
    public function usesManualPrice(): bool
    {
        return ! $this->usesCalculatedPrice();
    }

    /**
     * Получить активные позиции для расчётов.
     *
     * @return Collection<int, MarketProductBundleItem>
     */
    protected function itemsForCalculation(): Collection
    {
        if ($this->relationLoaded('activeItems')) {
            return $this->activeItems;
        }

        if ($this->relationLoaded('items')) {
            return $this->items
                ->where('activity', true)
                ->values();
        }

        return $this->activeItems()
            ->with([
                'product.currency',
                'variant.currency',
                'variant.product.currency',
            ])
            ->get();
    }

    /** Рассчитать полную цену состава комплекта. */
    public function calculatedPrice(): float
    {
        return round(
            $this->itemsForCalculation()->sum(
                fn (MarketProductBundleItem $item): float =>
                $item->totalPrice()
            ),
            2
        );
    }

    /**
     * Получить эффективную цену комплекта.
     *
     * При calculate_price=true цена рассчитывается
     * по активным позициям комплекта.
     */
    public function effectivePrice(): float
    {
        return $this->usesCalculatedPrice()
            ? $this->calculatedPrice()
            : round((float) $this->price, 2);
    }

    /** Есть старая цена. */
    public function hasOldPrice(): bool
    {
        return ! is_null($this->old_price)
            && (float) $this->old_price > $this->effectivePrice();
    }

    /** Есть оптовая цена. */
    public function hasWholesalePrice(): bool
    {
        return ! is_null($this->wholesale_price)
            && (float) $this->wholesale_price > 0
            && ! is_null($this->wholesale_min_quantity)
            && (int) $this->wholesale_min_quantity > 0;
    }

    /** Размер экономии относительно старой цены. */
    public function savingAmount(): float
    {
        if (! $this->hasOldPrice()) {
            return 0.0;
        }

        return round(
            (float) $this->old_price - $this->effectivePrice(),
            2
        );
    }

    /** Процент экономии относительно старой цены. */
    public function savingPercent(): float
    {
        if (
            ! $this->hasOldPrice()
            || (float) $this->old_price <= 0
        ) {
            return 0.0;
        }

        return round(
            ($this->savingAmount() / (float) $this->old_price) * 100,
            2
        );
    }

    /* ======================== Availability helpers ======================== */

    /** Есть позиции в комплекте. */
    public function hasItems(): bool
    {
        if (isset($this->items_count)) {
            return (int) $this->items_count > 0;
        }

        return $this->relationLoaded('items')
            ? $this->items->isNotEmpty()
            : $this->items()->exists();
    }

    /** Количество позиций комплекта. */
    public function itemsCount(): int
    {
        if (isset($this->items_count)) {
            return (int) $this->items_count;
        }

        return $this->relationLoaded('items')
            ? $this->items->count()
            : $this->items()->count();
    }

    /**
     * Получить доступное количество комплектов.
     *
     * Результат определяется позицией,
     * которой хватает на минимальное число комплектов.
     */
    public function availableQuantity(): int
    {
        $items = $this->itemsForCalculation();

        if ($items->isEmpty()) {
            return 0;
        }

        $quantities = $items->map(
            function (MarketProductBundleItem $item): int {
                $requiredQuantity = max(
                    1,
                    (int) $item->quantity
                );

                return intdiv(
                    $item->availableQuantity(),
                    $requiredQuantity
                );
            }
        );

        return (int) ($quantities->min() ?? 0);
    }

    /** Комплект доступен для продажи. */
    public function hasStock(): bool
    {
        return $this->availableQuantity() > 0;
    }

    /* ======================== State helpers ======================== */

    /** Комплект активен. */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Комплект одобрен. */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Комплект опубликован. */
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->isActive()
            && ! is_null($this->published_at);
    }

    /** Комплект находится в окне показа. */
    public function isPublishedNow(): bool
    {
        $now = now();

        if (
            $this->show_from_at
            && $now->lt($this->show_from_at)
        ) {
            return false;
        }

        if (
            $this->show_to_at
            && $now->gt($this->show_to_at)
        ) {
            return false;
        }

        return true;
    }

    /* ======================== Scopes ======================== */

    /** Сортировка по умолчанию. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('market_product_bundles.sort')
            ->orderByDesc('market_product_bundles.id');
    }

    /** Только активные комплекты. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundles.activity',
            true
        );
    }

    /** Только неактивные комплекты. */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundles.activity',
            false
        );
    }

    /** Только опубликованные комплекты. */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where(
                'market_product_bundles.status',
                'published'
            )
            ->where(
                'market_product_bundles.activity',
                true
            )
            ->whereNotNull(
                'market_product_bundles.published_at'
            );
    }

    /** Только одобренные комплекты. */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundles.moderation_status',
            1
        );
    }

    /** Только комплекты с позициями. */
    public function scopeHasItems(Builder $query): Builder
    {
        return $query->whereHas('items');
    }

    /** Только пустые комплекты. */
    public function scopeWithoutItems(Builder $query): Builder
    {
        return $query->whereDoesntHave('items');
    }

    /** Только комплекты с автоматическим расчётом цены. */
    public function scopeCalculatedPrice(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundles.calculate_price',
            true
        );
    }

    /** Только комплекты с ручной ценой. */
    public function scopeManualPrice(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundles.calculate_price',
            false
        );
    }

    /** Новинки. */
    public function scopeNew(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundles.is_new',
            true
        );
    }

    /** Хиты продаж. */
    public function scopeHit(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundles.is_hit',
            true
        );
    }

    /** Распродажа. */
    public function scopeSale(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundles.is_sale',
            true
        );
    }

    /** Левая рекламная зона. */
    public function scopeLeft(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundles.left',
            true
        );
    }

    /** Главная рекламная зона. */
    public function scopeMain(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundles.main',
            true
        );
    }

    /** Правая рекламная зона. */
    public function scopeRight(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundles.right',
            true
        );
    }

    /** Окно показа. */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $subQuery): void {
                $subQuery
                    ->whereNull(
                        'market_product_bundles.show_from_at'
                    )
                    ->orWhere(
                        'market_product_bundles.show_from_at',
                        '<=',
                        now()
                    );
            })
            ->where(function (Builder $subQuery): void {
                $subQuery
                    ->whereNull(
                        'market_product_bundles.show_to_at'
                    )
                    ->orWhere(
                        'market_product_bundles.show_to_at',
                        '>=',
                        now()
                    );
            });
    }

    /** Публично доступные комплекты. */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->inShowWindow()
            ->hasItems();
    }

    /**
     * Поиск комплектов.
     *
     * Backend-контракт:
     * - системные поля комплекта;
     * - текущий перевод комплекта;
     * - компания и магазин по текущей локали;
     * - владелец;
     * - названия товаров и вариантов состава по текущей локали.
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

        return $query->where(
            function (Builder $subQuery) use ($term, $locale): void {
                $subQuery
                    ->where(
                        'market_product_bundles.url',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'market_product_bundles.sku',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'market_product_bundles.vendor_code',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'market_product_bundles.barcode',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'market_product_bundles.status',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'market_product_bundles.moderation_note',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhereHas(
                        'translations',
                        function (Builder $translationQuery) use ($term, $locale): void {
                            $translationQuery
                                ->where('locale', $locale)
                                ->where(
                                    function (Builder $textQuery) use ($term): void {
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
                                            );
                                    }
                                );
                        }
                    )
                    ->orWhereHas(
                        'company.translations',
                        function (Builder $companyQuery) use ($term, $locale): void {
                            $companyQuery
                                ->where('locale', $locale)
                                ->where(
                                    'title',
                                    'like',
                                    "%{$term}%"
                                );
                        }
                    )
                    ->orWhereHas(
                        'shop.translations',
                        function (Builder $shopQuery) use ($term, $locale): void {
                            $shopQuery
                                ->where('locale', $locale)
                                ->where(
                                    'title',
                                    'like',
                                    "%{$term}%"
                                );
                        }
                    )
                    ->orWhereHas(
                        'owner',
                        function (Builder $ownerQuery) use ($term): void {
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
                    )
                    ->orWhereHas(
                        'items.product.translations',
                        function (Builder $productQuery) use ($term, $locale): void {
                            $productQuery
                                ->where('locale', $locale)
                                ->where(
                                    'title',
                                    'like',
                                    "%{$term}%"
                                );
                        }
                    )
                    ->orWhereHas(
                        'items.variant.translations',
                        function (Builder $variantQuery) use ($term, $locale): void {
                            $variantQuery
                                ->where('locale', $locale)
                                ->where(
                                    'title',
                                    'like',
                                    "%{$term}%"
                                );
                        }
                    );
            }
        );
    }

    /**
     * Сортировка и фильтрация комплектов товаров.
     */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale ?: app()->getLocale();

        /** Сортировка по колонке основной таблицы с детерминированным tie-break. */
        $orderByColumn = static function (
            Builder $builder,
            string $column,
            string $direction
        ): Builder {
            return $builder
                ->orderBy($column, $direction)
                ->orderByDesc('market_product_bundles.id');
        };

        /** Фильтрация по булевому полю с backend/frontend parity. */
        $filterBoolean = static function (
            Builder $builder,
            string $column,
            bool $value
        ): Builder {
            return $builder
                ->where($column, $value)
                ->orderByDesc('market_product_bundles.id');
        };

        return match ($sort) {
            /** ID — без дополнительного tie-break. */
            'idAsc' => $query
                ->orderBy('market_product_bundles.id'),

            'idDesc' => $query
                ->orderByDesc('market_product_bundles.id'),

            /** Ручная сортировка. */
            'sortAsc' => $orderByColumn(
                $query,
                'market_product_bundles.sort',
                'asc'
            ),

            'sortDesc' => $orderByColumn(
                $query,
                'market_product_bundles.sort',
                'desc'
            ),

            /** Название. */
            'titleAsc', 'titleDesc' => $query
                ->leftJoin(
                    'market_product_bundle_translations as mpbt_sort',
                    function ($join) use ($locale): void {
                        $join
                            ->on(
                                'mpbt_sort.market_product_bundle_id',
                                '=',
                                'market_product_bundles.id'
                            )
                            ->where(
                                'mpbt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect('market_product_bundles.*')
                ->orderBy(
                    'mpbt_sort.title',
                    $sort === 'titleAsc' ? 'asc' : 'desc'
                )
                ->orderByDesc('market_product_bundles.id'),

            /** Коды. */
            'urlAsc' => $orderByColumn($query, 'market_product_bundles.url', 'asc'),
            'urlDesc' => $orderByColumn($query, 'market_product_bundles.url', 'desc'),
            'skuAsc' => $orderByColumn($query, 'market_product_bundles.sku', 'asc'),
            'skuDesc' => $orderByColumn($query, 'market_product_bundles.sku', 'desc'),
            'vendorCodeAsc' => $orderByColumn($query, 'market_product_bundles.vendor_code', 'asc'),
            'vendorCodeDesc' => $orderByColumn($query, 'market_product_bundles.vendor_code', 'desc'),
            'barcodeAsc' => $orderByColumn($query, 'market_product_bundles.barcode', 'asc'),
            'barcodeDesc' => $orderByColumn($query, 'market_product_bundles.barcode', 'desc'),

            /** Компания. */
            'companyAsc', 'companyDesc' => $query
                ->leftJoin(
                    'market_company_translations as mct_bundle_sort',
                    function ($join) use ($locale): void {
                        $join
                            ->on(
                                'mct_bundle_sort.market_company_id',
                                '=',
                                'market_product_bundles.market_company_id'
                            )
                            ->where(
                                'mct_bundle_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect('market_product_bundles.*')
                ->orderBy(
                    'mct_bundle_sort.title',
                    $sort === 'companyAsc' ? 'asc' : 'desc'
                )
                ->orderByDesc('market_product_bundles.id'),

            /** Магазин. */
            'shopAsc', 'shopDesc' => $query
                ->leftJoin(
                    'market_shop_translations as mst_bundle_sort',
                    function ($join) use ($locale): void {
                        $join
                            ->on(
                                'mst_bundle_sort.market_shop_id',
                                '=',
                                'market_product_bundles.market_shop_id'
                            )
                            ->where(
                                'mst_bundle_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->addSelect('market_product_bundles.*')
                ->orderBy(
                    'mst_bundle_sort.title',
                    $sort === 'shopAsc' ? 'asc' : 'desc'
                )
                ->orderByDesc('market_product_bundles.id'),

            /** Владелец. */
            'ownerNameAsc', 'ownerNameDesc' => $query
                ->leftJoin(
                    'users as bundle_owner_sort',
                    'bundle_owner_sort.id',
                    '=',
                    'market_product_bundles.user_id'
                )
                ->addSelect('market_product_bundles.*')
                ->orderBy(
                    'bundle_owner_sort.name',
                    $sort === 'ownerNameAsc' ? 'asc' : 'desc'
                )
                ->orderByDesc('market_product_bundles.id'),

            'ownerEmailAsc', 'ownerEmailDesc' => $query
                ->leftJoin(
                    'users as bundle_owner_sort',
                    'bundle_owner_sort.id',
                    '=',
                    'market_product_bundles.user_id'
                )
                ->addSelect('market_product_bundles.*')
                ->orderBy(
                    'bundle_owner_sort.email',
                    $sort === 'ownerEmailAsc' ? 'asc' : 'desc'
                )
                ->orderByDesc('market_product_bundles.id'),

            /** Режим формирования цены. */
            'calculatePriceAsc' => $orderByColumn($query, 'market_product_bundles.calculate_price', 'asc'),
            'calculatePriceDesc' => $orderByColumn($query, 'market_product_bundles.calculate_price', 'desc'),
            'calculatedPrice' => $filterBoolean($query, 'market_product_bundles.calculate_price', true),
            'manualPrice' => $filterBoolean($query, 'market_product_bundles.calculate_price', false),

            /** Цены. */
            'priceAsc' => $orderByColumn($query, 'market_product_bundles.price', 'asc'),
            'priceDesc' => $orderByColumn($query, 'market_product_bundles.price', 'desc'),
            'oldPriceAsc' => $orderByColumn($query, 'market_product_bundles.old_price', 'asc'),
            'oldPriceDesc' => $orderByColumn($query, 'market_product_bundles.old_price', 'desc'),
            'purchasePriceAsc' => $orderByColumn($query, 'market_product_bundles.purchase_price', 'asc'),
            'purchasePriceDesc' => $orderByColumn($query, 'market_product_bundles.purchase_price', 'desc'),
            'wholesalePriceAsc' => $orderByColumn($query, 'market_product_bundles.wholesale_price', 'asc'),
            'wholesalePriceDesc' => $orderByColumn($query, 'market_product_bundles.wholesale_price', 'desc'),
            'wholesaleMinQuantityAsc' => $orderByColumn($query, 'market_product_bundles.wholesale_min_quantity', 'asc'),
            'wholesaleMinQuantityDesc' => $orderByColumn($query, 'market_product_bundles.wholesale_min_quantity', 'desc'),

            /** Позиции комплекта — aliases уже предоставляет Controller::withCount(). */
            'itemsAsc' => $query
                ->orderBy('items_count')
                ->orderByDesc('market_product_bundles.id'),

            'itemsDesc' => $query
                ->orderByDesc('items_count')
                ->orderByDesc('market_product_bundles.id'),

            'hasItems' => $query
                ->whereHas('items')
                ->orderByDesc('market_product_bundles.id'),

            'withoutItems' => $query
                ->whereDoesntHave('items')
                ->orderByDesc('market_product_bundles.id'),

            /** Изображения — alias images_count уже предоставляет Controller. */
            'imagesAsc' => $query
                ->orderBy('images_count')
                ->orderByDesc('market_product_bundles.id'),

            'imagesDesc' => $query
                ->orderByDesc('images_count')
                ->orderByDesc('market_product_bundles.id'),

            /** Статистика. */
            'viewsAsc' => $orderByColumn($query, 'market_product_bundles.views', 'asc'),
            'viewsDesc' => $orderByColumn($query, 'market_product_bundles.views', 'desc'),
            'likesAsc' => $orderByColumn($query, 'market_product_bundles.likes_count', 'asc'),
            'likesDesc' => $orderByColumn($query, 'market_product_bundles.likes_count', 'desc'),
            'ratingAsc' => $orderByColumn($query, 'market_product_bundles.rating_avg', 'asc'),
            'ratingDesc' => $orderByColumn($query, 'market_product_bundles.rating_avg', 'desc'),
            'ratingCountAsc' => $orderByColumn($query, 'market_product_bundles.rating_count', 'asc'),
            'ratingCountDesc' => $orderByColumn($query, 'market_product_bundles.rating_count', 'desc'),

            /** Активность. */
            'activityAsc' => $orderByColumn($query, 'market_product_bundles.activity', 'asc'),
            'activityDesc' => $orderByColumn($query, 'market_product_bundles.activity', 'desc'),
            'activity' => $filterBoolean($query, 'market_product_bundles.activity', true),
            'inactive' => $filterBoolean($query, 'market_product_bundles.activity', false),

            /** Маркетинговые признаки. */
            'newAsc' => $orderByColumn($query, 'market_product_bundles.is_new', 'asc'),
            'newDesc' => $orderByColumn($query, 'market_product_bundles.is_new', 'desc'),
            'new' => $filterBoolean($query, 'market_product_bundles.is_new', true),
            'notNew' => $filterBoolean($query, 'market_product_bundles.is_new', false),

            'hitAsc' => $orderByColumn($query, 'market_product_bundles.is_hit', 'asc'),
            'hitDesc' => $orderByColumn($query, 'market_product_bundles.is_hit', 'desc'),
            'hit' => $filterBoolean($query, 'market_product_bundles.is_hit', true),
            'notHit' => $filterBoolean($query, 'market_product_bundles.is_hit', false),

            'saleAsc' => $orderByColumn($query, 'market_product_bundles.is_sale', 'asc'),
            'saleDesc' => $orderByColumn($query, 'market_product_bundles.is_sale', 'desc'),
            'sale' => $filterBoolean($query, 'market_product_bundles.is_sale', true),
            'notSale' => $filterBoolean($query, 'market_product_bundles.is_sale', false),

            /** Рекламные позиции. */
            'leftAsc' => $orderByColumn($query, 'market_product_bundles.left', 'asc'),
            'leftDesc' => $orderByColumn($query, 'market_product_bundles.left', 'desc'),
            'left' => $filterBoolean($query, 'market_product_bundles.left', true),
            'noLeft' => $filterBoolean($query, 'market_product_bundles.left', false),

            'mainAsc' => $orderByColumn($query, 'market_product_bundles.main', 'asc'),
            'mainDesc' => $orderByColumn($query, 'market_product_bundles.main', 'desc'),
            'main' => $filterBoolean($query, 'market_product_bundles.main', true),
            'noMain' => $filterBoolean($query, 'market_product_bundles.main', false),

            'rightAsc' => $orderByColumn($query, 'market_product_bundles.right', 'asc'),
            'rightDesc' => $orderByColumn($query, 'market_product_bundles.right', 'desc'),
            'right' => $filterBoolean($query, 'market_product_bundles.right', true),
            'noRight' => $filterBoolean($query, 'market_product_bundles.right', false),

            /** Статус публикации. */
            'statusAsc' => $orderByColumn($query, 'market_product_bundles.status', 'asc'),
            'statusDesc' => $orderByColumn($query, 'market_product_bundles.status', 'desc'),

            'statusDraft' => $query
                ->where('market_product_bundles.status', 'draft')
                ->orderByDesc('market_product_bundles.id'),

            'statusPublished' => $query
                ->where('market_product_bundles.status', 'published')
                ->orderByDesc('market_product_bundles.id'),

            'statusArchived' => $query
                ->where('market_product_bundles.status', 'archived')
                ->orderByDesc('market_product_bundles.id'),

            /** Модерация. */
            'moderationStatusAsc' => $orderByColumn($query, 'market_product_bundles.moderation_status', 'asc'),
            'moderationStatusDesc' => $orderByColumn($query, 'market_product_bundles.moderation_status', 'desc'),

            'moderationPending' => $query
                ->where('market_product_bundles.moderation_status', 0)
                ->orderByDesc('market_product_bundles.id'),

            'moderationApproved' => $query
                ->where('market_product_bundles.moderation_status', 1)
                ->orderByDesc('market_product_bundles.id'),

            'moderationRejected' => $query
                ->where('market_product_bundles.moderation_status', 2)
                ->orderByDesc('market_product_bundles.id'),

            /** Даты. */
            'publishedAtAsc' => $orderByColumn($query, 'market_product_bundles.published_at', 'asc'),
            'publishedAtDesc' => $orderByColumn($query, 'market_product_bundles.published_at', 'desc'),
            'showFromAtAsc' => $orderByColumn($query, 'market_product_bundles.show_from_at', 'asc'),
            'showFromAtDesc' => $orderByColumn($query, 'market_product_bundles.show_from_at', 'desc'),
            'showToAtAsc' => $orderByColumn($query, 'market_product_bundles.show_to_at', 'asc'),
            'showToAtDesc' => $orderByColumn($query, 'market_product_bundles.show_to_at', 'desc'),
            'createdAtAsc', 'dateAsc' => $orderByColumn($query, 'market_product_bundles.created_at', 'asc'),
            'createdAtDesc', 'dateDesc' => $orderByColumn($query, 'market_product_bundles.created_at', 'desc'),
            'updatedAtAsc' => $orderByColumn($query, 'market_product_bundles.updated_at', 'asc'),
            'updatedAtDesc' => $orderByColumn($query, 'market_product_bundles.updated_at', 'desc'),

            /** По умолчанию. */
            default => $query->ordered(),
        };
    }
}
