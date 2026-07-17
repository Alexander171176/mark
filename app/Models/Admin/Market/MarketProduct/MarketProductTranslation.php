<?php

namespace App\Models\Admin\Market\MarketProduct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketProductTranslation extends Model
{
    use HasFactory;

    protected $table = 'market_product_translations';

    protected $fillable = [
        'market_product_id',
        'locale',
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** Товар перевода */
    public function product(): BelongsTo
    {
        return $this->belongsTo(
            MarketProduct::class,
            'market_product_id'
        );
    }
}
