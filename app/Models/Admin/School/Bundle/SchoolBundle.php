<?php

namespace App\Models\Admin\School\Bundle;

use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\Order\SchoolOrderItem;
use App\Models\Admin\School\Price\SchoolBundlePrice;
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
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'sort_asc'   => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc'  => $q->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc'   => $q->orderBy('published_at', 'asc')->orderByDesc('id'),
            'date_desc'  => $q->orderBy('published_at', 'desc')->orderByDesc('id'),
            'views_asc'  => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'views_desc' => $q->orderBy('views', 'desc')->orderByDesc('id'),
            'likes_asc'  => $q->orderBy('likes', 'asc')->orderByDesc('id'),
            'likes_desc' => $q->orderBy('likes', 'desc')->orderByDesc('id'),
            default      => $q->sorted(),
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
