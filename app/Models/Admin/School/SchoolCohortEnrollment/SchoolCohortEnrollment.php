<?php

namespace App\Models\Admin\School\SchoolCohortEnrollment;

use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolCohortEnrollment extends Model
{
    use HasFactory;

    protected $table = 'school_cohort_enrollments';

    protected $fillable = [
        'school_course_schedule_id',
        'user_id',
        'status',
        'enrolled_at',
        'notes',
    ];

    protected $casts = [
        'school_course_schedule_id' => 'integer',
        'user_id' => 'integer',
        'enrolled_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Поток курса */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(SchoolCourseSchedule::class, 'school_course_schedule_id');
    }

    /** Пользователь */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ======================== Scopes ======================== */

    /** Одобренные */
    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'approved');
    }

    /** Ожидающие */
    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    /** Отклонённые */
    public function scopeRejected(Builder $q): Builder
    {
        return $q->where('status', 'rejected');
    }

    /** Отменённые */
    public function scopeCancelled(Builder $q): Builder
    {
        return $q->where('status', 'cancelled');
    }
}
