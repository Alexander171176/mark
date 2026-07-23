<?php

namespace App\Models\Admin\Market\MarketProductVariant;

use App\Models\Admin\Market\MarketAttribute\MarketAttribute;
use App\Models\Admin\Market\MarketAttributeValue\MarketAttributeValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketProductVariantValue extends Model
{
    use HasFactory;

    protected $table = 'market_product_variant_values';

    protected $fillable = [
        'market_product_variant_id',
        'market_attribute_id',
        'market_attribute_value_id',
        'sort',
    ];

    protected $casts = [
        'market_product_variant_id' => 'integer',
        'market_attribute_id' => 'integer',
        'market_attribute_value_id' => 'integer',
        'sort' => 'integer',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /**
     * Вариант товара.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            MarketProductVariant::class,
            'market_product_variant_id'
        );
    }

    /**
     * Характеристика, участвующая
     * в формировании варианта.
     *
     * Например:
     * - цвет;
     * - размер;
     * - объём памяти.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(
            MarketAttribute::class,
            'market_attribute_id'
        );
    }

    /**
     * Выбранное справочное значение характеристики.
     *
     * Например:
     * - чёрный;
     * - XL;
     * - 256 ГБ.
     */
    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(
            MarketAttributeValue::class,
            'market_attribute_value_id'
        );
    }

    /* ======================== Helpers ======================== */

    /**
     * Получить переведённое название характеристики.
     */
    public function getAttributeTitle(
        ?string $locale = null,
        ?string $fallback = null
    ): ?string {
        if (! $this->relationLoaded('attribute')) {
            $this->load('attribute.translations');
        }

        return $this->attribute?->getTranslatedTitle(
            locale: $locale,
            fallback: $fallback
        );
    }

    /**
     * Получить переведённое название
     * выбранного значения характеристики.
     */
    public function getValueTitle(
        ?string $locale = null,
        ?string $fallback = null
    ): ?string {
        if (! $this->relationLoaded('attributeValue')) {
            $this->load('attributeValue.translations');
        }

        return $this->attributeValue?->getTranslatedTitle(
            locale: $locale,
            fallback: $fallback
        );
    }

    /**
     * Получить отображаемую пару:
     *
     * Цвет: Чёрный
     * Размер: XL
     */
    public function getDisplayValue(
        ?string $locale = null,
        ?string $fallback = null
    ): string {
        $attributeTitle = $this->getAttributeTitle(
            locale: $locale,
            fallback: $fallback
        );

        $valueTitle = $this->getValueTitle(
            locale: $locale,
            fallback: $fallback
        );

        if (filled($attributeTitle) && filled($valueTitle)) {
            return "{$attributeTitle}: {$valueTitle}";
        }

        return $valueTitle
            ?: $attributeTitle
                ?: "ID: {$this->id}";
    }

    /* ======================== Scopes ======================== */

    /**
     * Сортировка по порядку отображения.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('market_product_variant_values.sort')
            ->orderBy('market_product_variant_values.id');
    }

    /**
     * Значения конкретного варианта товара.
     */
    public function scopeForVariant(
        Builder $query,
        int $variantId
    ): Builder {
        return $query->where(
            'market_product_variant_values.market_product_variant_id',
            $variantId
        );
    }

    /**
     * Значения конкретной характеристики.
     */
    public function scopeForAttribute(
        Builder $query,
        int $attributeId
    ): Builder {
        return $query->where(
            'market_product_variant_values.market_attribute_id',
            $attributeId
        );
    }

    /**
     * Фильтрация по выбранному справочному значению.
     */
    public function scopeForAttributeValue(
        Builder $query,
        int $attributeValueId
    ): Builder {
        return $query->where(
            'market_product_variant_values.market_attribute_value_id',
            $attributeValueId
        );
    }
}
