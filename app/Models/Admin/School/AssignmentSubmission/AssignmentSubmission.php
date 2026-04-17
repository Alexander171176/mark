<?php

namespace App\Models\Admin\School\AssignmentSubmission;

use App\Models\Admin\School\Assignment\Assignment;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignmentSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'assignment_submissions';

    protected $fillable = [
        'assignment_id',     // FK -> assignments.id
        'lesson_id',         // FK -> lessons.id (nullable)
        'user_id',           // FK -> users.id (кто сдал)
        'content',           // текст ответа
        'attachments',       // JSON с файлами/ссылками
        'status',            // submitted|under_review|graded|needs_changes
        'score',             // оценка (nullable)
        'review_comment',    // комментарий проверяющего
        'graded_by',         // FK -> users.id (кто проверил)
        'submitted_at',      // время отправки
        'graded_at',         // время проверки
    ];

    protected $casts = [
        'attachments'  => 'array',
        'score'        => 'decimal:2',
        'submitted_at' => 'datetime',
        'graded_at'    => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /* ================= Связи ================= */

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // Студент, который сдал работу
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Пользователь, проверивший работу
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /* ================= Скоупы ================= */

    public function scopeSubmitted($q)     { return $q->where('status', 'submitted'); }
    public function scopeUnderReview($q)   { return $q->where('status', 'under_review'); }
    public function scopeGraded($q)        { return $q->where('status', 'graded'); }
    public function scopeNeedsChanges($q)  { return $q->where('status', 'needs_changes'); }

    /* ================= Хелперы ================= */

    public function markUnderReview(): void
    {
        $this->status = 'under_review';
        $this->save();
    }

    public function grade(float $score, ?string $comment = null, ?int $graderId = null): void
    {
        $this->status = 'graded';
        $this->score = $score;
        $this->review_comment = $comment;
        if ($graderId) $this->graded_by = $graderId;
        $this->graded_at = now();
        $this->save();
    }

    public function requestChanges(?string $comment = null, ?int $graderId = null): void
    {
        $this->status = 'needs_changes';
        $this->review_comment = $comment;
        if ($graderId) $this->graded_by = $graderId;
        $this->save();
    }
}
