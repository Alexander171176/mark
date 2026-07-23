<?php

namespace App\Models\Admin\Market\MarketProductVariant;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MarketProductVariantImage extends BaseImage
{
    protected $table = 'market_product_variant_images';

    /**
     * Варианты товаров, использующие данное изображение.
     */
    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProductVariant::class,
            'market_product_variant_has_images',
            'market_product_variant_image_id',
            'market_product_variant_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
