<?php

namespace App\Models\Admin\School\Assignment;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssignmentImage extends BaseImage
{
    use HasFactory;

    protected $table = 'assignment_images';

    /** Задания, в которых используется это изображение */
    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(
            Assignment::class,
            'assignment_has_images',
            'image_id',        // FK на текущую модель (assignment_images.id)
            'assignment_id'    // FK на assignments.id
        )
            ->withPivot('order')
            ->orderBy('assignment_has_images.order', 'asc');
    }
}
