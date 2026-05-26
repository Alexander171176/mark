<?php

namespace App\Models\Admin\School\Quiz;

use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\Lesson\SchoolLesson;
use App\Models\Admin\School\Module\SchoolModule;
use App\Models\Admin\School\QuizAttempt\SchoolQuizAttempt;
use App\Models\Admin\School\QuizQuestion\SchoolQuizQuestion;
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
        if (!$term) {
            return $q;
        }

        $locale = $locale ?: app()->getLocale();

        return $q->where(function (Builder $query) use ($term, $locale) {
            $query->where('slug', 'like', "%{$term}%")
                ->orWhereHas('translations', function (Builder $qq) use ($term, $locale) {
                    $qq->where('locale', $locale)
                        ->where(function (Builder $sub) use ($term) {
                            $sub->where('title', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%");
                        });
                });
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'sort_asc' => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc' => $q->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc' => $q->orderBy('published_at', 'asc')->orderByDesc('id'),
            'date_desc' => $q->orderBy('published_at', 'desc')->orderByDesc('id'),
            'pass_score_asc' => $q->orderBy('pass_score', 'asc')->orderByDesc('id'),
            'pass_score_desc' => $q->orderBy('pass_score', 'desc')->orderByDesc('id'),
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
