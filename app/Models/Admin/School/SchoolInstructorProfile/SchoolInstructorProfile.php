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
    public function scopeForPublic(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q
            ->active()
            ->whereHas('translations', fn ($qq) => $qq->where('locale', $locale))
            ->withLocale($locale);
    }

    /** По рейтингу */
    public function scopeWithGoodRating(Builder $q, float $min = 4.5, int $minCount = 10): Builder
    {
        return $q
            ->where('rating_avg', '>=', $min)
            ->where('rating_count', '>=', $minCount);
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
                                ->orWhere('bio', 'like', "%{$term}%");
                        });
                });
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

            'slugAsc' => $q->orderBy('slug', 'asc')->orderByDesc('id'),
            'slugDesc' => $q->orderBy('slug', 'desc')->orderByDesc('id'),

            'titleAsc' => $q
                ->leftJoin('school_instructor_profile_translations as sipt_sort', function ($join) use ($locale) {
                    $join->on('sipt_sort.school_instructor_profile_id', '=', 'school_instructor_profiles.id')
                        ->where('sipt_sort.locale', '=', $locale);
                })
                ->orderBy('sipt_sort.title', 'asc')
                ->orderByDesc('school_instructor_profiles.id')
                ->select('school_instructor_profiles.*'),

            'titleDesc' => $q
                ->leftJoin('school_instructor_profile_translations as sipt_sort', function ($join) use ($locale) {
                    $join->on('sipt_sort.school_instructor_profile_id', '=', 'school_instructor_profiles.id')
                        ->where('sipt_sort.locale', '=', $locale);
                })
                ->orderBy('sipt_sort.title', 'desc')
                ->orderByDesc('school_instructor_profiles.id')
                ->select('school_instructor_profiles.*'),

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
        $title = $this->relationLoaded('translation')
            ? $this->translation?->title
            : $this->translation()->value('title');

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
