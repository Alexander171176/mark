<?php

namespace App\Models\Admin\School\SchoolAssignment;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolAssignmentImage extends BaseImage
{
    use HasFactory;

    protected $table = 'school_assignment_images';

    /** Задания, в которых используется изображение */
    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolAssignment::class,
            'school_assignment_has_images',
            'image_id',
            'school_assignment_id'
        )
            ->withPivot('order')
            ->orderBy('school_assignment_has_images.order', 'asc');
    }
}
