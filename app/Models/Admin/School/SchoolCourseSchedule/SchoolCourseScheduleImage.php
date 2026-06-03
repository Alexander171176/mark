<?php

namespace App\Models\Admin\School\SchoolCourseSchedule;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolCourseScheduleImage extends BaseImage
{
    use HasFactory;

    protected $table = 'school_course_schedule_images';

    /** Потоки, в которых используется изображение */
    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolCourseSchedule::class,
            'school_course_schedule_has_images',
            'image_id',
            'school_course_schedule_id'
        )
            ->withPivot('order')
            ->orderBy('school_course_schedule_has_images.order', 'asc');
    }
}
