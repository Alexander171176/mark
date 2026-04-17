<?php

namespace App\Models\Admin\School\QuizQuestion;

use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\QuizAnswer\QuizAnswer;
use App\Models\Admin\School\QuizAttemptItem\QuizAttemptItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Вопрос внутри квиза: тип, текст, баллы и метаданные.
 */
class QuizQuestion extends Model
{
    use HasFactory;

    protected $table = 'quiz_questions';

    protected $fillable = [
        'quiz_id',        // FK -> quizzes.id
        'sort',           // порядок в квизе
        'question_type',  // single_choice|multiple_choice|true_false|open_text
        'question_text',  // текст вопроса
        'explanation',    // объяснение
        'points',         // баллы за вопрос
        'meta',           // JSON (медиа, подсказки, и т.п.)
        'activity',      // включён/выключен
    ];

    protected $casts = [
        'sort'       => 'int',
        'points'     => 'int',
        'activity'   => 'bool',
        'meta'       => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ========= Отношения ========= */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    // Если варианты ответов вынесены в отдельную таблицу quiz_answers
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'quiz_question_id');
    }

    // Элементы попыток, относящиеся к этому вопросу
    public function attemptItems(): HasMany
    {
        return $this->hasMany(QuizAttemptItem::class, 'quiz_question_id');
    }

    // скоупы
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort');
    }
}
