<?php

namespace App\Models\Admin\School\Bundle;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BundleImage extends BaseImage
{
    use HasFactory;

    protected $table = 'bundle_images';

    /** Наборы, в которых используется это изображение */
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(
            Bundle::class,
            'bundle_has_images',
            'image_id',    // FK на текущую модель (bundle_images.id)
            'bundle_id')   // FK на bundles.id
        ->withPivot('order')
            ->orderBy('bundle_has_images.order', 'asc');
    }
}
