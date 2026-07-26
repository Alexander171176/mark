<?php

namespace App\Models\Admin\Market\MarketProductBundle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketProductBundleTranslation extends Model
{
    use HasFactory;

    protected $table = 'market_product_bundle_translations';

    protected $fillable = [
        'market_product_bundle_id',
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

    /**
     * Комплект товаров, которому принадлежит перевод.
     */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(
            MarketProductBundle::class,
            'market_product_bundle_id'
        );
    }
}
