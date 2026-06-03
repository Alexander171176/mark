<?php

namespace App\Models\Admin\School\SchoolLesson;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolLessonImage extends BaseImage
{
    use HasFactory;

    protected $table = 'school_lesson_images';

    /** Уроки, в которых используется изображение */
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolLesson::class,
            'school_lesson_has_images',
            'image_id',
            'school_lesson_id'
        )
            ->withPivot('order')
            ->orderBy('school_lesson_has_images.order', 'asc');
    }
}
