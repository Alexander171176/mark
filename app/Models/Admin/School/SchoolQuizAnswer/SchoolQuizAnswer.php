<?php

namespace App\Models\Admin\School\SchoolQuizAnswer;

use App\Models\Admin\School\SchoolQuiz\SchoolQuiz;
use App\Models\Admin\School\SchoolQuizAttemptItem\SchoolQuizAttemptItem;
use App\Models\Admin\School\SchoolQuizQuestion\SchoolQuizQuestion;
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
                        ->where('school_quiz_answers.weight', 'like', "%{$word}%")

                        ->orWhereHas('translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('text', 'like', "%{$word}%")
                                        ->orWhere('explanation', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('question.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('question_text', 'like', "%{$word}%")
                                        ->orWhere('explanation', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('quiz.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
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
            'idAsc' => $q->orderBy('id', 'asc'),
            'idDesc' => $q->orderBy('id', 'desc'),

            'sortAsc' => $q->orderBy('sort', 'asc')->orderBy('id', 'asc'),
            'sortDesc' => $q->orderBy('sort', 'desc')->orderByDesc('id'),

            'textAsc' => $q
                ->leftJoin('school_quiz_answer_translations as sqat_sort', function ($join) use ($locale) {
                    $join->on('sqat_sort.school_quiz_answer_id', '=', 'school_quiz_answers.id')
                        ->where('sqat_sort.locale', '=', $locale);
                })
                ->orderBy('sqat_sort.text', 'asc')
                ->orderBy('school_quiz_answers.id', 'asc')
                ->select('school_quiz_answers.*'),

            'textDesc' => $q
                ->leftJoin('school_quiz_answer_translations as sqat_sort', function ($join) use ($locale) {
                    $join->on('sqat_sort.school_quiz_answer_id', '=', 'school_quiz_answers.id')
                        ->where('sqat_sort.locale', '=', $locale);
                })
                ->orderBy('sqat_sort.text', 'desc')
                ->orderByDesc('school_quiz_answers.id')
                ->select('school_quiz_answers.*'),

            'quizTitleAsc' => $q
                ->leftJoin('school_quizzes as sq_sort', 'sq_sort.id', '=', 'school_quiz_answers.school_quiz_id')
                ->leftJoin('school_quiz_translations as sqt_sort', function ($join) use ($locale) {
                    $join->on('sqt_sort.school_quiz_id', '=', 'sq_sort.id')
                        ->where('sqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqt_sort.title', 'asc')
                ->orderBy('school_quiz_answers.id', 'asc')
                ->select('school_quiz_answers.*'),

            'quizTitleDesc' => $q
                ->leftJoin('school_quizzes as sq_sort', 'sq_sort.id', '=', 'school_quiz_answers.school_quiz_id')
                ->leftJoin('school_quiz_translations as sqt_sort', function ($join) use ($locale) {
                    $join->on('sqt_sort.school_quiz_id', '=', 'sq_sort.id')
                        ->where('sqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqt_sort.title', 'desc')
                ->orderByDesc('school_quiz_answers.id')
                ->select('school_quiz_answers.*'),

            'questionTextAsc' => $q
                ->leftJoin('school_quiz_questions as sqq_sort', 'sqq_sort.id', '=', 'school_quiz_answers.school_quiz_question_id')
                ->leftJoin('school_quiz_question_translations as sqqt_sort', function ($join) use ($locale) {
                    $join->on('sqqt_sort.school_quiz_question_id', '=', 'sqq_sort.id')
                        ->where('sqqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqqt_sort.question_text', 'asc')
                ->orderBy('school_quiz_answers.id', 'asc')
                ->select('school_quiz_answers.*'),

            'questionTextDesc' => $q
                ->leftJoin('school_quiz_questions as sqq_sort', 'sqq_sort.id', '=', 'school_quiz_answers.school_quiz_question_id')
                ->leftJoin('school_quiz_question_translations as sqqt_sort', function ($join) use ($locale) {
                    $join->on('sqqt_sort.school_quiz_question_id', '=', 'sqq_sort.id')
                        ->where('sqqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqqt_sort.question_text', 'desc')
                ->orderByDesc('school_quiz_answers.id')
                ->select('school_quiz_answers.*'),

            'weightAsc' => $q->orderBy('weight', 'asc')->orderBy('id', 'asc'),
            'weightDesc' => $q->orderBy('weight', 'desc')->orderByDesc('id'),

            'attemptItemsAsc' => $q->withCount('attemptItems')->orderBy('attempt_items_count', 'asc')->orderBy('id', 'asc'),
            'attemptItemsDesc' => $q->withCount('attemptItems')->orderBy('attempt_items_count', 'desc')->orderByDesc('id'),

            'correct' => $q->where('is_correct', true)->orderByDesc('id'),
            'incorrect' => $q->where('is_correct', false)->orderByDesc('id'),

            'correctAsc' => $q->orderBy('is_correct', 'asc')->orderByDesc('id'),
            'correctDesc' => $q->orderBy('is_correct', 'desc')->orderByDesc('id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $q->where('activity', true)->orderByDesc('id'),
            'inactive' => $q->where('activity', false)->orderByDesc('id'),

            'createdAtAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('id'),

            default => $q->ordered(),
        };
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
