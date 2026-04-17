<?php

namespace App\Models\Admin\School\CourseSchedule;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseScheduleImage extends BaseImage
{
    use HasFactory;

    protected $table = 'course_schedule_images';

    /** Потоки, в которых используется это изображение */
    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(
            CourseSchedule::class,
            'course_schedule_has_images',
            'image_id',   // FK на текущую модель (course_schedule_images.id)
            'course_schedule_id'   // FK на course_schedules.id
        )
            ->withPivot('order')
            ->orderBy('course_schedule_has_images.order', 'asc');
    }
}
