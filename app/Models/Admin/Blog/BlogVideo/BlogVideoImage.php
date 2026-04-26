<?php

namespace App\Models\Admin\Blog\BlogVideo;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogVideoImage extends BaseImage
{
    use HasFactory;

    protected $table = 'blog_video_images';

    /**
     * Видео, где используется это превью
     */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogVideo::class,
            'blog_video_has_images',
            'image_id',
            'video_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
