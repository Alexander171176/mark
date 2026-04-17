<?php

namespace App\Models\Admin\Blog\Banner;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BannerImage extends BaseImage
{
    use HasFactory;

    protected $table = 'banner_images';

    /**
     * Баннеры, где используется это изображение (pivot.order)
     */
    public function banners(): BelongsToMany
    {
        return $this->belongsToMany(
            Banner::class,
            'banner_has_images',
            'image_id',
            'banner_id'
        )
            ->withPivot('order')
            ->orderBy('banner_has_images.order', 'asc');
    }
}
