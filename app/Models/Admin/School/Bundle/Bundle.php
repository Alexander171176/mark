<?php

namespace App\Models\Admin\School\Bundle;

use App\Models\Admin\Finance\BundlePrice\BundlePrice;
use App\Models\Admin\Finance\OrderItem\OrderItem;
use App\Models\Admin\School\Course\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bundle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bundles';

    protected $fillable = [
        'sort',
        'activity',

        'locale',
        'title',
        'slug',
        'subtitle',
        'short',
        'description',

        'published_at',

        'views',
        'likes',

        'meta_title',
        'meta_keywords',
        'meta_desc',

        'meta',
    ];

    protected $casts = [
        'activity'     => 'bool',
        'sort'         => 'int',

        'views'        => 'int',
        'likes'        => 'int',

        'meta'         => 'array',
        'published_at' => 'datetime',

        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Курсы внутри набора (M:N через bundle_has_course) */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class,
            'bundle_has_course',
            'bundle_id',
            'course_id'
        )->withTimestamps();
    }

    /** Изображения набора (M:N через bundle_has_images) */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            BundleImage::class,
            'bundle_has_images',
            'bundle_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('bundle_has_images.order', 'asc');
    }

    /** Цены набора */
    public function prices(): HasMany
    {
        return $this->hasMany(BundlePrice::class);
    }

    /** Позиции заказов, где встречается набор */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /* ======================== Scopes ======================== */

    /** sort ↑ затем id ↓ */
    public function scopeSorted(Builder $q): Builder
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
     * “Опубликованные” для витрины:
     * activity=true и published_at пустой или уже наступил
     */
    public function scopePublished(Builder $q): Builder
    {
        return $q
            ->where('activity', true)
            ->where(function (Builder $qq) {
                $qq->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /** Поиск по title/subtitle/short/description */
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

    /* ======================== Accessors ======================== */

    /** Главное изображение (минимальный order) */
    public function getPrimaryImageAttribute(): ?BundleImage
    {
        $relLoaded = $this->relationLoaded('images')
            ? $this->images
            : $this->images()->take(1)->get();

        return $relLoaded->first();
    }
}
