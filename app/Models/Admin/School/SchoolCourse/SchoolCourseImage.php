<?php

namespace App\Models\Admin\School\SchoolCourse;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolCourseImage extends BaseImage
{
    use HasFactory;

    protected $table = 'school_course_images';

    /** Курсы, в которых используется изображение */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolCourse::class,
            'school_course_has_images',
            'image_id',
            'school_course_id'
        )
            ->withPivot('order')
            ->orderBy('school_course_has_images.order', 'asc');
    }
}
