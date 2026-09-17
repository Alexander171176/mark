<?php

namespace App\Models\Admin\School\SchoolCourse;

use App\Models\Admin\School\SchoolBundle\SchoolBundle;
use App\Models\Admin\School\SchoolCoursePrice\SchoolCoursePrice;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\SchoolEnrollment\SchoolEnrollment;
use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\Admin\School\SchoolQuiz\SchoolQuiz;
use App\Models\Admin\School\SchoolReview\SchoolReview;
use App\Models\Admin\School\SchoolTrack\SchoolTrack;
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
        return $this->hasMany(
            SchoolCourseTranslation::class,
            'school_course_id'
        );
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(
            SchoolCourseTranslation::class,
            'school_course_id'
        )
            ->where(
                'locale',
                app()->getLocale()
            );
    }

    /**
     * Получить уже загруженный перевод:
     *
     * current locale
     * → fallback locale.
     *
     * Метод не выполняет SQL-запросов.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?SchoolCourseTranslation {
        if (!$this->relationLoaded('translations')) {
            return null;
        }

        $locale ??= app()->getLocale();

        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $this->translations
            ->firstWhere(
                'locale',
                $locale
            );

        if ($translation) {
            return $translation;
        }

        if ($fallbackLocale === $locale) {
            return null;
        }

        return $this->translations
            ->firstWhere(
                'locale',
                $fallbackLocale
            );
    }

    /* ======================== Relations ======================== */

    /** Преподаватель курса */
    public function instructorProfile(): BelongsTo
    {
        return $this->belongsTo(
            SchoolInstructorProfile::class,
            'school_instructor_profile_id'
        );
    }

    /** Модули курса */
    public function modules(): HasMany
    {
        return $this->hasMany(
            SchoolModule::class,
            'school_course_id'
        );
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
            ->orderBy(
                'school_course_has_images.order',
                'asc'
            );
    }

    /** Цены курса */
    public function prices(): HasMany
    {
        return $this->hasMany(
            SchoolCoursePrice::class,
            'school_course_id'
        );
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
        return $this->hasMany(
            SchoolEnrollment::class,
            'school_course_id'
        );
    }

    /** Расписания/потоки курса */
    public function schedules(): HasMany
    {
        return $this->hasMany(
            SchoolCourseSchedule::class,
            'school_course_id'
        );
    }

    /** Квизы курса */
    public function quizzes(): HasMany
    {
        return $this->hasMany(
            SchoolQuiz::class,
            'school_course_id'
        );
    }

    /** Лайки курса */
    public function likes(): HasMany
    {
        return $this->hasMany(
            SchoolCourseLike::class,
            'school_course_id'
        );
    }

    /** Отзывы курса */
    public function reviews(): MorphMany
    {
        return $this->morphMany(
            SchoolReview::class,
            'reviewable'
        );
    }

    /* ======================== Scopes ======================== */

    /** Сортировка */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q
            ->orderBy('sort')
            ->orderByDesc('id');
    }

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where(
            'activity',
            true
        );
    }

    /** Опубликованные */
    public function scopePublished(Builder $q): Builder
    {
        return $q
            ->where(
                'status',
                'published'
            )
            ->where(
                'availability',
                '!=',
                'private'
            )
            ->whereNotNull(
                'published_at'
            );
    }

    /** Новинки */
    public function scopeIsNew(Builder $q): Builder
    {
        return $q->where(
            'is_new',
            true
        );
    }

    /** Рекомендуемые */
    public function scopeIsHit(Builder $q): Builder
    {
        return $q->where(
            'is_hit',
            true
        );
    }

    /** Со скидкой */
    public function scopeIsSale(Builder $q): Builder
    {
        return $q->where(
            'is_sale',
            true
        );
    }

    /** Левая колонка */
    public function scopeLeft(Builder $q): Builder
    {
        return $q->where(
            'left',
            true
        );
    }

    /** Главный блок */
    public function scopeMain(Builder $q): Builder
    {
        return $q->where(
            'main',
            true
        );
    }

    /** Правая колонка */
    public function scopeRight(Builder $q): Builder
    {
        return $q->where(
            'right',
            true
        );
    }

    /** Подгрузка перевода */
    public function scopeWithLocale(
        Builder $q,
        ?string $locale = null
    ): Builder {
        $locale ??= app()->getLocale();

        return $q->with([
            'translations' =>
                fn ($query) =>
                $query->where(
                    'locale',
                    $locale
                ),
        ]);
    }

    /**
     * Публичный набор.
     *
     * Доступны только:
     * - активные;
     * - опубликованные;
     * - не private;
     * - имеющие current или fallback перевод.
     */
    public function scopeForPublic(
        Builder $q,
        ?string $locale = null
    ): Builder {
        $locale ??= app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        return $q
            ->active()
            ->published()
            ->whereHas(
                'translations',
                fn (Builder $query) =>
                $query->whereIn(
                    'locale',
                    $locales
                )
            )
            ->with([
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),
            ]);
    }

    /* ======================== Admin/Common Search ======================== */

    /**
     * Существующий поиск.
     *
     * ВАЖНО:
     * используется Admin и поэтому
     * его контракт не изменяем.
     */
    public function scopeSearch(
        Builder $q,
        ?string $term,
        ?string $locale = null
    ): Builder {
        if (!$term) {
            return $q;
        }

        $locale ??= app()->getLocale();

        return $q->where(
            function (Builder $query) use (
                $term,
                $locale
            ) {
                $query
                    ->where(
                        'slug',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhereHas(
                        'translations',
                        function (Builder $qq) use (
                            $term,
                            $locale
                        ) {
                            $qq
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (Builder $sub) use (
                                        $term
                                    ) {
                                        $sub
                                            ->where(
                                                'title',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'subtitle',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'short',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'description',
                                                'like',
                                                "%{$term}%"
                                            );
                                    }
                                );
                        }
                    );
            }
        );
    }

    /* ======================== Public Search ======================== */

    /**
     * Public Index search.
     *
     * Полностью соответствует
     * frontend-поиску SchoolCourses/Index.vue:
     *
     * - translation.title;
     * - translation.short;
     * - slug;
     * - instructor translation.title;
     * - instructor user.name.
     *
     * Для переводимых полей соблюдаем
     * тот же контракт, что Resource:
     *
     * current
     * → fallback.
     */
    public function scopePublicSearch(
        Builder $q,
        ?string $term,
        ?string $locale = null
    ): Builder {
        if (!$term) {
            return $q;
        }

        $locale ??= app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        return $this->applyPublicSearch(
            query: $q,
            term: $term,
            locale: $locale,
            fallbackLocale: $fallbackLocale,
            extended: false,
        );
    }

    /**
     * Public search списка курсов
     * на странице конкретного инструктора.
     *
     * Instructor Show.vue дополнительно ищет:
     *
     * - id;
     * - level;
     * - availability.
     *
     * Остальной Public-контракт
     * идентичен Index.
     */
    public function scopePublicInstructorSearch(
        Builder $q,
        ?string $term,
        ?string $locale = null
    ): Builder {
        if (!$term) {
            return $q;
        }

        $locale ??= app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        return $this->applyPublicSearch(
            query: $q,
            term: $term,
            locale: $locale,
            fallbackLocale: $fallbackLocale,
            extended: true,
        );
    }

    /**
     * Общая реализация Public search.
     */
    protected function applyPublicSearch(
        Builder $query,
        string $term,
        string $locale,
        string $fallbackLocale,
        bool $extended = false
    ): Builder {
        return $query->where(
            function (Builder $searchQuery) use (
                $term,
                $locale,
                $fallbackLocale,
                $extended
            ) {
                /**
                 * Непереводимые поля курса.
                 */
                $searchQuery->where(
                    'school_courses.slug',
                    'like',
                    "%{$term}%"
                );

                if ($extended) {
                    $searchQuery
                        ->orWhere(
                            'school_courses.id',
                            'like',
                            "%{$term}%"
                        )
                        ->orWhere(
                            'school_courses.level',
                            'like',
                            "%{$term}%"
                        )
                        ->orWhere(
                            'school_courses.availability',
                            'like',
                            "%{$term}%"
                        );
                }

                /**
                 * Видимый перевод курса:
                 *
                 * current locale.
                 */
                $searchQuery->orWhereHas(
                    'translations',
                    function (Builder $translationQuery) use (
                        $term,
                        $locale
                    ) {
                        $translationQuery
                            ->where(
                                'locale',
                                $locale
                            )
                            ->where(
                                function (Builder $fields) use (
                                    $term
                                ) {
                                    $fields
                                        ->where(
                                            'title',
                                            'like',
                                            "%{$term}%"
                                        )
                                        ->orWhere(
                                            'short',
                                            'like',
                                            "%{$term}%"
                                        );
                                }
                            );
                    }
                );

                /**
                 * Fallback курса ищем только тогда,
                 * когда current translation отсутствует.
                 *
                 * Это важно для полного совпадения
                 * с тем, что реально видит пользователь.
                 */
                if ($locale !== $fallbackLocale) {
                    $searchQuery->orWhere(
                        function (Builder $fallbackQuery) use (
                            $term,
                            $locale,
                            $fallbackLocale
                        ) {
                            $fallbackQuery
                                ->whereDoesntHave(
                                    'translations',
                                    fn (Builder $currentQuery) =>
                                    $currentQuery->where(
                                        'locale',
                                        $locale
                                    )
                                )
                                ->whereHas(
                                    'translations',
                                    function (Builder $translationQuery) use (
                                        $term,
                                        $fallbackLocale
                                    ) {
                                        $translationQuery
                                            ->where(
                                                'locale',
                                                $fallbackLocale
                                            )
                                            ->where(
                                                function (Builder $fields) use (
                                                    $term
                                                ) {
                                                    $fields
                                                        ->where(
                                                            'title',
                                                            'like',
                                                            "%{$term}%"
                                                        )
                                                        ->orWhere(
                                                            'short',
                                                            'like',
                                                            "%{$term}%"
                                                        );
                                                }
                                            );
                                    }
                                );
                        }
                    );
                }

                /**
                 * Публичное имя инструктора:
                 *
                 * current translation.title
                 * → fallback translation.title
                 * → user.name.
                 */
                $searchQuery->orWhereHas(
                    'instructorProfile',
                    function (Builder $instructorQuery) use (
                        $term,
                        $locale,
                        $fallbackLocale
                    ) {
                        /**
                         * Current translation инструктора.
                         */
                        $instructorQuery->where(
                            function (Builder $nameQuery) use (
                                $term,
                                $locale,
                                $fallbackLocale
                            ) {
                                $nameQuery->whereHas(
                                    'translations',
                                    function (Builder $translationQuery) use (
                                        $term,
                                        $locale
                                    ) {
                                        $translationQuery
                                            ->where(
                                                'locale',
                                                $locale
                                            )
                                            ->where(
                                                'title',
                                                'like',
                                                "%{$term}%"
                                            );
                                    }
                                );

                                /**
                                 * Fallback translation инструктора.
                                 */
                                if ($locale !== $fallbackLocale) {
                                    $nameQuery->orWhere(
                                        function (Builder $fallbackQuery) use (
                                            $term,
                                            $locale,
                                            $fallbackLocale
                                        ) {
                                            $fallbackQuery
                                                ->whereDoesntHave(
                                                    'translations',
                                                    fn (Builder $currentQuery) =>
                                                    $currentQuery->where(
                                                        'locale',
                                                        $locale
                                                    )
                                                )
                                                ->whereHas(
                                                    'translations',
                                                    function (Builder $translationQuery) use (
                                                        $term,
                                                        $fallbackLocale
                                                    ) {
                                                        $translationQuery
                                                            ->where(
                                                                'locale',
                                                                $fallbackLocale
                                                            )
                                                            ->where(
                                                                'title',
                                                                'like',
                                                                "%{$term}%"
                                                            );
                                                    }
                                                );
                                        }
                                    );
                                }

                                /**
                                 * user.name.
                                 *
                                 * Frontend использует его
                                 * как fallback публичного имени.
                                 */
                                $nameQuery->orWhereHas(
                                    'user',
                                    fn (Builder $userQuery) =>
                                    $userQuery->where(
                                        'name',
                                        'like',
                                        "%{$term}%"
                                    )
                                );
                            }
                        );
                    }
                );
            }
        );
    }

    /* ======================== Admin/Common Sort ======================== */

    /**
     * Существующая сортировка.
     *
     * ВАЖНО:
     * используется Admin и поэтому
     * её контракт не изменяем.
     */
    public function scopeSortByParam(
        Builder $q,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale ??= app()->getLocale();

        return match ($sort) {
            'idAsc' =>
            $q->orderBy('id', 'asc'),

            'idDesc' =>
            $q->orderBy('id', 'desc'),

            'sortAsc' =>
            $q
                ->orderBy('sort', 'asc')
                ->orderByDesc('id'),

            'sortDesc' =>
            $q
                ->orderBy('sort', 'desc')
                ->orderByDesc('id'),

            'titleAsc' =>
            $q
                ->leftJoin(
                    'school_course_translations as sct_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sct_sort.school_course_id',
                                '=',
                                'school_courses.id'
                            )
                            ->where(
                                'sct_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->orderBy(
                    'sct_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                )
                ->select(
                    'school_courses.*'
                ),

            'titleDesc' =>
            $q
                ->leftJoin(
                    'school_course_translations as sct_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sct_sort.school_course_id',
                                '=',
                                'school_courses.id'
                            )
                            ->where(
                                'sct_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->orderBy(
                    'sct_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                )
                ->select(
                    'school_courses.*'
                ),

            'studentsCountAsc' =>
            $q
                ->orderBy('students_count', 'asc')
                ->orderByDesc('id'),

            'studentsCountDesc' =>
            $q
                ->orderBy('students_count', 'desc')
                ->orderByDesc('id'),

            'viewsAsc' =>
            $q
                ->orderBy('views', 'asc')
                ->orderByDesc('id'),

            'viewsDesc' =>
            $q
                ->orderBy('views', 'desc')
                ->orderByDesc('id'),

            'likesAsc' =>
            $q
                ->orderBy('likes', 'asc')
                ->orderByDesc('id'),

            'likesDesc' =>
            $q
                ->orderBy('likes', 'desc')
                ->orderByDesc('id'),

            'popularityAsc' =>
            $q
                ->orderBy('popularity', 'asc')
                ->orderByDesc('id'),

            'popularityDesc' =>
            $q
                ->orderBy('popularity', 'desc')
                ->orderByDesc('id'),

            'ratingCountAsc' =>
            $q
                ->orderBy('rating_count', 'asc')
                ->orderByDesc('id'),

            'ratingCountDesc' =>
            $q
                ->orderBy('rating_count', 'desc')
                ->orderByDesc('id'),

            'ratingAvgAsc' =>
            $q
                ->orderBy('rating_avg', 'asc')
                ->orderByDesc('id'),

            'ratingAvgDesc' =>
            $q
                ->orderBy('rating_avg', 'desc')
                ->orderByDesc('id'),

            'difficultyAsc' =>
            $q
                ->orderBy('difficulty', 'asc')
                ->orderByDesc('id'),

            'difficultyDesc' =>
            $q
                ->orderBy('difficulty', 'desc')
                ->orderByDesc('id'),

            'durationAsc' =>
            $q
                ->orderBy('duration', 'asc')
                ->orderByDesc('id'),

            'durationDesc' =>
            $q
                ->orderBy('duration', 'desc')
                ->orderByDesc('id'),

            'levelAsc' =>
            $q
                ->orderBy('level', 'asc')
                ->orderByDesc('id'),

            'levelDesc' =>
            $q
                ->orderBy('level', 'desc')
                ->orderByDesc('id'),

            'statusAsc' =>
            $q
                ->orderBy('status', 'asc')
                ->orderByDesc('id'),

            'statusDesc' =>
            $q
                ->orderBy('status', 'desc')
                ->orderByDesc('id'),

            'availabilityAsc' =>
            $q
                ->orderBy('availability', 'asc')
                ->orderByDesc('id'),

            'availabilityDesc' =>
            $q
                ->orderBy('availability', 'desc')
                ->orderByDesc('id'),

            'modulesAsc' =>
            $q
                ->withCount('modules')
                ->orderBy('modules_count', 'asc')
                ->orderByDesc('id'),

            'modulesDesc' =>
            $q
                ->withCount('modules')
                ->orderBy('modules_count', 'desc')
                ->orderByDesc('id'),

            'lessonsAsc' =>
            $q
                ->withCount('lessons')
                ->orderBy('lessons_count', 'asc')
                ->orderByDesc('id'),

            'lessonsDesc' =>
            $q
                ->withCount('lessons')
                ->orderBy('lessons_count', 'desc')
                ->orderByDesc('id'),

            'tracksAsc' =>
            $q
                ->withCount('tracks')
                ->orderBy('tracks_count', 'asc')
                ->orderByDesc('id'),

            'tracksDesc' =>
            $q
                ->withCount('tracks')
                ->orderBy('tracks_count', 'desc')
                ->orderByDesc('id'),

            'hashtagsAsc' =>
            $q
                ->withCount('hashtags')
                ->orderBy('hashtags_count', 'asc')
                ->orderByDesc('id'),

            'hashtagsDesc' =>
            $q
                ->withCount('hashtags')
                ->orderBy('hashtags_count', 'desc')
                ->orderByDesc('id'),

            'imagesAsc' =>
            $q
                ->withCount('images')
                ->orderBy('images_count', 'asc')
                ->orderByDesc('id'),

            'imagesDesc' =>
            $q
                ->withCount('images')
                ->orderBy('images_count', 'desc')
                ->orderByDesc('id'),

            'pricesAsc' =>
            $q
                ->withCount('prices')
                ->orderBy('prices_count', 'asc')
                ->orderByDesc('id'),

            'pricesDesc' =>
            $q
                ->withCount('prices')
                ->orderBy('prices_count', 'desc')
                ->orderByDesc('id'),

            'reviewsAsc' =>
            $q
                ->withCount('reviews')
                ->orderBy('reviews_count', 'asc')
                ->orderByDesc('id'),

            'reviewsDesc' =>
            $q
                ->withCount('reviews')
                ->orderBy('reviews_count', 'desc')
                ->orderByDesc('id'),

            'enrollmentsAsc' =>
            $q
                ->withCount('enrollments')
                ->orderBy('enrollments_count', 'asc')
                ->orderByDesc('id'),

            'enrollmentsDesc' =>
            $q
                ->withCount('enrollments')
                ->orderBy('enrollments_count', 'desc')
                ->orderByDesc('id'),

            'activityAsc' =>
            $q
                ->orderBy('activity', 'asc')
                ->orderByDesc('id'),

            'activityDesc' =>
            $q
                ->orderBy('activity', 'desc')
                ->orderByDesc('id'),

            'activity' =>
            $q
                ->where('activity', true)
                ->orderByDesc('id'),

            'inactive' =>
            $q
                ->where('activity', false)
                ->orderByDesc('id'),

            'isNewAsc' =>
            $q
                ->orderBy('is_new', 'asc')
                ->orderByDesc('id'),

            'isNewDesc' =>
            $q
                ->orderBy('is_new', 'desc')
                ->orderByDesc('id'),

            'isNew' =>
            $q
                ->where('is_new', true)
                ->orderByDesc('id'),

            'isHitAsc' =>
            $q
                ->orderBy('is_hit', 'asc')
                ->orderByDesc('id'),

            'isHitDesc' =>
            $q
                ->orderBy('is_hit', 'desc')
                ->orderByDesc('id'),

            'isHit' =>
            $q
                ->where('is_hit', true)
                ->orderByDesc('id'),

            'isSaleAsc' =>
            $q
                ->orderBy('is_sale', 'asc')
                ->orderByDesc('id'),

            'isSaleDesc' =>
            $q
                ->orderBy('is_sale', 'desc')
                ->orderByDesc('id'),

            'isSale' =>
            $q
                ->where('is_sale', true)
                ->orderByDesc('id'),

            'left' =>
            $q
                ->where('left', true)
                ->orderByDesc('id'),

            'noLeft' =>
            $q
                ->where('left', false)
                ->orderByDesc('id'),

            'main' =>
            $q
                ->where('main', true)
                ->orderByDesc('id'),

            'noMain' =>
            $q
                ->where('main', false)
                ->orderByDesc('id'),

            'right' =>
            $q
                ->where('right', true)
                ->orderByDesc('id'),

            'noRight' =>
            $q
                ->where('right', false)
                ->orderByDesc('id'),

            'publishedAtAsc',
            'dateAsc' =>
            $q
                ->orderBy('published_at', 'asc')
                ->orderByDesc('id'),

            'publishedAtDesc',
            'dateDesc' =>
            $q
                ->orderBy('published_at', 'desc')
                ->orderByDesc('id'),

            'createdAtAsc' =>
            $q
                ->orderBy('created_at', 'asc')
                ->orderByDesc('id'),

            'createdAtDesc' =>
            $q
                ->orderBy('created_at', 'desc')
                ->orderByDesc('id'),

            'updatedAtAsc' =>
            $q
                ->orderBy('updated_at', 'asc')
                ->orderByDesc('id'),

            'updatedAtDesc' =>
            $q
                ->orderBy('updated_at', 'desc')
                ->orderByDesc('id'),

            default =>
            $q->ordered(),
        };
    }

    /* ======================== Public Sort ======================== */

    /**
     * Public-сортировка курсов.
     *
     * Поддерживает только варианты,
     * реально доступные Public SchoolCourses Index.
     *
     * ВАЖНО:
     * Controller заранее формирует
     * необходимые *_count.
     *
     * Здесь повторный withCount()
     * не выполняется.
     */
    public function scopePublicSortByParam(
        Builder $q,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale ??= app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        return match ($sort) {
            'idAsc' =>
            $q->orderBy(
                'school_courses.id',
                'asc'
            ),

            'idDesc' =>
            $q->orderBy(
                'school_courses.id',
                'desc'
            ),

            'sortAsc' =>
            $q
                ->orderBy(
                    'school_courses.sort',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'sortDesc' =>
            $q
                ->orderBy(
                    'school_courses.sort',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            /**
             * Сортировка по тому же переводу,
             * который реально видит пользователь:
             *
             * current locale
             * → fallback locale.
             */
            'titleAsc' =>
            $this->applyPublicTitleSort(
                query: $q,
                locale: $locale,
                fallbackLocale: $fallbackLocale,
                direction: 'asc',
            ),

            'titleDesc' =>
            $this->applyPublicTitleSort(
                query: $q,
                locale: $locale,
                fallbackLocale: $fallbackLocale,
                direction: 'desc',
            ),

            'studentsCountAsc' =>
            $q
                ->orderBy(
                    'school_courses.students_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'studentsCountDesc' =>
            $q
                ->orderBy(
                    'school_courses.students_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'viewsAsc' =>
            $q
                ->orderBy(
                    'school_courses.views',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'viewsDesc' =>
            $q
                ->orderBy(
                    'school_courses.views',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            /**
             * Public frontend использует
             * именно likes_count,
             * а не denormalized поле likes.
             */
            'likesAsc' =>
            $q
                ->orderBy(
                    'likes_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'likesDesc' =>
            $q
                ->orderBy(
                    'likes_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'popularityAsc' =>
            $q
                ->orderBy(
                    'school_courses.popularity',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'popularityDesc' =>
            $q
                ->orderBy(
                    'school_courses.popularity',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'ratingAvgAsc' =>
            $q
                ->orderBy(
                    'school_courses.rating_avg',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'ratingAvgDesc' =>
            $q
                ->orderBy(
                    'school_courses.rating_avg',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'ratingCountAsc' =>
            $q
                ->orderBy(
                    'school_courses.rating_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'ratingCountDesc' =>
            $q
                ->orderBy(
                    'school_courses.rating_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'difficultyAsc' =>
            $q
                ->orderBy(
                    'school_courses.difficulty',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'difficultyDesc' =>
            $q
                ->orderBy(
                    'school_courses.difficulty',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'durationAsc' =>
            $q
                ->orderBy(
                    'school_courses.duration',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'durationDesc' =>
            $q
                ->orderBy(
                    'school_courses.duration',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'levelAsc' =>
            $q
                ->orderBy(
                    'school_courses.level',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'levelDesc' =>
            $q
                ->orderBy(
                    'school_courses.level',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            /**
             * Все counts ниже уже рассчитывает
             * Public Controller через withCount().
             */
            'modulesAsc' =>
            $q
                ->orderBy(
                    'modules_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'modulesDesc' =>
            $q
                ->orderBy(
                    'modules_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'lessonsAsc' =>
            $q
                ->orderBy(
                    'lessons_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'lessonsDesc' =>
            $q
                ->orderBy(
                    'lessons_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'tracksAsc' =>
            $q
                ->orderBy(
                    'tracks_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'tracksDesc' =>
            $q
                ->orderBy(
                    'tracks_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'hashtagsAsc' =>
            $q
                ->orderBy(
                    'hashtags_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'hashtagsDesc' =>
            $q
                ->orderBy(
                    'hashtags_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'reviewsAsc' =>
            $q
                ->orderBy(
                    'reviews_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'reviewsDesc' =>
            $q
                ->orderBy(
                    'reviews_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            /**
             * Единственная дата,
             * используемая Public Index.
             */
            'publishedAtAsc' =>
            $q
                ->orderBy(
                    'school_courses.published_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            'publishedAtDesc' =>
            $q
                ->orderBy(
                    'school_courses.published_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_courses.id'
                ),

            default =>
            $q->ordered(),
        };
    }

    /**
     * Public-сортировка по видимому title:
     *
     * current
     * → fallback.
     */
    protected function applyPublicTitleSort(
        Builder $query,
        string $locale,
        string $fallbackLocale,
        string $direction
    ): Builder {
        $direction =
            strtolower($direction) === 'desc'
                ? 'desc'
                : 'asc';

        /**
         * Если current и fallback совпадают,
         * второй JOIN не нужен.
         */
        if ($locale === $fallbackLocale) {
            return $query
                ->leftJoin(
                    'school_course_translations as sct_public_current',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sct_public_current.school_course_id',
                                '=',
                                'school_courses.id'
                            )
                            ->where(
                                'sct_public_current.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->orderBy(
                    'sct_public_current.title',
                    $direction
                )
                ->orderByDesc(
                    'school_courses.id'
                )
                ->select(
                    'school_courses.*'
                );
        }

        return $query
            ->leftJoin(
                'school_course_translations as sct_public_current',
                function ($join) use ($locale) {
                    $join
                        ->on(
                            'sct_public_current.school_course_id',
                            '=',
                            'school_courses.id'
                        )
                        ->where(
                            'sct_public_current.locale',
                            '=',
                            $locale
                        );
                }
            )
            ->leftJoin(
                'school_course_translations as sct_public_fallback',
                function ($join) use ($fallbackLocale) {
                    $join
                        ->on(
                            'sct_public_fallback.school_course_id',
                            '=',
                            'school_courses.id'
                        )
                        ->where(
                            'sct_public_fallback.locale',
                            '=',
                            $fallbackLocale
                        );
                }
            )
            ->orderByRaw(
                "COALESCE(
                    sct_public_current.title,
                    sct_public_fallback.title
                ) {$direction}"
            )
            ->orderByDesc(
                'school_courses.id'
            )
            ->select(
                'school_courses.*'
            );
    }

    /* ======================== Accessors ======================== */

    /** Главное изображение */
    public function getPrimaryImageAttribute(): ?SchoolCourseImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(
                    fn ($image) =>
                        $image->pivot->order
                        ?? PHP_INT_MAX
                )
                ->first();
        }

        return $this->images()
            ->orderBy(
                'school_course_has_images.order',
                'asc'
            )
            ->first();
    }
}
