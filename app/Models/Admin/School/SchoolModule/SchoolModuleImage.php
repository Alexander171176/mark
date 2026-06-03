<?php

namespace App\Models\Admin\School\SchoolModule;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolModuleImage extends BaseImage
{
    use HasFactory;

    protected $table = 'school_module_images';

    /** Модули, в которых используется изображение */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolModule::class,
            'school_module_has_images',
            'image_id',
            'school_module_id'
        )
            ->withPivot('order')
            ->orderBy('school_module_has_images.order', 'asc');
    }
}
