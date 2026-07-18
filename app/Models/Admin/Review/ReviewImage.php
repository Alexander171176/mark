<?php

namespace App\Models\Admin\Review;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ReviewImage extends BaseImage
{
    protected $table = 'review_images';

    /** Отзывы, в которых используется изображение */
    public function reviews(): BelongsToMany
    {
        return $this->belongsToMany(
            Review::class,
            'review_has_images',
            'review_image_id',
            'review_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
