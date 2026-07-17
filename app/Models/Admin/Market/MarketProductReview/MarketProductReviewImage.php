<?php

namespace App\Models\Admin\Market\MarketProductReview;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MarketProductReviewImage extends BaseImage
{
    protected $table = 'market_product_review_images';

    /** Отзывы изображения */
    public function reviews(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProductReview::class,
            'market_product_review_has_images',
            'market_product_review_image_id',
            'market_product_review_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
