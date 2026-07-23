<?php

namespace App\Models\Admin\Market\MarketProductVariant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketProductVariantTranslation extends Model
{
    use HasFactory;

    protected $table = 'market_product_variant_translations';

    protected $fillable = [
        'market_product_variant_id',
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
     * Вариант товара, которому принадлежит перевод.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            MarketProductVariant::class,
            'market_product_variant_id'
        );
    }
}
