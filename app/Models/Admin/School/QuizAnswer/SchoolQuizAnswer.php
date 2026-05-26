<?php

namespace App\Models\Admin\School\QuizAnswer;

use App\Models\Admin\School\Quiz\SchoolQuiz;
use App\Models\Admin\School\QuizAttemptItem\SchoolQuizAttemptItem;
use App\Models\Admin\School\QuizQuestion\SchoolQuizQuestion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolQuizAnswer extends Model
{
    use HasFactory;

    protected $table = 'school_quiz_answers';

    protected $fillable = [
        'school_quiz_id',
        'school_quiz_question_id',
        'is_correct',
        'weight',
        'sort',
        'activity',
    ];

    protected $casts = [
        'school_quiz_id' => 'integer',
        'school_quiz_question_id' => 'integer',
        'is_correct' => 'boolean',
        'weight' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',
    ];

    protected $attributes = [
        'is_correct' => false,
        'weight' => 0,
        'sort' => 0,
        'activity' => true,
    ];

    /* ======================== Translations ======================== */

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolQuizAnswerTranslation::class, 'school_quiz_answer_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolQuizAnswerTranslation::class, 'school_quiz_answer_id')
            ->where('locale', app()->getLocale());
    }

    /* ======================== Relations ======================== */

    /** Квиз */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(SchoolQuiz::class, 'school_quiz_id');
    }

    /** Вопрос */
    public function question(): BelongsTo
    {
        return $this->belongsTo(SchoolQuizQuestion::class, 'school_quiz_question_id');
    }

    /** Элементы попыток */
    public function attemptItems(): HasMany
    {
        return $this->hasMany(SchoolQuizAttemptItem::class, 'selected_answer_id');
    }

    /* ======================== Scopes ======================== */

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Только правильные */
    public function scopeCorrect(Builder $q): Builder
    {
        return $q->where('is_correct', true);
    }

    /** По квизу */
    public function scopeByQuiz(Builder $q, int $quizId): Builder
    {
        return $q->where('school_quiz_id', $quizId);
    }

    /** По вопросу */
    public function scopeByQuestion(Builder $q, int $questionId): Builder
    {
        return $q->where('school_quiz_question_id', $questionId);
    }

    /** Сортировка */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    /** Подгрузка перевода */
    public function scopeWithLocale(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q->with([
            'translations' => fn ($query) => $query->where('locale', $locale),
        ]);
    }

    /** Поиск */
    public function scopeSearch(Builder $q, ?string $term, ?string $locale = null): Builder
    {
        if (!$term) {
            return $q;
        }

        $locale = $locale ?: app()->getLocale();

        return $q->whereHas('translations', function (Builder $qq) use ($term, $locale) {
            $qq->where('locale', $locale)
                ->where(function (Builder $sub) use ($term) {
                    $sub->where('text', 'like', "%{$term}%")
                        ->orWhere('explanation', 'like', "%{$term}%");
                });
        });
    }

    /** Список для админки */
    public function scopeForAdminList(Builder $q): Builder
    {
        return $q
            ->with([
                'quiz.translation',
                'question.translation',
            ])
            ->orderBy('sort')
            ->orderBy('id');
    }
}
