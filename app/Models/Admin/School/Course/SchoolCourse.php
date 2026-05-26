<?php

namespace App\Models\Admin\School\Course;

use App\Models\Admin\School\Bundle\SchoolBundle;
use App\Models\Admin\School\CourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\Enrollment\SchoolEnrollment;
use App\Models\Admin\School\Hashtag\SchoolHashtag;
use App\Models\Admin\School\InstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\Lesson\SchoolLesson;
use App\Models\Admin\School\Module\SchoolModule;
use App\Models\Admin\School\Price\SchoolCoursePrice;
use App\Models\Admin\School\Quiz\SchoolQuiz;
use App\Models\Admin\School\Review\SchoolReview;
use App\Models\Admin\School\Track\SchoolTrack;
use App\Models\User\Like\SchoolCourseLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class SchoolCourse extends Model
{
    use HasFactory;

    protected $table = 'school_courses';

    protected $fillable = [
        'school_instructor_profile_id',
        'sort',
        'activity',
        'is_new',
        'is_hit',
        'is_sale',
        'left',
        'main',
        'right',
        'slug',
        'published_at',
        'level',
        'status',
        'availability',
        'difficulty',
        'duration',
        'students_count',
        'popularity',
        'rating_count',
        'rating_avg',
        'views',
        'likes',
    ];

    protected $casts = [
        'school_instructor_profile_id' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',

        'is_new' => 'boolean',
        'is_hit' => 'boolean',
        'is_sale' => 'boolean',
        'left' => 'boolean',
        'main' => 'boolean',
        'right' => 'boolean',

        'published_at' => 'datetime',

        'difficulty' => 'integer',
        'duration' => 'integer',
        'students_count' => 'integer',
        'popularity' => 'integer',
        'rating_count' => 'integer',
        'rating_avg' => 'float',
        'views' => 'integer',
        'likes' => 'integer',
    ];

    /* ======================== Translations ======================== */

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolCourseTranslation::class, 'school_course_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolCourseTranslation::class, 'school_course_id')
            ->where('locale', app()->getLocale());
    }

    /* ======================== Relations ======================== */

    /** Преподаватель курса */
    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(SchoolInstructorProfile::class, 'school_instructor_profile_id');
    }

    /** Модули курса */
    public function modules(): HasMany
    {
        return $this->hasMany(SchoolModule::class, 'school_course_id');
    }

    /** Уроки через модули */
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(
            SchoolLesson::class,
            SchoolModule::class,
            'school_course_id',
            'school_module_id',
            'id',
            'id'
        );
    }

    /** Треки курса */
    public function tracks(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolTrack::class,
            'school_course_has_tracks',
            'school_course_id',
            'school_track_id'
        )->withTimestamps();
    }

    /** Хештеги курса */
    public function hashtags(): MorphToMany
    {
        return $this->morphToMany(
            SchoolHashtag::class,
            'hashtaggable',
            'school_hashtaggables',
            'hashtaggable_id',
            'school_hashtag_id'
        )->withTimestamps();
    }

    /** Рекомендованные курсы */
    public function relatedCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'school_course_related',
            'school_course_id',
            'related_school_course_id'
        );
    }

    /** Курсы, где этот курс рекомендован */
    public function relatedBy(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'school_course_related',
            'related_school_course_id',
            'school_course_id'
        );
    }

    /** Изображения курса */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolCourseImage::class,
            'school_course_has_images',
            'school_course_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('school_course_has_images.order', 'asc');
    }

    /** Цены курса */
    public function prices(): HasMany
    {
        return $this->hasMany(SchoolCoursePrice::class, 'school_course_id');
    }

    /** Наборы, в которые входит курс */
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolBundle::class,
            'school_bundle_has_courses',
            'school_course_id',
            'school_bundle_id'
        )->withTimestamps();
    }

    /** Зачисления на курс */
    public function enrollments(): HasMany
    {
        return $this->hasMany(SchoolEnrollment::class, 'school_course_id');
    }

    /** Расписания/потоки курса */
    public function schedules(): HasMany
    {
        return $this->hasMany(SchoolCourseSchedule::class, 'school_course_id');
    }

    /** Квизы курса */
    public function quizzes(): HasMany
    {
        return $this->hasMany(SchoolQuiz::class, 'school_course_id');
    }

    /** Лайки курса */
    public function likes(): HasMany
    {
        return $this->hasMany(SchoolCourseLike::class, 'school_course_id');
    }

    /** Отзывы курса */
    public function reviews(): MorphMany
    {
        return $this->morphMany(SchoolReview::class, 'reviewable');
    }

    /* ======================== Scopes ======================== */

    /** Сортировка */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
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
            ->where('status', 'published')
            ->where('availability', '!=', 'private')
            ->whereNotNull('published_at');
    }

    /** Новинки */
    public function scopeIsNew(Builder $q): Builder
    {
        return $q->where('is_new', true);
    }

    /** Рекомендуемые */
    public function scopeIsHit(Builder $q): Builder
    {
        return $q->where('is_hit', true);
    }

    /** Со скидкой */
    public function scopeIsSale(Builder $q): Builder
    {
        return $q->where('is_sale', true);
    }

    /** Левая колонка */
    public function scopeLeft(Builder $q): Builder
    {
        return $q->where('left', true);
    }

    /** Главный блок */
    public function scopeMain(Builder $q): Builder
    {
        return $q->where('main', true);
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
                                ->orWhere('subtitle', 'like', "%{$term}%")
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
            'sort_asc'        => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc'       => $q->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc'        => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'date_desc'       => $q->orderBy('created_at', 'desc')->orderByDesc('id'),
            'views_asc'       => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'views_desc'      => $q->orderBy('views', 'desc')->orderByDesc('id'),
            'likes_asc'       => $q->withCount('likes')->orderBy('likes_count', 'asc')->orderByDesc('id'),
            'likes_desc'      => $q->withCount('likes')->orderBy('likes_count', 'desc')->orderByDesc('id'),
            'rating_asc'      => $q->orderBy('rating_avg', 'asc')->orderByDesc('id'),
            'rating_desc'     => $q->orderBy('rating_avg', 'desc')->orderByDesc('id'),
            'popularity_asc'  => $q->orderBy('popularity', 'asc')->orderByDesc('id'),
            'popularity_desc' => $q->orderBy('popularity', 'desc')->orderByDesc('id'),
            'students_asc'    => $q->orderBy('students_count', 'asc')->orderByDesc('id'),
            'students_desc'   => $q->orderBy('students_count', 'desc')->orderByDesc('id'),
            'duration_asc'    => $q->orderBy('duration', 'asc')->orderByDesc('id'),
            'duration_desc'   => $q->orderBy('duration', 'desc')->orderByDesc('id'),
            default           => $q->ordered(),
        };
    }

    /* ======================== Accessors ======================== */

    /** Главное изображение */
    public function getPrimaryImageAttribute(): ?SchoolCourseImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                ->first();
        }

        return $this->images()
            ->orderBy('school_course_has_images.order', 'asc')
            ->first();
    }
}
