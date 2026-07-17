<?php

namespace App\Models\Admin\Market\MarketProductAttributeValue;

use App\Models\Admin\Market\MarketAttribute\MarketAttribute;
use App\Models\Admin\Market\MarketAttributeValue\MarketAttributeValue;
use App\Models\Admin\Market\MarketProduct\MarketProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketProductAttributeValue extends Model
{
    use HasFactory;

    protected $table = 'market_product_attribute_values';

    protected $fillable = [
        'market_product_id',
        'market_attribute_id',
        'market_attribute_value_id',

        'value_string',
        'value_number',
        'value_boolean',
        'value_date',
        'value_json',

        'unit',
        'order',
        'activity',
    ];

    protected $casts = [
        'market_product_id' => 'integer',
        'market_attribute_id' => 'integer',
        'market_attribute_value_id' => 'integer',

        'value_number' => 'decimal:4',
        'value_boolean' => 'boolean',
        'value_date' => 'date',
        'value_json' => 'array',

        'order' => 'integer',
        'activity' => 'boolean',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Товар */
    public function product(): BelongsTo
    {
        return $this->belongsTo(
            MarketProduct::class,
            'market_product_id'
        );
    }

    /** Характеристика */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(
            MarketAttribute::class,
            'market_attribute_id'
        );
    }

    /** Готовое значение характеристики из справочника */
    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(
            MarketAttributeValue::class,
            'market_attribute_value_id'
        );
    }

    /* ======================== Scopes ======================== */

    /** Только активные значения */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('activity', true);
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('order')
            ->orderBy('id');
    }

    /** Значения конкретного товара */
    public function scopeForProduct(
        Builder $query,
        int $productId
    ): Builder {
        return $query->where('market_product_id', $productId);
    }

    /** Значения конкретной характеристики */
    public function scopeForAttribute(
        Builder $query,
        int $attributeId
    ): Builder {
        return $query->where('market_attribute_id', $attributeId);
    }

    /* ======================== Helpers ======================== */

    /** Используется готовое справочное значение */
    public function hasReferenceValue(): bool
    {
        return ! is_null($this->market_attribute_value_id);
    }

    /** Получить фактическое ручное значение */
    public function getManualValue(): mixed
    {
        return match (true) {
            ! is_null($this->value_string) => $this->value_string,
            ! is_null($this->value_number) => $this->value_number,
            ! is_null($this->value_boolean) => $this->value_boolean,
            ! is_null($this->value_date) => $this->value_date,
            ! is_null($this->value_json) => $this->value_json,
            default => null,
        };
    }

    /** Получить значение для отображения */
    public function getDisplayValue(
        ?string $locale = null,
        string $fallback = 'ru'
    ): mixed {
        if ($this->attributeValue) {
            $locale = $locale ?: app()->getLocale();

            $translation = $this->attributeValue
                ->translations
                ->firstWhere('locale', $locale)
                ?: $this->attributeValue
                    ->translations
                    ->firstWhere('locale', $fallback)
                    ?: $this->attributeValue
                        ->translations
                        ->first();

            return $translation?->title
                ?? $this->attributeValue->code;
        }

        return $this->getManualValue();
    }
}
