<?php

namespace App\Models\Admin\Market\MarketCategory;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MarketCategoryImage extends BaseImage
{
    protected $table = 'market_category_images';

    /**
     * Категории изображения
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketCategory::class,
            'market_category_has_images',
            'market_category_image_id',
            'market_category_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
