<?php

namespace App\Models\Admin\School\QuizAnswer;

use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\QuizQuestion\QuizQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Вариант ответа для вопроса квиза.
 * Может быть правильным/неправильным, с пояснением и весом.
 */
class QuizAnswer extends Model
{
    use HasFactory;

    protected $table = 'quiz_answers';

    protected $fillable = [
        'quiz_id',           // FK -> quizzes.id (для быстрых выборок по квизу)
        'quiz_question_id',  // FK -> quiz_questions.id (родительский вопрос)
        'text',              // текст варианта ответа
        'is_correct',        // флаг правильности
        'weight',            // вес для частичного начисления баллов
        'sort',              // порядок сортировки
        'explanation',       // пояснение к ответу (отображение после попытки)
        'activity',          // опубликован/скрыт
    ];

    protected $casts = [
        'is_correct' => 'bool',
        'weight'     => 'int',
        'sort'       => 'int',
        'activity'   => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Значения по умолчанию (синхронно с миграцией).
     */
    protected $attributes = [
        'is_correct' => false,
        'weight'     => 0,
        'sort'       => 0,
        'activity'   => true,
    ];

    /* ================= Отношения ================= */

    // Родительский квиз (для быстрых фильтров)
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    // Родительский вопрос
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    /* ================= Скоупы ================= */

    /**
     * Только активные варианты ответа.
     */
    public function scopeActive($query)
    {
        return $query->where('activity', true);
    }

    /**
     * Фильтр по квизу.
     */
    public function scopeByQuiz($query, int $quizId)
    {
        return $query->where('quiz_id', $quizId);
    }

    /**
     * Фильтр по вопросу.
     */
    public function scopeByQuestion($query, int $questionId)
    {
        return $query->where('quiz_question_id', $questionId);
    }

    /**
     * Стандартная сортировка: сначала по sort, затем по id.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort')->orderBy('id');
    }

    /**
     * Только правильные ответы.
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Пресет под админский список:
     * подтягивает квиз и вопрос и сортирует по sort, id.
     */
    public function scopeForAdminList($query)
    {
        return $query
            ->with([
                'quiz:id,title,slug',
                'question:id,quiz_id,question_type,sort',
            ])
            ->orderBy('sort')
            ->orderBy('id');
    }
}
