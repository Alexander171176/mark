<?php

namespace App\Models\Admin\School\SchoolModule;

use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\User\Like\SchoolModuleLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolModule extends Model
{
    use HasFactory;

    protected $table = 'school_modules';

    protected $fillable = [
        'school_course_id',
        'sort',
        'activity',
        'slug',
        'published_at',
        'status',
        'availability',
        'difficulty',
        'duration',
        'lessons_count',
        'popularity',
        'rating_count',
        'rating_avg',
        'views',
        'likes',
    ];

    protected $casts = [
        'school_course_id' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',
        'published_at' => 'datetime',
        'difficulty' => 'integer',
        'duration' => 'integer',
        'lessons_count' => 'integer',
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
        return $this->hasMany(SchoolModuleTranslation::class, 'school_module_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolModuleTranslation::class, 'school_module_id')
            ->where('locale', app()->getLocale());
    }

    /* ======================== Relations ======================== */

    /** Родительский курс */
    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    /** Уроки модуля */
    public function lessons(): HasMany
    {
        return $this->hasMany(SchoolLesson::class, 'school_module_id')
            ->orderBy('sort');
    }

    /** Изображения модуля */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolModuleImage::class,
            'school_module_has_images',
            'school_module_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('school_module_has_images.order', 'asc');
    }

    /** Лайки модуля */
    public function likes(): HasMany
    {
        return $this->hasMany(SchoolModuleLike::class, 'school_module_id');
    }

    /* ======================== Scopes ======================== */

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
    public function scopeSortByParam(Builder $q, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $q->orderBy('id', 'asc'),
            'idDesc' => $q->orderBy('id', 'desc'),

            'sortAsc' => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sortDesc' => $q->orderBy('sort', 'desc')->orderByDesc('id'),

            'courseAsc' => $q->orderBy('school_course_id', 'asc')->orderByDesc('id'),
            'courseDesc' => $q->orderBy('school_course_id', 'desc')->orderByDesc('id'),

            'titleAsc' => $q
                ->leftJoin('school_module_translations as smt_sort', function ($join) use ($locale) {
                    $join->on('smt_sort.school_module_id', '=', 'school_modules.id')
                        ->where('smt_sort.locale', '=', $locale);
                })
                ->orderBy('smt_sort.title', 'asc')
                ->orderByDesc('school_modules.id')
                ->select('school_modules.*'),

            'titleDesc' => $q
                ->leftJoin('school_module_translations as smt_sort', function ($join) use ($locale) {
                    $join->on('smt_sort.school_module_id', '=', 'school_modules.id')
                        ->where('smt_sort.locale', '=', $locale);
                })
                ->orderBy('smt_sort.title', 'desc')
                ->orderByDesc('school_modules.id')
                ->select('school_modules.*'),

            'statusAsc' => $q->orderBy('status', 'asc')->orderByDesc('id'),
            'statusDesc' => $q->orderBy('status', 'desc')->orderByDesc('id'),

            'availabilityAsc' => $q->orderBy('availability', 'asc')->orderByDesc('id'),
            'availabilityDesc' => $q->orderBy('availability', 'desc')->orderByDesc('id'),

            'difficultyAsc' => $q->orderBy('difficulty', 'asc')->orderByDesc('id'),
            'difficultyDesc' => $q->orderBy('difficulty', 'desc')->orderByDesc('id'),

            'durationAsc' => $q->orderBy('duration', 'asc')->orderByDesc('id'),
            'durationDesc' => $q->orderBy('duration', 'desc')->orderByDesc('id'),

            'lessonsAsc' => $q->withCount('lessons')->orderBy('lessons_count', 'asc')->orderByDesc('id'),
            'lessonsDesc' => $q->withCount('lessons')->orderBy('lessons_count', 'desc')->orderByDesc('id'),

            'imagesAsc' => $q->withCount('images')->orderBy('images_count', 'asc')->orderByDesc('id'),
            'imagesDesc' => $q->withCount('images')->orderBy('images_count', 'desc')->orderByDesc('id'),

            'likesAsc' => $q->orderBy('likes', 'asc')->orderByDesc('id'),
            'likesDesc' => $q->orderBy('likes', 'desc')->orderByDesc('id'),

            'likesCountAsc' => $q->withCount('likes')->orderBy('likes_count', 'asc')->orderByDesc('id'),
            'likesCountDesc' => $q->withCount('likes')->orderBy('likes_count', 'desc')->orderByDesc('id'),

            'viewsAsc' => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'viewsDesc' => $q->orderBy('views', 'desc')->orderByDesc('id'),

            'popularityAsc' => $q->orderBy('popularity', 'asc')->orderByDesc('id'),
            'popularityDesc' => $q->orderBy('popularity', 'desc')->orderByDesc('id'),

            'ratingCountAsc' => $q->orderBy('rating_count', 'asc')->orderByDesc('id'),
            'ratingCountDesc' => $q->orderBy('rating_count', 'desc')->orderByDesc('id'),

            'ratingAvgAsc' => $q->orderBy('rating_avg', 'asc')->orderByDesc('id'),
            'ratingAvgDesc' => $q->orderBy('rating_avg', 'desc')->orderByDesc('id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $q->where('activity', true)->orderByDesc('id'),
            'inactive' => $q->where('activity', false)->orderByDesc('id'),

            'publishedAtAsc' => $q->orderBy('published_at', 'asc')->orderByDesc('id'),
            'publishedAtDesc' => $q->orderBy('published_at', 'desc')->orderByDesc('id'),

            'createdAtAsc', 'dateAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc', 'dateDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('id'),

            default => $q->sorted(),
        };
    }

    /* ======================== Accessors ======================== */

    /** Главное изображение */
    public function getPrimaryImageAttribute(): ?SchoolModuleImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                ->first();
        }

        return $this->images()
            ->orderBy('school_module_has_images.order', 'asc')
            ->first();
    }
}
