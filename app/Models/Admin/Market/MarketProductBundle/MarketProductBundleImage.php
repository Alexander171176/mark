<?php

namespace App\Models\Admin\Market\MarketProductBundle;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MarketProductBundleImage extends BaseImage
{
    protected $table = 'market_product_bundle_images';

    /**
     * Комплекты товаров, использующие изображение.
     */
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProductBundle::class,
            'market_product_bundle_has_images',
            'market_product_bundle_image_id',
            'market_product_bundle_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
