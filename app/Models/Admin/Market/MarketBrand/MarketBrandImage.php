<?php

namespace App\Models\Admin\Market\MarketBrand;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MarketBrandImage extends BaseImage
{
    protected $table = 'market_brand_images';

    /** Бренды изображения */
    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketBrand::class,
            'market_brand_has_images',
            'market_brand_image_id',
            'market_brand_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
