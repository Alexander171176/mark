<?php

namespace App\Models\Admin\School\QuizAttemptItem;

use App\Models\Admin\School\QuizAnswer\QuizAnswer;
use App\Models\Admin\School\QuizAttempt\QuizAttempt;
use App\Models\Admin\School\QuizQuestion\QuizQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ответ на конкретный вопрос в рамках конкретной попытки.
 * Поддерживает закрытые и открытые вопросы, хранит начисленные баллы.
 */
class QuizAttemptItem extends Model
{
    use HasFactory;

    protected $table = 'quiz_attempt_items';

    protected $fillable = [
        'quiz_attempt_id',     // FK -> quiz_attempts.id
        'quiz_question_id',    // FK -> quiz_questions.id
        'selected_answer_id',  // FK -> quiz_answers.id (для single-choice)
        'selected_answer_ids', // JSON массив ответов (для multiple-choice)
        'free_text_answer',    // свободный ответ (open-ended)
        'is_correct',          // определение правильности
        'score',               // начисленные баллы
        'max_score',           // максимальные баллы по вопросу
        'reviewer_comment',    // комментарий проверяющего
    ];

    protected $casts = [
        'selected_answer_ids' => 'array',
        'is_correct'          => 'bool',
        'score'               => 'int',
        'max_score'           => 'int',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    /* ================= Отношения ================= */

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    public function selectedAnswer(): BelongsTo
    {
        return $this->belongsTo(QuizAnswer::class, 'selected_answer_id');
    }
}
