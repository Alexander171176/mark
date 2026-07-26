<?php

namespace App\Models\Admin\Market\MarketProductBundle;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\Admin\Market\MarketProductVariant\MarketProductVariant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketProductBundleItem extends Model
{
    use HasFactory;

    protected $table = 'market_product_bundle_items';

    protected $fillable = [
        'market_product_bundle_id',
        'market_product_id',
        'market_product_variant_id',

        'quantity',

        'unit_price',
        'discount_type',
        'discount_value',

        'sort',
        'activity',
    ];

    protected $casts = [
        'market_product_bundle_id' => 'integer',
        'market_product_id' => 'integer',
        'market_product_variant_id' => 'integer',

        'quantity' => 'integer',

        'unit_price' => 'decimal:2',
        'discount_value' => 'decimal:2',

        'sort' => 'integer',
        'activity' => 'boolean',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Комплект товаров */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(
            MarketProductBundle::class,
            'market_product_bundle_id'
        );
    }

    /** Товар позиции комплекта */
    public function product(): BelongsTo
    {
        return $this->belongsTo(
            MarketProduct::class,
            'market_product_id'
        );
    }

    /** Конкретный вариант товара */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            MarketProductVariant::class,
            'market_product_variant_id'
        );
    }

    /* ======================== State helpers ======================== */

    /** Позиция активна */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Для позиции выбран конкретный вариант */
    public function hasVariant(): bool
    {
        return ! is_null($this->market_product_variant_id);
    }

    /** Для позиции задана собственная цена */
    public function hasCustomUnitPrice(): bool
    {
        return ! is_null($this->unit_price);
    }

    /** У позиции есть скидка */
    public function hasDiscount(): bool
    {
        return in_array(
                $this->discount_type,
                [
                    'fixed',
                    'percent',
                ],
                true
            )
            && ! is_null($this->discount_value)
            && (float) $this->discount_value > 0;
    }

    /** Используется фиксированная скидка */
    public function hasFixedDiscount(): bool
    {
        return $this->discount_type === 'fixed'
            && $this->hasDiscount();
    }

    /** Используется процентная скидка */
    public function hasPercentDiscount(): bool
    {
        return $this->discount_type === 'percent'
            && $this->hasDiscount();
    }

    /**
     * Проверить, принадлежит ли выбранный вариант
     * указанному товару.
     */
    public function variantBelongsToProduct(): bool
    {
        if (! $this->hasVariant()) {
            return true;
        }

        if ($this->relationLoaded('variant')) {
            return $this->variant !== null
                && (int) $this->variant->market_product_id
                === (int) $this->market_product_id;
        }

        return MarketProductVariant::query()
            ->whereKey($this->market_product_variant_id)
            ->where(
                'market_product_id',
                $this->market_product_id
            )
            ->exists();
    }

    /* ======================== Price helpers ======================== */

    /**
     * Получить базовую цену одной единицы позиции.
     *
     * Приоритет:
     * 1. собственная unit_price;
     * 2. эффективная цена варианта;
     * 3. цена товара.
     */
    public function baseUnitPrice(): float
    {
        if ($this->hasCustomUnitPrice()) {
            return round(
                max(0, (float) $this->unit_price),
                2
            );
        }

        if ($this->hasVariant()) {
            $variant = $this->getVariantForCalculation();

            if ($variant) {
                return round(
                    max(0, (float) $variant->effectivePrice()),
                    2
                );
            }
        }

        $product = $this->getProductForCalculation();

        return round(
            max(0, (float) ($product?->price ?? 0)),
            2
        );
    }

    /**
     * Рассчитать скидку на одну единицу позиции.
     */
    public function unitDiscountAmount(): float
    {
        if (! $this->hasDiscount()) {
            return 0.0;
        }

        $basePrice = $this->baseUnitPrice();
        $discountValue = max(
            0,
            (float) $this->discount_value
        );

        if ($this->hasPercentDiscount()) {
            $discountValue = min(
                $discountValue,
                100
            );

            return round(
                $basePrice * $discountValue / 100,
                2
            );
        }

        return round(
            min($discountValue, $basePrice),
            2
        );
    }

    /**
     * Получить итоговую цену одной единицы
     * после применения скидки.
     */
    public function effectiveUnitPrice(): float
    {
        return round(
            max(
                0,
                $this->baseUnitPrice()
                - $this->unitDiscountAmount()
            ),
            2
        );
    }

    /**
     * Получить общую цену позиции без скидки.
     */
    public function subtotalPrice(): float
    {
        return round(
            $this->baseUnitPrice()
            * max(1, (int) $this->quantity),
            2
        );
    }

    /**
     * Получить общую скидку позиции.
     */
    public function totalDiscountAmount(): float
    {
        return round(
            $this->unitDiscountAmount()
            * max(1, (int) $this->quantity),
            2
        );
    }

    /**
     * Получить итоговую цену позиции комплекта.
     */
    public function totalPrice(): float
    {
        return round(
            $this->effectiveUnitPrice()
            * max(1, (int) $this->quantity),
            2
        );
    }

    /* ======================== Availability helpers ======================== */

    /**
     * Получить доступное количество товара
     * или выбранного варианта.
     */
    public function availableQuantity(): int
    {
        if ($this->hasVariant()) {
            $variant = $this->getVariantForCalculation();

            if (! $variant) {
                return 0;
            }

            return $variant->hasStock()
                ? max(0, (int) $variant->quantity)
                : 0;
        }

        $product = $this->getProductForCalculation();

        if (! $product) {
            return 0;
        }

        /**
         * Если у товара имеются варианты,
         * но конкретный вариант в позиции не выбран,
         * используем основной вариант товара.
         */
        if ($product->hasVariants()) {
            $defaultVariant = $product->getDefaultVariant();

            if (! $defaultVariant) {
                return 0;
            }

            return $defaultVariant->hasStock()
                ? max(0, (int) $defaultVariant->quantity)
                : 0;
        }

        return $product->hasStock()
            ? max(0, (int) $product->quantity)
            : 0;
    }

    /**
     * Достаточно ли остатка для одной позиции комплекта.
     */
    public function hasStock(): bool
    {
        return $this->availableQuantity()
            >= max(1, (int) $this->quantity);
    }

    /* ======================== Display helpers ======================== */

    /**
     * Получить название позиции.
     *
     * Если выбран вариант, возвращается его название.
     * Иначе возвращается название товара.
     */
    public function getDisplayTitle(
        ?string $locale = null,
        ?string $fallback = null
    ): ?string {
        if ($this->hasVariant()) {
            $variant = $this->getVariantForCalculation();

            if ($variant) {
                return $variant->getTranslatedTitle(
                    locale: $locale,
                    fallback: $fallback
                );
            }
        }

        return $this->getProductForCalculation()
            ?->getTranslatedTitle(
                locale: $locale,
                fallback: $fallback ?: 'ru'
            );
    }

    /* ======================== Internal helpers ======================== */

    /**
     * Получить товар для расчётов.
     */
    protected function getProductForCalculation(): ?MarketProduct
    {
        if ($this->relationLoaded('product')) {
            $product = $this->getRelation('product');

            return $product instanceof MarketProduct
                ? $product
                : null;
        }

        $product = $this->product()
            ->with([
                'variants',
                'defaultVariant',
            ])
            ->first();

        return $product instanceof MarketProduct
            ? $product
            : null;
    }

    /**
     * Получить вариант для расчётов.
     */
    protected function getVariantForCalculation(): ?MarketProductVariant
    {
        if (! $this->hasVariant()) {
            return null;
        }

        if ($this->relationLoaded('variant')) {
            $variant = $this->getRelation('variant');

            return $variant instanceof MarketProductVariant
                ? $variant
                : null;
        }

        $variant = $this->variant()
            ->with([
                'product',
                'currency',
            ])
            ->first();

        return $variant instanceof MarketProductVariant
            ? $variant
            : null;
    }

    /* ======================== Scopes ======================== */

    /** Только активные позиции */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundle_items.activity',
            true
        );
    }

    /** Только неактивные позиции */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where(
            'market_product_bundle_items.activity',
            false
        );
    }

    /** Сортировка позиций по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy(
                'market_product_bundle_items.sort'
            )
            ->orderBy(
                'market_product_bundle_items.id'
            );
    }

    /** Позиции конкретного комплекта */
    public function scopeForBundle(
        Builder $query,
        int $bundleId
    ): Builder {
        return $query->where(
            'market_product_bundle_items.market_product_bundle_id',
            $bundleId
        );
    }

    /** Позиции конкретного товара */
    public function scopeForProduct(
        Builder $query,
        int $productId
    ): Builder {
        return $query->where(
            'market_product_bundle_items.market_product_id',
            $productId
        );
    }

    /** Позиции конкретного варианта */
    public function scopeForVariant(
        Builder $query,
        int $variantId
    ): Builder {
        return $query->where(
            'market_product_bundle_items.market_product_variant_id',
            $variantId
        );
    }

    /** Позиции с конкретным вариантом */
    public function scopeWithVariant(Builder $query): Builder
    {
        return $query->whereNotNull(
            'market_product_bundle_items.market_product_variant_id'
        );
    }

    /** Позиции без конкретного варианта */
    public function scopeWithoutVariant(Builder $query): Builder
    {
        return $query->whereNull(
            'market_product_bundle_items.market_product_variant_id'
        );
    }

    /** Позиции с собственной ценой */
    public function scopeWithCustomPrice(Builder $query): Builder
    {
        return $query->whereNotNull(
            'market_product_bundle_items.unit_price'
        );
    }

    /** Позиции без собственной цены */
    public function scopeWithoutCustomPrice(Builder $query): Builder
    {
        return $query->whereNull(
            'market_product_bundle_items.unit_price'
        );
    }

    /** Позиции со скидкой */
    public function scopeWithDiscount(Builder $query): Builder
    {
        return $query
            ->whereIn(
                'market_product_bundle_items.discount_type',
                [
                    'fixed',
                    'percent',
                ]
            )
            ->whereNotNull(
                'market_product_bundle_items.discount_value'
            )
            ->where(
                'market_product_bundle_items.discount_value',
                '>',
                0
            );
    }
}
