<?php

namespace App\Models\Admin\School\CohortEnrollment;

use App\Models\Admin\School\CourseSchedule\CourseSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CohortEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cohort_enrollments';

    protected $fillable = [
        'course_schedule_id',
        'user_id',
        'status',
        'enrolled_at',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($q)  { return $q->where('status', 'approved'); }
    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeCancelled($q) { return $q->where('status', 'cancelled'); }
    // при желании:
    // public function scopeRejected($q) { return $q->where('status', 'rejected'); }
}
