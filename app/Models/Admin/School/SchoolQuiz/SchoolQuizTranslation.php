<?php

namespace App\Models\Admin\School\SchoolQuiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolQuizTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_quiz_translations';

    protected $fillable = [
        'school_quiz_id',
        'locale',
        'title',
        'short',
        'description',
    ];

    /** Квиз */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(SchoolQuiz::class, 'school_quiz_id');
    }
}
