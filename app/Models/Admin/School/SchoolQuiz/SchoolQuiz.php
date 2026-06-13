<?php

namespace App\Models\Admin\School\SchoolQuiz;

use App\Models\Admin\School\SchoolQuizAttempt\SchoolQuizAttempt;
use App\Models\Admin\School\SchoolQuizQuestion\SchoolQuizQuestion;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolQuiz extends Model
{
    use HasFactory;

    protected $table = 'school_quizzes';

    protected $fillable = [
        'school_course_id',
        'school_module_id',
        'school_lesson_id',
        'slug',
        'type',
        'attempts_limit',
        'time_limit_minutes',
        'pass_score',
        'sort',
        'activity',
        'published_at',
        'left',
        'main',
        'right',
    ];

    protected $casts = [
        'school_course_id' => 'integer',
        'school_module_id' => 'integer',
        'school_lesson_id' => 'integer',
        'attempts_limit' => 'integer',
        'time_limit_minutes' => 'integer',
        'pass_score' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',
        'published_at' => 'datetime',
        'left' => 'boolean',
        'main' => 'boolean',
        'right' => 'boolean',
    ];

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolQuizTranslation::class, 'school_quiz_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolQuizTranslation::class, 'school_quiz_id')
            ->where('locale', app()->getLocale());
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

    /** Вопросы квиза */
    public function questions(): HasMany
    {
        return $this->hasMany(SchoolQuizQuestion::class, 'school_quiz_id')
            ->orderBy('sort');
    }

    /** Попытки прохождения */
    public function attempts(): HasMany
    {
        return $this->hasMany(SchoolQuizAttempt::class, 'school_quiz_id');
    }

    /** Изображения квиза */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolQuizImage::class,
            'school_quiz_has_images',
            'school_quiz_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('school_quiz_has_images.order', 'asc');
    }

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Опубликованные */
    public function scopePublished(Builder $q): Builder
    {
        return $q
            ->where('activity', true)
            ->whereNotNull('published_at');
    }

    /** Сортировка */
    public function scopeSorted(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /** Алиас сортировки */
    public function scopeOrdered(Builder $q): Builder
    {
        return $this->scopeSorted($q);
    }

    /** Главный блок */
    public function scopeMain(Builder $q): Builder
    {
        return $q->where('main', true);
    }

    /** Левая колонка */
    public function scopeLeft(Builder $q): Builder
    {
        return $q->where('left', true);
    }

    /** Правая колонка */
    public function scopeRight(Builder $q): Builder
    {
        return $q->where('right', true);
    }

    /** Подгрузка перевода */
    public function scopeWithLocale(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q->with([
            'translations' => fn ($query) => $query->where('locale', $locale),
        ]);
    }

    /** Публичный набор */
    public function scopeForPublic(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q
            ->active()
            ->published()
            ->whereHas('translations', fn ($qq) => $qq->where('locale', $locale))
            ->withLocale($locale);
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
                        ->where('school_quizzes.slug', 'like', "%{$word}%")
                        ->orWhere('school_quizzes.type', 'like', "%{$word}%")

                        ->orWhereHas('translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('description', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('course.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('description', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('module.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('description', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('lesson.translations', function (Builder $qq) use ($word, $locale) {
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

            'sortAsc' => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sortDesc' => $q->orderBy('sort', 'desc')->orderByDesc('id'),

            'titleAsc' => $q
                ->leftJoin('school_quiz_translations as sqt_sort', function ($join) use ($locale) {
                    $join->on('sqt_sort.school_quiz_id', '=', 'school_quizzes.id')
                        ->where('sqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqt_sort.title', 'asc')
                ->orderByDesc('school_quizzes.id')
                ->select('school_quizzes.*'),

            'titleDesc' => $q
                ->leftJoin('school_quiz_translations as sqt_sort', function ($join) use ($locale) {
                    $join->on('sqt_sort.school_quiz_id', '=', 'school_quizzes.id')
                        ->where('sqt_sort.locale', '=', $locale);
                })
                ->orderBy('sqt_sort.title', 'desc')
                ->orderByDesc('school_quizzes.id')
                ->select('school_quizzes.*'),

            'slugAsc' => $q->orderBy('slug', 'asc')->orderByDesc('id'),
            'slugDesc' => $q->orderBy('slug', 'desc')->orderByDesc('id'),

            'typeAsc' => $q->orderBy('type', 'asc')->orderByDesc('id'),
            'typeDesc' => $q->orderBy('type', 'desc')->orderByDesc('id'),

            'passScoreAsc' => $q->orderBy('pass_score', 'asc')->orderByDesc('id'),
            'passScoreDesc' => $q->orderBy('pass_score', 'desc')->orderByDesc('id'),

            'attemptsLimitAsc' => $q->orderBy('attempts_limit', 'asc')->orderByDesc('id'),
            'attemptsLimitDesc' => $q->orderBy('attempts_limit', 'desc')->orderByDesc('id'),

            'timeLimitAsc' => $q->orderBy('time_limit_minutes', 'asc')->orderByDesc('id'),
            'timeLimitDesc' => $q->orderBy('time_limit_minutes', 'desc')->orderByDesc('id'),

            'questionsAsc' => $q->withCount('questions')->orderBy('questions_count', 'asc')->orderByDesc('id'),
            'questionsDesc' => $q->withCount('questions')->orderBy('questions_count', 'desc')->orderByDesc('id'),

            'attemptsAsc' => $q->withCount('attempts')->orderBy('attempts_count', 'asc')->orderByDesc('id'),
            'attemptsDesc' => $q->withCount('attempts')->orderBy('attempts_count', 'desc')->orderByDesc('id'),

            'imagesAsc' => $q->withCount('images')->orderBy('images_count', 'asc')->orderByDesc('id'),
            'imagesDesc' => $q->withCount('images')->orderBy('images_count', 'desc')->orderByDesc('id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $q->where('activity', true)->orderByDesc('id'),
            'inactive' => $q->where('activity', false)->orderByDesc('id'),

            'left' => $q->where('left', true)->orderByDesc('id'),
            'noLeft' => $q->where('left', false)->orderByDesc('id'),

            'main' => $q->where('main', true)->orderByDesc('id'),
            'noMain' => $q->where('main', false)->orderByDesc('id'),

            'right' => $q->where('right', true)->orderByDesc('id'),
            'noRight' => $q->where('right', false)->orderByDesc('id'),

            'graded' => $q->where('type', 'graded')->orderByDesc('id'),
            'practice' => $q->where('type', 'practice')->orderByDesc('id'),

            'publishedAtAsc', 'dateAsc' => $q->orderBy('published_at', 'asc')->orderByDesc('id'),
            'publishedAtDesc', 'dateDesc' => $q->orderBy('published_at', 'desc')->orderByDesc('id'),

            'createdAtAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('id'),

            default => $q->sorted(),
        };
    }

    /** Главное изображение */
    public function getPrimaryImageAttribute(): ?SchoolQuizImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                ->first();
        }

        return $this->images()
            ->orderBy('school_quiz_has_images.order', 'asc')
            ->first();
    }
}
