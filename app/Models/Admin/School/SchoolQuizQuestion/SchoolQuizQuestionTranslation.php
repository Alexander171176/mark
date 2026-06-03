<?php

namespace App\Models\Admin\School\SchoolQuizQuestion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolQuizQuestionTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_quiz_question_translations';

    protected $fillable = [
        'school_quiz_question_id',
        'locale',
        'question_text',
        'explanation',
    ];

    /** Вопрос квиза */
    public function question(): BelongsTo
    {
        return $this->belongsTo(SchoolQuizQuestion::class, 'school_quiz_question_id');
    }
}
