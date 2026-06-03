<?php

namespace App\Models\Admin\School\SchoolAssignmentSubmission;

use App\Models\Admin\School\SchoolAssignment\SchoolAssignment;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolAssignmentSubmission extends Model
{
    use HasFactory;

    protected $table = 'school_assignment_submissions';

    protected $fillable = [
        'school_assignment_id',
        'school_lesson_id',
        'user_id',
        'content',
        'attachments',
        'status',
        'score',
        'review_comment',
        'graded_by',
        'submitted_at',
        'graded_at',
    ];

    protected $casts = [
        'school_assignment_id' => 'integer',
        'school_lesson_id' => 'integer',
        'user_id' => 'integer',
        'attachments' => 'array',
        'score' => 'decimal:2',
        'graded_by' => 'integer',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    /** Задание */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(SchoolAssignment::class, 'school_assignment_id');
    }

    /** Урок */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(SchoolLesson::class, 'school_lesson_id');
    }

    /** Студент */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Проверяющий */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /** Сданные */
    public function scopeSubmitted(Builder $q): Builder
    {
        return $q->where('status', 'submitted');
    }

    /** На проверке */
    public function scopeUnderReview(Builder $q): Builder
    {
        return $q->where('status', 'under_review');
    }

    /** Проверенные */
    public function scopeGraded(Builder $q): Builder
    {
        return $q->where('status', 'graded');
    }

    /** Требуют доработки */
    public function scopeNeedsChanges(Builder $q): Builder
    {
        return $q->where('status', 'needs_changes');
    }

    /** Пометить как на проверке */
    public function markUnderReview(): void
    {
        $this->status = 'under_review';
        $this->save();
    }

    /** Выставить оценку */
    public function grade(float $score, ?string $comment = null, ?int $graderId = null): void
    {
        $this->status = 'graded';
        $this->score = $score;
        $this->review_comment = $comment;

        if ($graderId) {
            $this->graded_by = $graderId;
        }

        $this->graded_at = now();
        $this->save();
    }

    /** Запросить доработку */
    public function requestChanges(?string $comment = null, ?int $graderId = null): void
    {
        $this->status = 'needs_changes';
        $this->review_comment = $comment;

        if ($graderId) {
            $this->graded_by = $graderId;
        }

        $this->save();
    }
}
