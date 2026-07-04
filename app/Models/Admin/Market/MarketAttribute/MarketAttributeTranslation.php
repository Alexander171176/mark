<?php

namespace App\Models\Admin\Market\MarketAttribute;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketAttributeTranslation extends Model
{
    use HasFactory;

    protected $table = 'market_attribute_translations';

    protected $fillable = [
        'market_attribute_id',
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

    /** Характеристика перевода */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(
            MarketAttribute::class,
            'market_attribute_id'
        );
    }
}
