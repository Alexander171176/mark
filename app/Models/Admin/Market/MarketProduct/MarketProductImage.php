<?php

namespace App\Models\Admin\Market\MarketProduct;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MarketProductImage extends BaseImage
{
    protected $table = 'market_product_images';

    /** Товары изображения */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProduct::class,
            'market_product_has_images',
            'market_product_image_id',
            'market_product_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
