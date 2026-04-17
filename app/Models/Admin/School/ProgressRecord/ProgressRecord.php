<?php

namespace App\Models\Admin\School\ProgressRecord;

use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Enrollment\Enrollment;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressRecord extends Model
{
    use HasFactory;

    protected $table = 'progress_records';

    // Массовое заполнение
    protected $fillable = [
        'user_id',             // Пользователь
        'enrollment_id',       // Запись зачисления (если есть)
        'course_id',           // Курс (для агрегации прогресса)
        'module_id',           // Модуль (для агрегации прогресса)
        'lesson_id',           // Урок (основной сценарий)
        'status',              // in_progress|completed|skipped|locked
        'progress_percent',    // 0..100
        'time_spent_seconds',  // время, потраченное на просмотр/выполнение
        'last_viewed_at',      // последняя активность
        'completed_at',        // момент завершения
    ];

    protected $casts = [
        'progress_percent'   => 'int',
        'time_spent_seconds' => 'int',
        'last_viewed_at'     => 'datetime',
        'completed_at'       => 'datetime',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Пользователь */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Зачисление на курс (опционально, если ведёте Cohort/Enrollment) */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    /** Курс (агрегирующая привязка) */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** Модуль (агрегирующая привязка) */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /** Урок (основной сценарий трекинга) */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /* ======================== Scopes ======================== */

    public function scopeForUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopeForCourse($q, int $courseId)
    {
        return $q->where('course_id', $courseId);
    }

    public function scopeForLesson($q, int $lessonId)
    {
        return $q->where('lesson_id', $lessonId);
    }

    public function scopeCompleted($q)
    {
        return $q->where('status', 'completed');
    }

    /* ======================== Helpers ======================== */

    /** Удобное вычисляемое поле: доля прогресса 0..1 */
    public function getProgressRatioAttribute(): float
    {
        return max(0, min(100, (int)$this->progress_percent)) / 100;
    }

    /** Быстрый апдейт прогресса с автозавершением при 100% */
    public function touchProgress(int $addSeconds, ?int $percent = null): void
    {
        $this->time_spent_seconds += max(0, $addSeconds);
        if ($percent !== null) {
            $this->progress_percent = max(0, min(100, $percent));
            if ($this->progress_percent === 100 && $this->status !== 'completed') {
                $this->status = 'completed';
                $this->completed_at = now();
            } elseif ($this->status === 'locked') {
                // Не трогаем статус, если контент заблокирован
            } elseif ($this->progress_percent > 0 && $this->status !== 'completed') {
                $this->status = 'in_progress';
            }
        }
        $this->last_viewed_at = now();
        $this->save();
    }
}
