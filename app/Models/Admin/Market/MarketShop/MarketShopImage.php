<?php

namespace App\Models\Admin\Market\MarketShop;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MarketShopImage extends BaseImage
{
    protected $table = 'market_shop_images';

    /** Магазины изображения */
    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketShop::class,
            'market_shop_has_images',
            'market_shop_image_id',
            'market_shop_id'
        )->withPivot('order')
            ->orderByPivot('order');
    }
}
