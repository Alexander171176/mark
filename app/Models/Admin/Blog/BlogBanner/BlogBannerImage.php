<?php

namespace App\Models\Admin\Blog\BlogBanner;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogBannerImage extends BaseImage
{
    use HasFactory;

    protected $table = 'blog_banner_images';

    /**
     * Баннеры, где используется это изображение
     */
    public function banners(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogBanner::class,
            'blog_banner_has_images',
            'image_id',
            'banner_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
