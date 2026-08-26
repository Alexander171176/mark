<?php

namespace App\Models\Admin\School\SchoolBundle;

use App\Models\Admin\School\SchoolBundlePrice\SchoolBundlePrice;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolOrderItem\SchoolOrderItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolBundle extends Model
{
    use HasFactory;

    protected $table = 'school_bundles';

    protected $fillable = [
        'sort',
        'activity',
        'slug',
        'published_at',
        'views',
        'likes',
        'meta',
    ];

    protected $casts = [
        'sort' => 'integer',
        'activity' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer',
        'likes' => 'integer',
        'meta' => 'array',
    ];

    /* ======================== Translations ======================== */

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolBundleTranslation::class, 'school_bundle_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolBundleTranslation::class, 'school_bundle_id')
            ->where('locale', app()->getLocale());
    }

    /* ======================== Relations ======================== */

    /** Курсы внутри набора */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolCourse::class,
            'school_bundle_has_courses',
            'school_bundle_id',
            'school_course_id'
        )->withTimestamps();
    }

    /** Изображения набора */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolBundleImage::class,
            'school_bundle_has_images',
            'school_bundle_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('school_bundle_has_images.order', 'asc');
    }

    /** Цены набора */
    public function prices(): HasMany
    {
        return $this->hasMany(SchoolBundlePrice::class, 'school_bundle_id');
    }

    /** Позиции заказов */
    public function orderItems(): HasMany
    {
        return $this->hasMany(SchoolOrderItem::class, 'purchasable_id')
            ->where('purchasable_type', self::class);
    }

    /* ======================== Scopes ======================== */

    /** Сортировка */
    public function scopeSorted(Builder $q): Builder
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
            ->where('activity', true)
            ->where(function (Builder $qq) {
                $qq->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
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

            'titleAsc' => $q
                ->leftJoin('school_bundle_translations as sbt_sort', function ($join) use ($locale) {
                    $join->on('sbt_sort.school_bundle_id', '=', 'school_bundles.id')
                        ->where('sbt_sort.locale', '=', $locale);
                })
                ->orderBy('sbt_sort.title', 'asc')
                ->orderByDesc('school_bundles.id')
                ->addSelect('school_bundles.*'),

            'titleDesc' => $q
                ->leftJoin('school_bundle_translations as sbt_sort', function ($join) use ($locale) {
                    $join->on('sbt_sort.school_bundle_id', '=', 'school_bundles.id')
                        ->where('sbt_sort.locale', '=', $locale);
                })
                ->orderBy('sbt_sort.title', 'desc')
                ->orderByDesc('school_bundles.id')
                ->addSelect('school_bundles.*'),

            'slugAsc' => $q->orderBy('slug', 'asc')->orderByDesc('id'),
            'slugDesc' => $q->orderBy('slug', 'desc')->orderByDesc('id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $q->where('activity', true)->orderByDesc('id'),
            'inactive' => $q->where('activity', false)->orderByDesc('id'),

            'viewsAsc' => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'viewsDesc' => $q->orderBy('views', 'desc')->orderByDesc('id'),

            'likesAsc' => $q->orderBy('likes', 'asc')->orderByDesc('id'),
            'likesDesc' => $q->orderBy('likes', 'desc')->orderByDesc('id'),

            'coursesAsc' => $q->withCount('courses')->orderBy('courses_count', 'asc')->orderByDesc('id'),
            'coursesDesc' => $q->withCount('courses')->orderBy('courses_count', 'desc')->orderByDesc('id'),

            'imagesAsc' => $q->withCount('images')->orderBy('images_count', 'asc')->orderByDesc('id'),
            'imagesDesc' => $q->withCount('images')->orderBy('images_count', 'desc')->orderByDesc('id'),

            'pricesAsc' => $q->withCount('prices')->orderBy('prices_count', 'asc')->orderByDesc('id'),
            'pricesDesc' => $q->withCount('prices')->orderBy('prices_count', 'desc')->orderByDesc('id'),

            'orderItemsAsc' => $q->withCount('orderItems')->orderBy('order_items_count', 'asc')->orderByDesc('id'),
            'orderItemsDesc' => $q->withCount('orderItems')->orderBy('order_items_count', 'desc')->orderByDesc('id'),

            'publishedAtAsc', 'dateAsc' => $q->orderBy('published_at', 'asc')->orderByDesc('id'),
            'publishedAtDesc', 'dateDesc' => $q->orderBy('published_at', 'desc')->orderByDesc('id'),

            'createdAtAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('id'),

            default => $q->sorted(),
        };
    }

    /* ======================== Accessors ======================== */

    /** Главное изображение */
    public function getPrimaryImageAttribute(): ?SchoolBundleImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                ->first();
        }

        return $this->images()
            ->orderBy('school_bundle_has_images.order', 'asc')
            ->first();
    }
}
