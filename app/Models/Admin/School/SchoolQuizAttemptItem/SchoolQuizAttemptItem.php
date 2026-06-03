<?php

namespace App\Models\Admin\School\SchoolQuizAttemptItem;

use App\Models\Admin\School\SchoolQuizAnswer\SchoolQuizAnswer;
use App\Models\Admin\School\SchoolQuizAttempt\SchoolQuizAttempt;
use App\Models\Admin\School\SchoolQuizQuestion\SchoolQuizQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolQuizAttemptItem extends Model
{
    use HasFactory;

    protected $table = 'school_quiz_attempt_items';

    protected $fillable = [
        'school_quiz_attempt_id',
        'school_quiz_question_id',
        'selected_answer_id',
        'selected_answer_ids',
        'free_text_answer',
        'is_correct',
        'score',
        'max_score',
        'reviewer_comment',
    ];

    protected $casts = [
        'school_quiz_attempt_id' => 'integer',
        'school_quiz_question_id' => 'integer',
        'selected_answer_id' => 'integer',
        'selected_answer_ids' => 'array',
        'is_correct' => 'boolean',
        'score' => 'integer',
        'max_score' => 'integer',
    ];

    /* ======================== Relations ======================== */

    /** Попытка */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(SchoolQuizAttempt::class, 'school_quiz_attempt_id');
    }

    /** Вопрос */
    public function question(): BelongsTo
    {
        return $this->belongsTo(SchoolQuizQuestion::class, 'school_quiz_question_id');
    }

    /** Выбранный ответ */
    public function selectedAnswer(): BelongsTo
    {
        return $this->belongsTo(SchoolQuizAnswer::class, 'selected_answer_id');
    }
}
