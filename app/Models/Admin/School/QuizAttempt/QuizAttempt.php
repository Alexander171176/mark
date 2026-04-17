<?php

namespace App\Models\Admin\School\QuizAttempt;

use App\Models\Admin\School\Course\Course;
use App\Models\Admin\School\Enrollment\Enrollment;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\QuizAttemptItem\QuizAttemptItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Попытка прохождения конкретного квиза конкретным пользователем.
 * Хранит баллы, проценты, статус и тайминги.
 */
class QuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'quiz_attempts';

    protected $fillable = [
        'user_id',           // FK -> users.id (кто проходит)
        'quiz_id',           // FK -> quizzes.id (какой квиз)
        'enrollment_id',     // FK -> enrollments.id (контекст зачисления)
        'course_id',         // FK -> courses.id
        'module_id',         // FK -> modules.id
        'lesson_id',         // FK -> lessons.id
        'attempt_number',    // порядковый номер попытки для (user, quiz)
        'score',             // набранные баллы
        'max_score',         // максимальные баллы
        'percent',           // процент 0..100
        'status',            // in_progress|completed|graded
        'started_at',        // когда начал
        'finished_at',       // когда закончил
        'duration_seconds',  // затраченное время в секундах
        'ip_address',        // IP
        'user_agent',        // User-Agent
    ];

    protected $casts = [
        'attempt_number'   => 'int',
        'score'            => 'int',
        'max_score'        => 'int',
        'percent'          => 'int',
        'duration_seconds' => 'int',
        'started_at'       => 'datetime',
        'finished_at'      => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    protected $attributes = [
        'attempt_number'    => 1,
        'score'             => 0,
        'max_score'         => 0,
        'percent'           => 0,
        'status'            => 'in_progress',
        'duration_seconds'  => 0,
    ];

    /* ================= Отношения ================= */

    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function quiz(): BelongsTo     { return $this->belongsTo(Quiz::class); }
    public function enrollment(): BelongsTo { return $this->belongsTo(Enrollment::class); }
    public function course(): BelongsTo   { return $this->belongsTo(Course::class); }
    public function module(): BelongsTo   { return $this->belongsTo(Module::class); }
    public function lesson(): BelongsTo   { return $this->belongsTo(Lesson::class); }

    // Детализация ответов в рамках попытки
    public function items(): HasMany
    {
        return $this->hasMany(QuizAttemptItem::class, 'quiz_attempt_id');
    }

    /* ================= Хелперы ================= */

    public function recalcPercent(): void
    {
        $this->percent = $this->max_score > 0 ? (int) floor(($this->score / $this->max_score) * 100) : 0;
    }

    public function markFinished(): void
    {
        $this->finished_at = now();
        if ($this->started_at) {
            $this->duration_seconds = $this->finished_at->diffInSeconds($this->started_at);
        }
        $this->status = 'completed';
    }

    /* ================= Скоупы ================= */

    public function scopeByUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopeByQuiz($q, int $quizId)
    {
        return $q->where('quiz_id', $quizId);
    }

    public function scopeStatus($q, string $status)
    {
        return $q->where('status', $status);
    }

    public function scopeInProgress($q)
    {
        return $q->where('status', 'in_progress');
    }

    public function scopeCompleted($q)
    {
        return $q->where('status', 'completed');
    }

    public function scopeGraded($q)
    {
        return $q->where('status', 'graded');
    }

    public function scopeForAdminList($q)
    {
        return $q->with([
            'user:id,name,email',
            'quiz:id,title,slug',
            'course:id,title',
            'module:id,title',
            'lesson:id,title',
        ])->orderByDesc('id');
    }

}
