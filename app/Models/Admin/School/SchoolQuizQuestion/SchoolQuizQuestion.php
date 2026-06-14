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

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $q->orderBy('id', 'asc'),
            'idDesc' => $q->orderBy('id', 'desc'),

            'sortAsc' => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sortDesc' => $q->orderBy('sort', 'desc')->orderByDesc('id'),

            'questionTextAsc' => $q
                ->leftJoin('school_quiz_question_translations as sqqt_sort', function ($join) use ($locale) {
                    $join->on('sqqt_sort.school_quiz_question_id', '=', 'school_quiz_questions.id')
                        ->where('sqqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqqt_sort.question_text', 'asc')
                ->orderByDesc('school_quiz_questions.id')
                ->select('school_quiz_questions.*'),

            'questionTextDesc' => $q
                ->leftJoin('school_quiz_question_translations as sqqt_sort', function ($join) use ($locale) {
                    $join->on('sqqt_sort.school_quiz_question_id', '=', 'school_quiz_questions.id')
                        ->where('sqqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqqt_sort.question_text', 'desc')
                ->orderByDesc('school_quiz_questions.id')
                ->select('school_quiz_questions.*'),

            'quizTitleAsc' => $q
                ->leftJoin('school_quizzes as sq_sort', 'sq_sort.id', '=', 'school_quiz_questions.school_quiz_id')
                ->leftJoin('school_quiz_translations as sqt_sort', function ($join) use ($locale) {
                    $join->on('sqt_sort.school_quiz_id', '=', 'sq_sort.id')
                        ->where('sqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqt_sort.title', 'asc')
                ->orderByDesc('school_quiz_questions.id')
                ->select('school_quiz_questions.*'),

            'quizTitleDesc' => $q
                ->leftJoin('school_quizzes as sq_sort', 'sq_sort.id', '=', 'school_quiz_questions.school_quiz_id')
                ->leftJoin('school_quiz_translations as sqt_sort', function ($join) use ($locale) {
                    $join->on('sqt_sort.school_quiz_id', '=', 'sq_sort.id')
                        ->where('sqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqt_sort.title', 'desc')
                ->orderByDesc('school_quiz_questions.id')
                ->select('school_quiz_questions.*'),

            'pointsAsc' => $q->orderBy('points', 'asc')->orderByDesc('id'),
            'pointsDesc' => $q->orderBy('points', 'desc')->orderByDesc('id'),

            'answersCountAsc' => $q->withCount('answers')->orderBy('answers_count', 'asc')->orderByDesc('id'),
            'answersCountDesc' => $q->withCount('answers')->orderBy('answers_count', 'desc')->orderByDesc('id'),

            'attemptItemsCountAsc' => $q->withCount('attemptItems')->orderBy('attempt_items_count', 'asc')->orderByDesc('id'),
            'attemptItemsCountDesc' => $q->withCount('attemptItems')->orderBy('attempt_items_count', 'desc')->orderByDesc('id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $q->where('activity', true)->orderByDesc('id'),
            'inactive' => $q->where('activity', false)->orderByDesc('id'),

            'singleChoice' => $q->where('question_type', 'single_choice')->orderByDesc('id'),
            'multipleChoice' => $q->where('question_type', 'multiple_choice')->orderByDesc('id'),
            'trueFalse' => $q->where('question_type', 'true_false')->orderByDesc('id'),
            'openText' => $q->where('question_type', 'open_text')->orderByDesc('id'),

            'createdAtAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('id'),

            default => $q->ordered(),
        };
    }
}
