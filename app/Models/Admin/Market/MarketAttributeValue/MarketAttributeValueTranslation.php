<?php

namespace App\Models\Admin\Market\MarketAttributeValue;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketAttributeValueTranslation extends Model
{
    use HasFactory;

    protected $table = 'market_attribute_value_translations';

    protected $fillable = [
        'market_attribute_value_id',
        'locale',
        'title',
        'subtitle',
        'short',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** Значение характеристики перевода */
    public function value(): BelongsTo
    {
        return $this->belongsTo(
            MarketAttributeValue::class,
            'market_attribute_value_id'
        );
    }
}
