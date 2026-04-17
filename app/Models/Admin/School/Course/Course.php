<?php

namespace App\Models\Admin\School\Course;

use App\Models\Admin\Blog\Comment\Comment;
use App\Models\Admin\Finance\CoursePrice\CoursePrice;
use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\CourseSchedule\CourseSchedule;
use App\Models\Admin\School\Enrollment\Enrollment;
use App\Models\Admin\School\Hashtag\Hashtag;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Models\Admin\School\LearningCategory\LearningCategory;
use App\Models\Admin\School\Lesson\Lesson;
use App\Models\Admin\School\Module\Module;
use App\Models\Admin\School\Quiz\Quiz;
use App\Models\Admin\School\Review\Review;
use App\Models\User\Like\CourseLike;
use App\Traits\HashtagsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Курс
 *
 * @property int $id
 * @property int $instructor_profile_id
 * @property string $locale
 * @property string $title
 * @property string $slug
 * @property string|null $subtitle
 * @property string|null $short
 * @property string|null $description
 *
 * @property string|null $level
 * @property string|null $status          // draft|published|archived
 * @property string|null $availability    // public|unlisted|private
 * @property int|null    $difficulty
 * @property int|null    $duration
 *
 * @property bool $is_new
 * @property bool $is_hit
 * @property bool $is_sale
 * @property bool $left
 * @property bool $main
 * @property bool $right
 *
 * @property string|null $meta_title
 * @property string|null $meta_keywords
 * @property string|null $meta_desc
 *
 * @property bool $activity
 * @property int  $sort
 *
 * @property float $rating_avg
 * @property int   $rating_count
 * @property int   $students_count
 * @property int   $popularity
 * @property int   $views
 * @property int   $likes
 *
 * @property Carbon|null $published_at
 * @property Carbon       $created_at
 * @property Carbon       $updated_at
 * @property Carbon|null  $deleted_at
 *
 * @property-read InstructorProfile                $instructorProfile
 * @property-read Collection|Module[]              $modules
 * @property-read Collection|Lesson[]              $lessons
 * @property-read Collection|LearningCategory[]    $learningCategories
 * @property-read Collection|Hashtag[] $hashtags
 * @property-read Collection|CourseImage[]         $images
 * @property-read CourseImage|null                 $primaryImage
 */
class Course extends Model
{
    use HasFactory, SoftDeletes, HashtagsTrait;

    protected $table = 'courses';

    protected $fillable = [
        'instructor_profile_id',
        'locale',

        'title',
        'slug',
        'subtitle',
        'short',
        'description',

        'level',
        'status',
        'availability',
        'difficulty',
        'duration',

        'is_new',
        'is_hit',
        'is_sale',
        'left',
        'main',
        'right',

        'meta_title',
        'meta_keywords',
        'meta_desc',

        'activity',
        'sort',

        'rating_avg',
        'rating_count',
        'students_count',
        'popularity',
        'views',
        'likes',

        'published_at',
    ];

    protected $casts = [
        'difficulty'        => 'int',
        'duration'          => 'int',

        'is_new'            => 'bool',
        'is_hit'            => 'bool',
        'is_sale'           => 'bool',
        'left'              => 'bool',
        'main'              => 'bool',
        'right'             => 'bool',

        'activity'          => 'bool',
        'sort'              => 'int',

        'rating_avg'        => 'float',
        'rating_count'      => 'int',
        'students_count'    => 'int',
        'popularity'        => 'int',
        'views'             => 'int',
        'likes'             => 'int',

        'published_at'      => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Владелец/преподаватель курса */
    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class, 'instructor_profile_id');
    }

    /** Модули курса (1:N) */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }

    /** Уроки через модули (удобно для подсчётов/фильтров) */
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Module::class);
    }

    /** Категории (M:M через course_has_learning_category) */
    public function learningCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            LearningCategory::class,
            'course_has_learning_category',
            'course_id',
            'learning_category_id'
        )->withTimestamps();
    }

    /** Рекомендованные курсы (текущий курс → другие курсы) */
    public function relatedCourses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_related',
            'course_id',
            'related_course_id'
        );
    }

    /** Курсы, которые указали этот курс как рекомендованный */
    public function relatedBy(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'course_related',
            'related_course_id',
            'course_id'
        );
    }

    /**
     * Комментарии (полиморфные)
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Лайки курса (если модель лайка существует).
     * Если лайки реализованы без модели (через query builder) — можно убрать.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(CourseLike::class, 'course_id');
    }

    /** Изображения курса (через pivot course_has_images) */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            CourseImage::class,
            'course_has_images',
            'course_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('course_has_images.order', 'asc');
    }

    /** Цены курса (история/прайсы) */
    public function prices(): HasMany
    {
        return $this->hasMany(CoursePrice::class);
    }

    /** Бандлы, в которые входит курс (M:M через bundle_has_course) */
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(
            Bundle::class,
            'bundle_has_course',
            'course_id',
            'bundle_id'
        )->withTimestamps();
    }

    /** Записи на курс (покупки/зачисления) */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /** Расписания наборов/потоков */
    public function schedules(): HasMany
    {
        return $this->hasMany(CourseSchedule::class);
    }

    /** Тесты, привязанные к курсу */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Отзывы по курсу
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /* ======================== Scopes ======================== */

    /** Удобная сортировка: по sort ↑ затем по id ↓ */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** По локали */
    public function scopeByLocale(Builder $q, string $locale): Builder
    {
        return $q->where('locale', $locale);
    }

    /**
     * Публичный скоуп:
     * active + locale + published
     */
    public function scopeForPublic(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q
            ->active()
            ->byLocale($locale)
            ->published();
    }

    /** В Новинки по флагу is_new */
    public function scopeIsNew(Builder $q): Builder
    {
        return $q->where('is_new', true);
    }

    /** В Рекомендованные по флагу is_hit */
    public function scopeIsHit(Builder $q): Builder
    {
        return $q->where('is_hit', true);
    }

    /** В Распродажу по флагу is_sale */
    public function scopeIsSale(Builder $q): Builder
    {
        return $q->where('is_sale', true);
    }

    /** Избранные (для левой колонки) — теперь по флагу left */
    public function scopeLeft(Builder $q): Builder
    {
        return $q->where('left', true);
    }

    /** Избранные (для главного окна) — теперь по флагу main */
    public function scopeMain(Builder $q): Builder
    {
        return $q->where('main', true);
    }

    /** Избранные (для правой колонки) — теперь по флагу right */
    public function scopeRight(Builder $q): Builder
    {
        return $q->where('right', true);
    }

    /**
     * Опубликованные и не private
     * status: published, availability: не private
     */
    public function scopePublished(Builder $q): Builder
    {
        return $q
            ->where('status', 'published')
            ->where('availability', '!=', 'private')
            ->whereNotNull('published_at');
    }

    /** Поиск по заголовку/подзаголовку/описанию */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) {
            return $q;
        }

        return $q->where(function (Builder $qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
                ->orWhere('subtitle', 'like', "%{$term}%")
                ->orWhere('short', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
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
            'title_asc'       => $q->orderBy('title', 'asc')->orderByDesc('id'),
            'title_desc'      => $q->orderBy('title', 'desc')->orderByDesc('id'),
            default           => $q->sorted(),
        };
    }

    /* ======================== Accessors ======================== */

    /** Главное изображение (с наименьшим order) */
    public function getPrimaryImageAttribute(): ?CourseImage
    {
        $relLoaded = $this->relationLoaded('images')
            ? $this->images
            : $this->images()->take(1)->get();

        return $relLoaded->first();
    }

}
