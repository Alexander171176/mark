<?php

namespace App\Models\Admin\School\SchoolQuizQuestion;

use App\Models\Admin\School\SchoolQuiz\SchoolQuiz;
use App\Models\Admin\School\SchoolQuizAnswer\SchoolQuizAnswer;
use App\Models\Admin\School\SchoolQuizAttemptItem\SchoolQuizAttemptItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolQuizQuestion extends Model
{
    use HasFactory;

    protected $table = 'school_quiz_questions';

    protected $fillable = [
        'school_quiz_id',
        'sort',
        'question_type',
        'points',
        'meta',
        'activity',
    ];

    protected $casts = [
        'school_quiz_id' => 'integer',
        'sort' => 'integer',
        'points' => 'integer',
        'meta' => 'array',
        'activity' => 'boolean',
    ];

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolQuizQuestionTranslation::class, 'school_quiz_question_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolQuizQuestionTranslation::class, 'school_quiz_question_id')
            ->where('locale', app()->getLocale());
    }

    /** Квиз */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(SchoolQuiz::class, 'school_quiz_id');
    }

    /** Ответы вопроса */
    public function answers(): HasMany
    {
        return $this->hasMany(SchoolQuizAnswer::class, 'school_quiz_question_id')
            ->orderBy('sort');
    }

    /** Ответы пользователей в попытках */
    public function attemptItems(): HasMany
    {
        return $this->hasMany(SchoolQuizAttemptItem::class, 'school_quiz_question_id');
    }

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Сортировка */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
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
                    $sub->where('question_text', 'like', "%{$term}%")
                        ->orWhere('explanation', 'like', "%{$term}%");
                });
        });
    }
}
