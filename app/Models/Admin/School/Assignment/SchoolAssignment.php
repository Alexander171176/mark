<?php

namespace App\Models\Admin\School\Assignment;

use App\Models\Admin\School\AssignmentSubmission\SchoolAssignmentSubmission;
use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\InstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\Lesson\SchoolLesson;
use App\Models\Admin\School\Module\SchoolModule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolAssignment extends Model
{
    use HasFactory;

    protected $table = 'school_assignments';

    protected $fillable = [
        'school_course_id',
        'school_module_id',
        'school_lesson_id',
        'school_instructor_profile_id',
        'slug',
        'sort',
        'activity',
        'left',
        'main',
        'right',
        'published_at',
        'status',
        'visibility',
        'attempts_limit',
        'grading_type',
        'max_score',
        'due_at',
    ];

    protected $casts = [
        'school_course_id' => 'integer',
        'school_module_id' => 'integer',
        'school_lesson_id' => 'integer',
        'school_instructor_profile_id' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',
        'left' => 'boolean',
        'main' => 'boolean',
        'right' => 'boolean',
        'published_at' => 'datetime',
        'attempts_limit' => 'integer',
        'max_score' => 'integer',
        'due_at' => 'datetime',
    ];

    /* ======================== Translations ======================== */

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolAssignmentTranslation::class, 'school_assignment_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolAssignmentTranslation::class, 'school_assignment_id')
            ->where('locale', app()->getLocale());
    }

    /* ======================== Relations ======================== */

    /** Курс задания */
    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    /** Модуль задания */
    public function module(): BelongsTo
    {
        return $this->belongsTo(SchoolModule::class, 'school_module_id');
    }

    /** Урок задания */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(SchoolLesson::class, 'school_lesson_id');
    }

    /** Преподаватель */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(SchoolInstructorProfile::class, 'school_instructor_profile_id');
    }

    /** Сдачи студентов */
    public function submissions(): HasMany
    {
        return $this->hasMany(SchoolAssignmentSubmission::class, 'school_assignment_id');
    }

    /** Изображения задания */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolAssignmentImage::class,
            'school_assignment_has_images',
            'school_assignment_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('school_assignment_has_images.order', 'asc');
    }

    /* ======================== Scopes ======================== */

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Опубликованные */
    public function scopePublished(Builder $q): Builder
    {
        return $q
            ->where('status', 'published')
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

    /** По курсу */
    public function scopeForCourse(Builder $q, int $courseId): Builder
    {
        return $q->where('school_course_id', $courseId);
    }

    /** По модулю */
    public function scopeForModule(Builder $q, int $moduleId): Builder
    {
        return $q->where('school_module_id', $moduleId);
    }

    /** По уроку */
    public function scopeForLesson(Builder $q, int $lessonId): Builder
    {
        return $q->where('school_lesson_id', $lessonId);
    }

    /** Ближайший дедлайн */
    public function scopeDueSoon(Builder $q, int $days = 7): Builder
    {
        return $q
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [now(), now()->addDays($days)]);
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
                                ->orWhere('subtitle', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%")
                                ->orWhere('instructions', 'like', "%{$term}%");
                        });
                });
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'sort_asc'  => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc' => $q->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc'  => $q->orderBy('published_at', 'asc')->orderByDesc('id'),
            'date_desc' => $q->orderBy('published_at', 'desc')->orderByDesc('id'),
            'score_asc' => $q->orderBy('max_score', 'asc')->orderByDesc('id'),
            'score_desc'=> $q->orderBy('max_score', 'desc')->orderByDesc('id'),
            'due_asc'   => $q->orderBy('due_at', 'asc')->orderByDesc('id'),
            'due_desc'  => $q->orderBy('due_at', 'desc')->orderByDesc('id'),
            default     => $q->sorted(),
        };
    }

    /* ======================== Accessors ======================== */

    /** Просрочено ли задание */
    public function getIsOverdueAttribute(): bool
    {
        return !is_null($this->due_at) && $this->due_at->isPast();
    }

    /** Главное изображение */
    public function getPrimaryImageAttribute(): ?SchoolAssignmentImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                ->first();
        }

        return $this->images()
            ->orderBy('school_assignment_has_images.order', 'asc')
            ->first();
    }
}
