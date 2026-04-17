<?php

namespace App\Models\Admin\School\Course;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseImage extends BaseImage
{
    use HasFactory;

    protected $table = 'course_images';

    /** Курсы, в которых используется это изображение */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_has_images',
            'image_id',    // FK на текущую модель (course_images.id)
            'course_id')   // FK на courses.id
        ->withPivot('order')
            ->orderBy('course_has_images.order', 'asc');
    }
}
