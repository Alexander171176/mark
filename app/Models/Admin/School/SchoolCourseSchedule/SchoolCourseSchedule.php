<?php

namespace App\Models\Admin\School\SchoolCourseSchedule;

use App\Models\Admin\School\SchoolCohortEnrollment\SchoolCohortEnrollment;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolCourseSchedule extends Model
{
    use HasFactory;

    protected $table = 'school_course_schedules';

    protected $fillable = [
        'school_course_id',
        'school_instructor_profile_id',
        'sort',
        'activity',
        'slug',
        'starts_at',
        'ends_at',
        'enroll_starts_at',
        'enroll_ends_at',
        'capacity',
        'is_online',
        'location',
        'meeting_url',
        'timezone',
        'status',
        'views',
        'notes',
    ];

    protected $casts = [
        'school_course_id' => 'integer',
        'school_instructor_profile_id' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'enroll_starts_at' => 'datetime',
        'enroll_ends_at' => 'datetime',
        'capacity' => 'integer',
        'is_online' => 'boolean',
        'views' => 'integer',
    ];

    /* ======================== Translations ======================== */

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolCourseScheduleTranslation::class, 'school_course_schedule_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolCourseScheduleTranslation::class, 'school_course_schedule_id')
            ->where('locale', app()->getLocale());
    }

    /* ======================== Relations ======================== */

    /** Родительский курс */
    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    /** Преподаватель потока */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(SchoolInstructorProfile::class, 'school_instructor_profile_id');
    }

    /** Заявки на поток */
    public function cohortEnrollments(): HasMany
    {
        return $this->hasMany(SchoolCohortEnrollment::class, 'school_course_schedule_id');
    }

    /** Изображения потока */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolCourseScheduleImage::class,
            'school_course_schedule_has_images',
            'school_course_schedule_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('school_course_schedule_has_images.order', 'asc');
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
            ->where('activity', true);
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
                ->orWhere('location', 'like', "%{$term}%")
                ->orWhere('meeting_url', 'like', "%{$term}%")
                ->orWhereHas('translations', function (Builder $qq) use ($term, $locale) {
                    $qq->where('locale', $locale)
                        ->where(function (Builder $sub) use ($term) {
                            $sub->where('title', 'like', "%{$term}%")
                                ->orWhere('subtitle', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%");
                        });
                });
        });
    }

    /** Будущие потоки */
    public function scopeUpcomingDates(Builder $q): Builder
    {
        return $q
            ->where('status', 'published')
            ->whereNotNull('starts_at')
            ->where('starts_at', '>', now());
    }

    /** Текущие потоки */
    public function scopeRunning(Builder $q): Builder
    {
        return $q
            ->where('status', 'published')
            ->whereNotNull('starts_at')
            ->whereNotNull('ends_at')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    /** Открытые для записи */
    public function scopeOpenForEnroll(Builder $q): Builder
    {
        return $q
            ->where('status', 'published')
            ->where(function (Builder $query) {
                $query->whereNull('enroll_starts_at')
                    ->orWhere('enroll_starts_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('enroll_ends_at')
                    ->orWhere('enroll_ends_at', '>=', now());
            });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'sort_asc'   => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc'  => $q->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc'   => $q->orderBy('starts_at', 'asc')->orderByDesc('id'),
            'date_desc'  => $q->orderBy('starts_at', 'desc')->orderByDesc('id'),
            'views_asc'  => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'views_desc' => $q->orderBy('views', 'desc')->orderByDesc('id'),
            default      => $q->sorted(),
        };
    }

    /* ======================== Accessors ======================== */

    /** Открыта ли запись */
    public function getIsEnrollmentOpenAttribute(): bool
    {
        $now = now();

        $okStart = is_null($this->enroll_starts_at) || $this->enroll_starts_at->lte($now);
        $okEnd = is_null($this->enroll_ends_at) || $this->enroll_ends_at->gte($now);

        return $this->status === 'published' && $okStart && $okEnd;
    }

    /** Главное изображение */
    public function getPrimaryImageAttribute(): ?SchoolCourseScheduleImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                ->first();
        }

        return $this->images()
            ->orderBy('school_course_schedule_has_images.order', 'asc')
            ->first();
    }
}
