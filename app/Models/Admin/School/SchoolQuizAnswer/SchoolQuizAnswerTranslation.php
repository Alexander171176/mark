<?php

namespace App\Models\Admin\School\SchoolQuizAnswer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolQuizAnswerTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_quiz_answer_translations';

    protected $fillable = [
        'school_quiz_answer_id',
        'locale',
        'text',
        'explanation',
    ];

    /** Ответ квиза */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(SchoolQuizAnswer::class, 'school_quiz_answer_id');
    }
}
