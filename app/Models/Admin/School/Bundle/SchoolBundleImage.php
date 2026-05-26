<?php

namespace App\Models\Admin\School\Bundle;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolBundleImage extends BaseImage
{
    use HasFactory;

    protected $table = 'school_bundle_images';

    /** Наборы, в которых используется изображение */
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolBundle::class,
            'school_bundle_has_images',
            'image_id',
            'school_bundle_id'
        )
            ->withPivot('order')
            ->orderBy('school_bundle_has_images.order', 'asc');
    }
}
