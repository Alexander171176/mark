<?php

namespace App\Models\Admin\School\SchoolQuizAttempt;

use App\Models\Admin\School\SchoolQuiz\SchoolQuiz;
use App\Models\Admin\School\SchoolQuizAttemptItem\SchoolQuizAttemptItem;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolEnrollment\SchoolEnrollment;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolQuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'school_quiz_attempts';

    protected $fillable = [
        'user_id',
        'school_quiz_id',
        'school_enrollment_id',
        'school_course_id',
        'school_module_id',
        'school_lesson_id',
        'attempt_number',
        'score',
        'max_score',
        'percent',
        'status',
        'started_at',
        'finished_at',
        'duration_seconds',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'school_quiz_id' => 'integer',
        'school_enrollment_id' => 'integer',
        'school_course_id' => 'integer',
        'school_module_id' => 'integer',
        'school_lesson_id' => 'integer',
        'attempt_number' => 'integer',
        'score' => 'integer',
        'max_score' => 'integer',
        'percent' => 'integer',
        'duration_seconds' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    protected $attributes = [
        'attempt_number' => 1,
        'score' => 0,
        'max_score' => 0,
        'percent' => 0,
        'status' => 'in_progress',
        'duration_seconds' => 0,
    ];

    /* ======================== Relations ======================== */

    /** Пользователь */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Квиз */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(SchoolQuiz::class, 'school_quiz_id');
    }

    /** Зачисление */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(SchoolEnrollment::class, 'school_enrollment_id');
    }

    /** Курс */
    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    /** Модуль */
    public function module(): BelongsTo
    {
        return $this->belongsTo(SchoolModule::class, 'school_module_id');
    }

    /** Урок */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(SchoolLesson::class, 'school_lesson_id');
    }

    /** Ответы в рамках попытки */
    public function items(): HasMany
    {
        return $this->hasMany(SchoolQuizAttemptItem::class, 'school_quiz_attempt_id');
    }

    /* ======================== Helpers ======================== */

    /** Пересчёт процента */
    public function recalcPercent(): void
    {
        $this->percent = $this->max_score > 0
            ? (int) floor(($this->score / $this->max_score) * 100)
            : 0;
    }

    /** Завершение попытки */
    public function markFinished(): void
    {
        $this->finished_at = now();

        if ($this->started_at) {
            $this->duration_seconds = $this->finished_at->diffInSeconds($this->started_at);
        }

        $this->status = 'completed';
    }

    /* ======================== Scopes ======================== */

    /** По пользователю */
    public function scopeByUser(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    /** По квизу */
    public function scopeByQuiz(Builder $q, int $quizId): Builder
    {
        return $q->where('school_quiz_id', $quizId);
    }

    /** По статусу */
    public function scopeStatus(Builder $q, string $status): Builder
    {
        return $q->where('status', $status);
    }

    /** В процессе */
    public function scopeInProgress(Builder $q): Builder
    {
        return $q->where('status', 'in_progress');
    }

    /** Завершённые */
    public function scopeCompleted(Builder $q): Builder
    {
        return $q->where('status', 'completed');
    }

    /** Проверенные */
    public function scopeGraded(Builder $q): Builder
    {
        return $q->where('status', 'graded');
    }

    /** Список для админки */
    public function scopeForAdminList(Builder $q): Builder
    {
        return $q
            ->with([
                'user:id,name,email',
                'quiz.translation',
                'course.translation',
                'module.translation',
                'lesson.translation',
            ])
            ->orderByDesc('id');
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
                        ->where('school_quiz_attempts.status', 'like', "%{$word}%")
                        ->orWhere('school_quiz_attempts.ip_address', 'like', "%{$word}%")
                        ->orWhere('school_quiz_attempts.user_agent', 'like', "%{$word}%")
                        ->orWhere('school_quiz_attempts.attempt_number', 'like', "%{$word}%")
                        ->orWhere('school_quiz_attempts.score', 'like', "%{$word}%")
                        ->orWhere('school_quiz_attempts.max_score', 'like', "%{$word}%")
                        ->orWhere('school_quiz_attempts.percent', 'like', "%{$word}%")

                        ->orWhereHas('user', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%")
                                ->orWhere('email', 'like', "%{$word}%");
                        })

                        ->orWhereHas('quiz.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('description', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('course.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('subtitle', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('description', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('module.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('description', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('lesson.translations', function (Builder $qq) use ($word, $locale) {
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
            'idAsc' => $q->orderBy('school_quiz_attempts.id', 'asc'),
            'idDesc' => $q->orderBy('school_quiz_attempts.id', 'desc'),

            'attemptAsc' => $q->orderBy('attempt_number', 'asc')->orderByDesc('school_quiz_attempts.id'),
            'attemptDesc' => $q->orderBy('attempt_number', 'desc')->orderByDesc('school_quiz_attempts.id'),

            'scoreAsc' => $q->orderBy('score', 'asc')->orderByDesc('school_quiz_attempts.id'),
            'scoreDesc' => $q->orderBy('score', 'desc')->orderByDesc('school_quiz_attempts.id'),

            'maxScoreAsc' => $q->orderBy('max_score', 'asc')->orderByDesc('school_quiz_attempts.id'),
            'maxScoreDesc' => $q->orderBy('max_score', 'desc')->orderByDesc('school_quiz_attempts.id'),

            'percentAsc' => $q->orderBy('percent', 'asc')->orderByDesc('school_quiz_attempts.id'),
            'percentDesc' => $q->orderBy('percent', 'desc')->orderByDesc('school_quiz_attempts.id'),

            'durationAsc' => $q->orderBy('duration_seconds', 'asc')->orderByDesc('school_quiz_attempts.id'),
            'durationDesc' => $q->orderBy('duration_seconds', 'desc')->orderByDesc('school_quiz_attempts.id'),

            'startedAtAsc' => $q->orderBy('started_at', 'asc')->orderByDesc('school_quiz_attempts.id'),
            'startedAtDesc' => $q->orderBy('started_at', 'desc')->orderByDesc('school_quiz_attempts.id'),

            'finishedAtAsc' => $q->orderBy('finished_at', 'asc')->orderByDesc('school_quiz_attempts.id'),
            'finishedAtDesc' => $q->orderBy('finished_at', 'desc')->orderByDesc('school_quiz_attempts.id'),

            'statusAsc' => $q->orderBy('status', 'asc')->orderByDesc('school_quiz_attempts.id'),
            'statusDesc' => $q->orderBy('status', 'desc')->orderByDesc('school_quiz_attempts.id'),

            'inProgress' => $q->where('status', 'in_progress')->orderByDesc('school_quiz_attempts.id'),
            'completed' => $q->where('status', 'completed')->orderByDesc('school_quiz_attempts.id'),
            'graded' => $q->where('status', 'graded')->orderByDesc('school_quiz_attempts.id'),

            'itemsAsc' => $q->withCount('items')->orderBy('items_count', 'asc')->orderByDesc('school_quiz_attempts.id'),
            'itemsDesc' => $q->withCount('items')->orderBy('items_count', 'desc')->orderByDesc('school_quiz_attempts.id'),

            'userNameAsc' => $q
                ->leftJoin('users as u_sort', 'u_sort.id', '=', 'school_quiz_attempts.user_id')
                ->orderBy('u_sort.name', 'asc')
                ->orderByDesc('school_quiz_attempts.id')
                ->select('school_quiz_attempts.*'),

            'userNameDesc' => $q
                ->leftJoin('users as u_sort', 'u_sort.id', '=', 'school_quiz_attempts.user_id')
                ->orderBy('u_sort.name', 'desc')
                ->orderByDesc('school_quiz_attempts.id')
                ->select('school_quiz_attempts.*'),

            'quizTitleAsc' => $q
                ->leftJoin('school_quiz_translations as sqt_sort', function ($join) use ($locale) {
                    $join->on('sqt_sort.school_quiz_id', '=', 'school_quiz_attempts.school_quiz_id')
                        ->where('sqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqt_sort.title', 'asc')
                ->orderByDesc('school_quiz_attempts.id')
                ->select('school_quiz_attempts.*'),

            'quizTitleDesc' => $q
                ->leftJoin('school_quiz_translations as sqt_sort', function ($join) use ($locale) {
                    $join->on('sqt_sort.school_quiz_id', '=', 'school_quiz_attempts.school_quiz_id')
                        ->where('sqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqt_sort.title', 'desc')
                ->orderByDesc('school_quiz_attempts.id')
                ->select('school_quiz_attempts.*'),

            default => $q->orderByDesc('school_quiz_attempts.id'),
        };
    }
}
