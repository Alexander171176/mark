<?php

namespace App\Models\Admin\School\QuizAttempt;

use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\Enrollment\SchoolEnrollment;
use App\Models\Admin\School\Lesson\SchoolLesson;
use App\Models\Admin\School\Module\SchoolModule;
use App\Models\Admin\School\Quiz\SchoolQuiz;
use App\Models\Admin\School\QuizAttemptItem\SchoolQuizAttemptItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolQuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'school_quiz_attempts';

    protected $fillable = [
        'user_id',
        'school_quiz_id',
        'school_enrollment_id',
        'school_course_id',
        'school_module_id',
        'school_lesson_id',
        'attempt_number',
        'score',
        'max_score',
        'percent',
        'status',
        'started_at',
        'finished_at',
        'duration_seconds',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'school_quiz_id' => 'integer',
        'school_enrollment_id' => 'integer',
        'school_course_id' => 'integer',
        'school_module_id' => 'integer',
        'school_lesson_id' => 'integer',
        'attempt_number' => 'integer',
        'score' => 'integer',
        'max_score' => 'integer',
        'percent' => 'integer',
        'duration_seconds' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    protected $attributes = [
        'attempt_number' => 1,
        'score' => 0,
        'max_score' => 0,
        'percent' => 0,
        'status' => 'in_progress',
        'duration_seconds' => 0,
    ];

    /* ======================== Relations ======================== */

    /** Пользователь */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Квиз */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(SchoolQuiz::class, 'school_quiz_id');
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

    /** Ответы в рамках попытки */
    public function items(): HasMany
    {
        return $this->hasMany(SchoolQuizAttemptItem::class, 'school_quiz_attempt_id');
    }

    /* ======================== Helpers ======================== */

    /** Пересчёт процента */
    public function recalcPercent(): void
    {
        $this->percent = $this->max_score > 0
            ? (int) floor(($this->score / $this->max_score) * 100)
            : 0;
    }

    /** Завершение попытки */
    public function markFinished(): void
    {
        $this->finished_at = now();

        if ($this->started_at) {
            $this->duration_seconds = $this->finished_at->diffInSeconds($this->started_at);
        }

        $this->status = 'completed';
    }

    /* ======================== Scopes ======================== */

    /** По пользователю */
    public function scopeByUser(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    /** По квизу */
    public function scopeByQuiz(Builder $q, int $quizId): Builder
    {
        return $q->where('school_quiz_id', $quizId);
    }

    /** По статусу */
    public function scopeStatus(Builder $q, string $status): Builder
    {
        return $q->where('status', $status);
    }

    /** В процессе */
    public function scopeInProgress(Builder $q): Builder
    {
        return $q->where('status', 'in_progress');
    }

    /** Завершённые */
    public function scopeCompleted(Builder $q): Builder
    {
        return $q->where('status', 'completed');
    }

    /** Проверенные */
    public function scopeGraded(Builder $q): Builder
    {
        return $q->where('status', 'graded');
    }

    /** Список для админки */
    public function scopeForAdminList(Builder $q): Builder
    {
        return $q
            ->with([
                'user:id,name,email',
                'quiz.translation',
                'course.translation',
                'module.translation',
                'lesson.translation',
            ])
            ->orderByDesc('id');
    }
}
