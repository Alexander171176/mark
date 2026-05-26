<?php

namespace App\Models\Admin\School\Track;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolTrackImage extends BaseImage
{
    use HasFactory;

    protected $table = 'school_track_images';

    /** Треки, к которым принадлежит изображение */
    public function tracks(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolTrack::class,
            'school_track_has_images',
            'image_id',
            'school_track_id'
        )
            ->withPivot('order')
            ->orderBy('school_track_has_images.order', 'asc');
    }
}
