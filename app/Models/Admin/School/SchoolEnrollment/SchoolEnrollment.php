<?php

namespace App\Models\Admin\School\SchoolEnrollment;

use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use App\Models\Admin\School\SchoolProgressRecord\SchoolProgressRecord;
use App\Models\Admin\School\SchoolCertificate\SchoolCertificate;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolEnrollment extends Model
{
    use HasFactory;

    protected $table = 'school_enrollments';

    protected $fillable = [
        'user_id',
        'school_course_id',
        'school_course_schedule_id',
        'school_order_id',
        'status',
        'started_at',
        'expires_at',
        'completed_at',
        'progress_percent',
        'notes',
        'meta',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'school_course_id' => 'integer',
        'school_course_schedule_id' => 'integer',
        'school_order_id' => 'integer',
        'progress_percent' => 'integer',
        'meta' => 'array',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Пользователь */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Курс */
    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    /** Поток курса */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(SchoolCourseSchedule::class, 'school_course_schedule_id');
    }

    /** Заказ */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SchoolOrder::class, 'school_order_id');
    }

    /** Записи прогресса */
    public function progressRecords(): HasMany
    {
        return $this->hasMany(SchoolProgressRecord::class, 'school_enrollment_id');
    }

    /** Сертификат */
    public function certificate(): HasOne
    {
        return $this->hasOne(SchoolCertificate::class, 'school_enrollment_id');
    }

    /* ======================== Scopes ======================== */

    /** Активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q
            ->where('status', 'active')
            ->where(function (Builder $query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /** Завершённые */
    public function scopeCompleted(Builder $q): Builder
    {
        return $q->where('status', 'completed');
    }

    /** Отменённые */
    public function scopeCancelled(Builder $q): Builder
    {
        return $q->where('status', 'cancelled');
    }

    /** Приостановленные */
    public function scopePaused(Builder $q): Builder
    {
        return $q->where('status', 'paused');
    }

    /** Истёкшие */
    public function scopeExpired(Builder $q): Builder
    {
        return $q->where('status', 'expired');
    }

    /** По пользователю и курсу */
    public function scopeForUserCourse(Builder $q, int $userId, int $courseId): Builder
    {
        return $q
            ->where('user_id', $userId)
            ->where('school_course_id', $courseId);
    }

    /** По потоку */
    public function scopeForSchedule(Builder $q, int $scheduleId): Builder
    {
        return $q->where('school_course_schedule_id', $scheduleId);
    }

    /* ======================== Accessors ======================== */

    /** Есть ли доступ */
    public function getIsAccessibleAttribute(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /** Осталось дней */
    public function getDaysLeftAttribute(): ?int
    {
        return $this->expires_at
            ? now()->diffInDays($this->expires_at, false)
            : null;
    }
}
