<?php

namespace App\Models\Admin\Blog\Video;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VideoImage extends BaseImage
{
    use HasFactory;

    protected $table = 'video_images';

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(
            Video::class,
            'video_has_images',
            'image_id',
            'video_id'
        )
            ->withPivot('order')
            ->orderBy('video_has_images.order');
    }
}
