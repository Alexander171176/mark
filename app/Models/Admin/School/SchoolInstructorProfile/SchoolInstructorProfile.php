<?php

namespace App\Models\Admin\School\SchoolInstructorProfile;

use App\Models\Admin\School\SchoolPayout\SchoolPayout;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolInstructorProfile extends Model
{
    use HasFactory;

    protected $table = 'school_instructor_profiles';

    protected $fillable = [
        'sort',
        'activity',
        'user_id',
        'slug',
        'experience_years',
        'hourly_rate',
        'rating_count',
        'rating_avg',
        'views',
        'social_links',
    ];

    protected $casts = [
        'sort' => 'integer',
        'activity' => 'boolean',
        'experience_years' => 'integer',
        'hourly_rate' => 'decimal:2',
        'rating_count' => 'integer',
        'rating_avg' => 'float',
        'views' => 'integer',
        'social_links' => 'array',
    ];

    /* ======================== Translations ======================== */

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolInstructorProfileTranslation::class, 'school_instructor_profile_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolInstructorProfileTranslation::class, 'school_instructor_profile_id')
            ->where('locale', app()->getLocale());
    }

    /* ======================== Relations ======================== */

    /** Пользователь-инструктор */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Курсы инструктора */
    public function courses(): HasMany
    {
        return $this->hasMany(SchoolCourse::class, 'school_instructor_profile_id');
    }

    /** Выплаты инструктору */
    public function payouts(): HasMany
    {
        return $this->hasMany(SchoolPayout::class, 'school_instructor_profile_id');
    }

    /** Изображения инструктора */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolInstructorProfileImage::class,
            'school_instructor_profile_has_images',
            'school_instructor_profile_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('school_instructor_profile_has_images.order', 'asc');
    }

    /* ======================== Scopes ======================== */

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Сортировка */
    public function scopeSorted(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
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
    public function scopeForPublic(
        Builder $query,
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): Builder {
        $locale ??= app()->getLocale();

        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        return $query
            ->where(
                'activity',
                true
            )
            ->whereHas(
                'translations',
                fn (Builder $translationQuery) =>
                $translationQuery->whereIn(
                    'locale',
                    $locales
                )
            );
    }

    /** По рейтингу */
    public function scopeWithGoodRating(Builder $q, float $min = 4.5, int $minCount = 10): Builder
    {
        return $q
            ->where('rating_avg', '>=', $min)
            ->where('rating_count', '>=', $minCount);
    }

    /** Поиск */
    public function scopeSearch(
        Builder $q,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim(
            (string) $term
        );

        if ($term === '') {
            return $q;
        }

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

        return $q->where(
            function (Builder $query) use (
                $term,
                $locales
            ) {
                /**
                 * Основная таблица.
                 */
                $query
                    ->where(
                        'school_instructor_profiles.slug',
                        'like',
                        "%{$term}%"
                    )

                    /**
                     * Current locale
                     * + fallback locale.
                     */
                    ->orWhereHas(
                        'translations',
                        function (Builder $translationQuery) use (
                            $term,
                            $locales
                        ) {
                            $translationQuery
                                ->whereIn(
                                    'locale',
                                    $locales
                                )
                                ->where(
                                    function (Builder $subQuery) use ($term) {
                                        $subQuery
                                            ->where(
                                                'title',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'short',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'bio',
                                                'like',
                                                "%{$term}%"
                                            );
                                    }
                                );
                        }
                    )

                    /**
                     * Пользователь.
                     */
                    ->orWhereHas(
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

    /** Сортировка по параметру */
    public function scopeSortByParam(
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
            'idAsc' => $q->orderBy('id', 'asc'),
            'idDesc' => $q->orderBy('id', 'desc'),

            'sortAsc' => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sortDesc' => $q->orderBy('sort', 'desc')->orderByDesc('id'),

            'slugAsc' => $q->orderBy('slug', 'asc')->orderByDesc('id'),
            'slugDesc' => $q->orderBy('slug', 'desc')->orderByDesc('id'),

            'titleAsc' => $q
                /**
                 * Перевод текущей локали.
                 */
                ->leftJoin(
                    'school_instructor_profile_translations as sipt_current',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sipt_current.school_instructor_profile_id',
                                '=',
                                'school_instructor_profiles.id'
                            )
                            ->where(
                                'sipt_current.locale',
                                '=',
                                $locale
                            );
                    }
                )

                /**
                 * Fallback-перевод.
                 */
                ->leftJoin(
                    'school_instructor_profile_translations as sipt_fallback',
                    function ($join) use ($fallbackLocale) {
                        $join
                            ->on(
                                'sipt_fallback.school_instructor_profile_id',
                                '=',
                                'school_instructor_profiles.id'
                            )
                            ->where(
                                'sipt_fallback.locale',
                                '=',
                                $fallbackLocale
                            );
                    }
                )

                /**
                 * Приоритет:
                 *
                 * current locale
                 * → fallback locale.
                 */
                ->orderByRaw(
                    'COALESCE(sipt_current.title, sipt_fallback.title) ASC'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                )
                ->addSelect(
                    'school_instructor_profiles.*'
                ),


            'titleDesc' => $q
                /**
                 * Перевод текущей локали.
                 */
                ->leftJoin(
                    'school_instructor_profile_translations as sipt_current',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sipt_current.school_instructor_profile_id',
                                '=',
                                'school_instructor_profiles.id'
                            )
                            ->where(
                                'sipt_current.locale',
                                '=',
                                $locale
                            );
                    }
                )

                /**
                 * Fallback-перевод.
                 */
                ->leftJoin(
                    'school_instructor_profile_translations as sipt_fallback',
                    function ($join) use ($fallbackLocale) {
                        $join
                            ->on(
                                'sipt_fallback.school_instructor_profile_id',
                                '=',
                                'school_instructor_profiles.id'
                            )
                            ->where(
                                'sipt_fallback.locale',
                                '=',
                                $fallbackLocale
                            );
                    }
                )

                /**
                 * Приоритет:
                 *
                 * current locale
                 * → fallback locale.
                 */
                ->orderByRaw(
                    'COALESCE(sipt_current.title, sipt_fallback.title) DESC'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                )
                ->addSelect(
                    'school_instructor_profiles.*'
                ),

            'viewsAsc' => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'viewsDesc' => $q->orderBy('views', 'desc')->orderByDesc('id'),

            'ratingAvgAsc' => $q->orderBy('rating_avg', 'asc')->orderByDesc('id'),
            'ratingAvgDesc' => $q->orderBy('rating_avg', 'desc')->orderByDesc('id'),

            'ratingCountAsc' => $q->orderBy('rating_count', 'asc')->orderByDesc('id'),
            'ratingCountDesc' => $q->orderBy('rating_count', 'desc')->orderByDesc('id'),

            'hourlyRateAsc' => $q->orderBy('hourly_rate', 'asc')->orderByDesc('id'),
            'hourlyRateDesc' => $q->orderBy('hourly_rate', 'desc')->orderByDesc('id'),

            'experienceAsc' => $q->orderBy('experience_years', 'asc')->orderByDesc('id'),
            'experienceDesc' => $q->orderBy('experience_years', 'desc')->orderByDesc('id'),

            'coursesAsc' => $q->withCount('courses')
                ->orderBy('courses_count', 'asc')->orderByDesc('id'),
            'coursesDesc' => $q->withCount('courses')
                ->orderBy('courses_count', 'desc')->orderByDesc('id'),

            'payoutsAsc' => $q->withCount('payouts')
                ->orderBy('payouts_count', 'asc')->orderByDesc('id'),
            'payoutsDesc' => $q->withCount('payouts')
                ->orderBy('payouts_count', 'desc')->orderByDesc('id'),

            'imagesAsc' => $q->withCount('images')
                ->orderBy('images_count', 'asc')->orderByDesc('id'),
            'imagesDesc' => $q->withCount('images')
                ->orderBy('images_count', 'desc')->orderByDesc('id'),

            'createdAtAsc', 'dateAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc', 'dateDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $q->where('activity', true)->orderByDesc('id'),
            'inactive' => $q->where('activity', false)->orderByDesc('id'),

            default => $q->sorted(),
        };
    }

    /* ======================== Accessors ======================== */

    /** Публичное имя */
    public function getPublicNameAttribute(): string
    {
        $title = null;

        if ($this->relationLoaded('translations')) {
            $title = $this->translations->first()?->title;
        } elseif ($this->relationLoaded('translation')) {
            $title = $this->translation?->title;
        } else {
            $title = $this->translation()->value('title');
        }

        return $title ?: ($this->user->name ?? 'Инструктор');
    }

    /** Главное изображение */
    public function getPrimaryImageAttribute(): ?SchoolInstructorProfileImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                ->first();
        }

        return $this->images()
            ->orderBy('school_instructor_profile_has_images.order', 'asc')
            ->first();
    }
}
