<?php

namespace App\Models\Admin\School\SchoolAssignment;

use App\Models\Admin\School\SchoolAssignmentSubmission\SchoolAssignmentSubmission;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
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
        $term = trim((string) $term);

        if ($term === '') {
            return $q;
        }

        $locale = $locale ?: app()->getLocale();

        $words = collect(preg_split('/[\s:#№,"\'«»(){}\[\].!?\/\\\\|]+/u', $term))
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
                        ->where('school_assignments.slug', 'like', "%{$word}%")
                        ->orWhere('school_assignments.id', 'like', "%{$word}%")
                        ->orWhere('school_assignments.sort', 'like', "%{$word}%")
                        ->orWhere('school_assignments.school_course_id', 'like', "%{$word}%")
                        ->orWhere('school_assignments.school_module_id', 'like', "%{$word}%")
                        ->orWhere('school_assignments.school_lesson_id', 'like', "%{$word}%")
                        ->orWhere('school_assignments.school_instructor_profile_id', 'like', "%{$word}%")
                        ->orWhere('school_assignments.status', 'like', "%{$word}%")
                        ->orWhere('school_assignments.visibility', 'like', "%{$word}%")
                        ->orWhere('school_assignments.grading_type', 'like', "%{$word}%")
                        ->orWhere('school_assignments.max_score', 'like', "%{$word}%")
                        ->orWhere('school_assignments.attempts_limit', 'like', "%{$word}%")

                        ->orWhereHas('translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('instructions', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('course.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('module.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('lesson.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('instructor.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('instructor.user', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%")
                                ->orWhere('email', 'like', "%{$word}%");
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

            'slugAsc' => $q->orderBy('school_assignments.slug', 'asc')
                ->orderByDesc('school_assignments.id'),
            'slugDesc' => $q->orderBy('school_assignments.slug', 'desc')
                ->orderByDesc('school_assignments.id'),

            'titleAsc' => $q
                ->leftJoin('school_assignment_translations as sat_sort', function ($join) use ($locale) {
                    $join->on('sat_sort.school_assignment_id', '=', 'school_assignments.id')
                        ->where('sat_sort.locale', '=', $locale);
                })
                ->orderBy('sat_sort.title', 'asc')
                ->orderByDesc('school_assignments.id')
                ->select('school_assignments.*'),

            'titleDesc' => $q
                ->leftJoin('school_assignment_translations as sat_sort', function ($join) use ($locale) {
                    $join->on('sat_sort.school_assignment_id', '=', 'school_assignments.id')
                        ->where('sat_sort.locale', '=', $locale);
                })
                ->orderBy('sat_sort.title', 'desc')
                ->orderByDesc('school_assignments.id')
                ->select('school_assignments.*'),

            'courseAsc' => $q->orderBy('school_course_id', 'asc')->orderByDesc('id'),
            'courseDesc' => $q->orderBy('school_course_id', 'desc')->orderByDesc('id'),

            'courseTitleAsc' => $q
                ->leftJoin('school_course_translations as sct_sort', function ($join) use ($locale) {
                    $join->on('sct_sort.course_id', '=', 'school_assignments.school_course_id')
                        ->where('sct_sort.locale', '=', $locale);
                })
                ->orderBy('sct_sort.title', 'asc')
                ->orderByDesc('school_assignments.id')
                ->select('school_assignments.*'),

            'courseTitleDesc' => $q
                ->leftJoin('school_course_translations as sct_sort', function ($join) use ($locale) {
                    $join->on('sct_sort.course_id', '=', 'school_assignments.school_course_id')
                        ->where('sct_sort.locale', '=', $locale);
                })
                ->orderBy('sct_sort.title', 'desc')
                ->orderByDesc('school_assignments.id')
                ->select('school_assignments.*'),

            'moduleAsc' => $q->orderBy('school_module_id', 'asc')->orderByDesc('id'),
            'moduleDesc' => $q->orderBy('school_module_id', 'desc')->orderByDesc('id'),

            'moduleTitleAsc' => $q
                ->leftJoin('school_module_translations as smt_sort', function ($join) use ($locale) {
                    $join->on('smt_sort.module_id', '=', 'school_assignments.school_module_id')
                        ->where('smt_sort.locale', '=', $locale);
                })
                ->orderBy('smt_sort.title', 'asc')
                ->orderByDesc('school_assignments.id')
                ->select('school_assignments.*'),

            'moduleTitleDesc' => $q
                ->leftJoin('school_module_translations as smt_sort', function ($join) use ($locale) {
                    $join->on('smt_sort.module_id', '=', 'school_assignments.school_module_id')
                        ->where('smt_sort.locale', '=', $locale);
                })
                ->orderBy('smt_sort.title', 'desc')
                ->orderByDesc('school_assignments.id')
                ->select('school_assignments.*'),

            'lessonAsc' => $q->orderBy('school_lesson_id', 'asc')->orderByDesc('id'),
            'lessonDesc' => $q->orderBy('school_lesson_id', 'desc')->orderByDesc('id'),

            'lessonTitleAsc' => $q
                ->leftJoin('school_lesson_translations as slt_sort', function ($join) use ($locale) {
                    $join->on('slt_sort.lesson_id', '=', 'school_assignments.school_lesson_id')
                        ->where('slt_sort.locale', '=', $locale);
                })
                ->orderBy('slt_sort.title', 'asc')
                ->orderByDesc('school_assignments.id')
                ->select('school_assignments.*'),

            'lessonTitleDesc' => $q
                ->leftJoin('school_lesson_translations as slt_sort', function ($join) use ($locale) {
                    $join->on('slt_sort.lesson_id', '=', 'school_assignments.school_lesson_id')
                        ->where('slt_sort.locale', '=', $locale);
                })
                ->orderBy('slt_sort.title', 'desc')
                ->orderByDesc('school_assignments.id')
                ->select('school_assignments.*'),

            'instructorAsc' => $q->orderBy('school_instructor_profile_id', 'asc')
                ->orderByDesc('id'),
            'instructorDesc' => $q->orderBy('school_instructor_profile_id', 'desc')
                ->orderByDesc('id'),

            'instructorTitleAsc' => $q
                ->leftJoin('school_instructor_profile_translations as sipt_sort', function ($join) use ($locale) {
                    $join->on('sipt_sort.instructor_profile_id', '=', 'school_assignments.school_instructor_profile_id')
                        ->where('sipt_sort.locale', '=', $locale);
                })
                ->orderBy('sipt_sort.title', 'asc')
                ->orderByDesc('school_assignments.id')
                ->select('school_assignments.*'),

            'instructorTitleDesc' => $q
                ->leftJoin('school_instructor_profile_translations as sipt_sort', function ($join) use ($locale) {
                    $join->on('sipt_sort.instructor_profile_id', '=', 'school_assignments.school_instructor_profile_id')
                        ->where('sipt_sort.locale', '=', $locale);
                })
                ->orderBy('sipt_sort.title', 'desc')
                ->orderByDesc('school_assignments.id')
                ->select('school_assignments.*'),

            'statusAsc' => $q->orderBy('status', 'asc')->orderByDesc('id'),
            'statusDesc' => $q->orderBy('status', 'desc')->orderByDesc('id'),

            'visibilityAsc' => $q->orderBy('visibility', 'asc')->orderByDesc('id'),
            'visibilityDesc' => $q->orderBy('visibility', 'desc')->orderByDesc('id'),

            'gradingTypeAsc' => $q->orderBy('grading_type', 'asc')->orderByDesc('id'),
            'gradingTypeDesc' => $q->orderBy('grading_type', 'desc')->orderByDesc('id'),

            'attemptsLimitAsc' => $q->orderBy('attempts_limit', 'asc')->orderByDesc('id'),
            'attemptsLimitDesc' => $q->orderBy('attempts_limit', 'desc')->orderByDesc('id'),

            'maxScoreAsc' => $q->orderBy('max_score', 'asc')->orderByDesc('id'),
            'maxScoreDesc' => $q->orderBy('max_score', 'desc')->orderByDesc('id'),

            'submissionsAsc' => $q->withCount('submissions')
                ->orderBy('submissions_count', 'asc')->orderByDesc('id'),
            'submissionsDesc' => $q->withCount('submissions')
                ->orderBy('submissions_count', 'desc')->orderByDesc('id'),

            'imagesAsc' => $q->withCount('images')
                ->orderBy('images_count', 'asc')->orderByDesc('id'),
            'imagesDesc' => $q->withCount('images')
                ->orderBy('images_count', 'desc')->orderByDesc('id'),

            'publishedAtAsc', 'dateAsc' => $q->orderBy('published_at', 'asc')
                ->orderByDesc('id'),
            'publishedAtDesc', 'dateDesc' => $q->orderBy('published_at', 'desc')
                ->orderByDesc('id'),

            'dueAtAsc' => $q->orderBy('due_at', 'asc')->orderByDesc('id'),
            'dueAtDesc' => $q->orderBy('due_at', 'desc')->orderByDesc('id'),

            'createdAtAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('id'),

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

            default => $q->sorted(),
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
