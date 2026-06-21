<?php

namespace App\Models\Admin\Market\MarketShop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketShopTranslation extends Model
{
    use HasFactory;

    protected $table = 'market_shop_translations';

    protected $fillable = [
        'market_shop_id',
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

    /** Магазин перевода */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(MarketShop::class, 'market_shop_id');
    }
}
