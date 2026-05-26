<?php

namespace App\Models\Admin\School\CourseSchedule;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolCourseScheduleTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_course_schedule_translations';

    protected $fillable = [
        'school_course_schedule_id',
        'locale',
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Поток курса */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(SchoolCourseSchedule::class, 'school_course_schedule_id');
    }
}
