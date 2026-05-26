<?php

namespace App\Models\Admin\School\ProgressRecord;

use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\Enrollment\SchoolEnrollment;
use App\Models\Admin\School\Lesson\SchoolLesson;
use App\Models\Admin\School\Module\SchoolModule;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolProgressRecord extends Model
{
    use HasFactory;

    protected $table = 'school_progress_records';

    protected $fillable = [
        'user_id',
        'school_enrollment_id',
        'school_course_id',
        'school_module_id',
        'school_lesson_id',
        'status',
        'progress_percent',
        'time_spent_seconds',
        'last_viewed_at',
        'completed_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'school_enrollment_id' => 'integer',
        'school_course_id' => 'integer',
        'school_module_id' => 'integer',
        'school_lesson_id' => 'integer',
        'progress_percent' => 'integer',
        'time_spent_seconds' => 'integer',
        'last_viewed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /** Пользователь */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Зачисление */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(SchoolEnrollment::class, 'school_enrollment_id');
    }

    /** Курс */
    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    /** Модуль */
    public function module(): BelongsTo
    {
        return $this->belongsTo(SchoolModule::class, 'school_module_id');
    }

    /** Урок */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(SchoolLesson::class, 'school_lesson_id');
    }

    /** По пользователю */
    public function scopeForUser(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    /** По курсу */
    public function scopeForCourse(Builder $q, int $courseId): Builder
    {
        return $q->where('school_course_id', $courseId);
    }

    /** По модулю */
    public function scopeForModule(Builder $q, int $moduleId): Builder
    {
        return $q->where('school_module_id', $moduleId);
    }

    /** По уроку */
    public function scopeForLesson(Builder $q, int $lessonId): Builder
    {
        return $q->where('school_lesson_id', $lessonId);
    }

    /** Завершённые */
    public function scopeCompleted(Builder $q): Builder
    {
        return $q->where('status', 'completed');
    }

    /** Доля прогресса */
    public function getProgressRatioAttribute(): float
    {
        return max(0, min(100, (int) $this->progress_percent)) / 100;
    }

    /** Обновить прогресс */
    public function touchProgress(int $addSeconds, ?int $percent = null): void
    {
        $this->time_spent_seconds += max(0, $addSeconds);

        if ($percent !== null) {
            $this->progress_percent = max(0, min(100, $percent));

            if ($this->progress_percent === 100 && $this->status !== 'completed') {
                $this->status = 'completed';
                $this->completed_at = now();
            } elseif ($this->status === 'locked') {
                // Заблокированный прогресс не переводим в другой статус
            } elseif ($this->progress_percent > 0 && $this->status !== 'completed') {
                $this->status = 'in_progress';
            }
        }

        $this->last_viewed_at = now();
        $this->save();
    }
}
