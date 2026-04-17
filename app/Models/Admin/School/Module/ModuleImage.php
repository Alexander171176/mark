<?php

namespace App\Models\Admin\School\Module;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ModuleImage extends BaseImage
{
    use HasFactory;

    protected $table = 'module_images';

    /** Модули, в которых используется это изображение */
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(
            Module::class,
            'module_has_images',
            'image_id',  // FK на текущую модель (module_images.id)
            'module_id'  // FK на modules.id
        )
            ->withPivot('order')
            ->orderBy('module_has_images.order', 'asc');
    }
}

