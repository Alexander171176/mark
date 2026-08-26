<?php

namespace App\Models\Admin\School\SchoolQuizAttemptItem;

use App\Models\Admin\School\SchoolQuizAnswer\SchoolQuizAnswer;
use App\Models\Admin\School\SchoolQuizAttempt\SchoolQuizAttempt;
use App\Models\Admin\School\SchoolQuizQuestion\SchoolQuizQuestion;
use Illuminate\Database\Eloquent\Builder;
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

    /* ======================== Scopes ======================== */

    /** Поиск */
    public function scopeSearch(Builder $q, ?string $term, ?string $locale = null): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $q;
        }

        $locale = $locale ?: app()->getLocale();

        $words = collect(preg_split('/[\s:#№,"\'«»(){}\[\].!?\/\\\\|;+=*&^%$@<>`~_-]+/u', $term))
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => mb_strlen($word) >= 2)
            ->values();

        if ($words->isEmpty()) {
            return $q;
        }

        return $q->where(function (Builder $query) use ($words, $locale) {
            foreach ($words as $word) {
                $query->where(function (Builder $query) use ($word, $locale) {
                    $query
                        ->where('school_quiz_attempt_items.free_text_answer', 'like', "%{$word}%")
                        ->orWhere('school_quiz_attempt_items.reviewer_comment', 'like', "%{$word}%")
                        ->orWhere('school_quiz_attempt_items.score', 'like', "%{$word}%")
                        ->orWhere('school_quiz_attempt_items.max_score', 'like', "%{$word}%")

                        ->orWhereHas('question.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('question_text', 'like', "%{$word}%")
                                        ->orWhere('explanation', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('selectedAnswer.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('text', 'like', "%{$word}%")
                                        ->orWhere('explanation', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('attempt.user', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%")
                                ->orWhere('email', 'like', "%{$word}%");
                        })

                        ->orWhereHas('attempt.quiz.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('description', 'like', "%{$word}%");
                                });
                        });
                });
            }
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $q->orderBy('school_quiz_attempt_items.id', 'asc'),
            'idDesc' => $q->orderBy('school_quiz_attempt_items.id', 'desc'),

            'attemptIdAsc' => $q->orderBy('school_quiz_attempt_id', 'asc')->orderByDesc('school_quiz_attempt_items.id'),
            'attemptIdDesc' => $q->orderBy('school_quiz_attempt_id', 'desc')->orderByDesc('school_quiz_attempt_items.id'),

            'questionIdAsc' => $q->orderBy('school_quiz_question_id', 'asc')->orderByDesc('school_quiz_attempt_items.id'),
            'questionIdDesc' => $q->orderBy('school_quiz_question_id', 'desc')->orderByDesc('school_quiz_attempt_items.id'),

            'scoreAsc' => $q->orderBy('score', 'asc')->orderByDesc('school_quiz_attempt_items.id'),
            'scoreDesc' => $q->orderBy('score', 'desc')->orderByDesc('school_quiz_attempt_items.id'),

            'maxScoreAsc' => $q->orderBy('max_score', 'asc')->orderByDesc('school_quiz_attempt_items.id'),
            'maxScoreDesc' => $q->orderBy('max_score', 'desc')->orderByDesc('school_quiz_attempt_items.id'),

            'correctFirst' => $q->orderBy('is_correct', 'desc')->orderByDesc('school_quiz_attempt_items.id'),
            'wrongFirst' => $q->orderBy('is_correct', 'asc')->orderByDesc('school_quiz_attempt_items.id'),

            'questionTextAsc' => $q
                ->leftJoin('school_quiz_question_translations as sqqt_sort', function ($join) use ($locale) {
                    $join->on('sqqt_sort.school_quiz_question_id', '=', 'school_quiz_attempt_items.school_quiz_question_id')
                        ->where('sqqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqqt_sort.question_text', 'asc')
                ->orderByDesc('school_quiz_attempt_items.id')
                ->addSelect('school_quiz_attempt_items.*'),

            'questionTextDesc' => $q
                ->leftJoin('school_quiz_question_translations as sqqt_sort', function ($join) use ($locale) {
                    $join->on('sqqt_sort.school_quiz_question_id', '=', 'school_quiz_attempt_items.school_quiz_question_id')
                        ->where('sqqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqqt_sort.question_text', 'desc')
                ->orderByDesc('school_quiz_attempt_items.id')
                ->addSelect('school_quiz_attempt_items.*'),

            'createdAtAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('school_quiz_attempt_items.id'),
            'createdAtDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('school_quiz_attempt_items.id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('school_quiz_attempt_items.id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('school_quiz_attempt_items.id'),

            default => $q->orderByDesc('school_quiz_attempt_items.id'),
        };
    }
}
