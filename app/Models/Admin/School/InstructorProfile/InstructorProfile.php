<?php

namespace App\Models\Admin\School\InstructorProfile;

use App\Models\Admin\Finance\Payout\Payout;
use App\Models\Admin\School\Course\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstructorProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'instructor_profiles';

    protected $fillable = [
        'sort',
        'user_id',
        'locale',
        'title',
        'short',
        'bio',
        'slug',
        'rating_avg',
        'rating_count',
        'activity',
        'hourly_rate',
        'experience_years',
        'social_links',
        'views',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    protected $casts = [
        'sort' => 'integer',
        'activity' => 'bool',
        'rating_avg' => 'float',
        'rating_count' => 'int',
        'hourly_rate' => 'decimal:2',
        'experience_years' => 'int',
        'social_links' => 'array',
        'views' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /* ================= Relations ================ */

    /** Инструктор */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Курсы */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_profile_id');
    }

    /** Выплаты */
    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class, 'instructor_profile_id');
    }

    /** Изображения Инструктора (many-to-many) + сортировка по pivot.order */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            InstructorProfileImage::class,
            'instructor_profile_has_images',
            'instructor_profile_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('instructor_profile_has_images.order', 'asc');
    }

    /* ================= Scopes ================ */

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

    /** По рейтингу */
    public function scopeWithGoodRating(Builder $q, float $min = 4.5, int $minCount = 10): Builder
    {
        return $q->where('rating_avg', '>=', $min)
            ->where('rating_count', '>=', $minCount);
    }

    /** Сортировка: по sort ↑ затем по id ↓ */
    public function scopeSorted(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /** Поиск по заголовку/подзаголовку/описанию */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) {
            return $q;
        }

        return $q->where(function (Builder $qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
                ->orWhere('short', 'like', "%{$term}%")
                ->orWhere('bio', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%");
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
            'rating_asc'      => $q->orderBy('rating_avg', 'asc')->orderByDesc('id'),
            'rating_desc'     => $q->orderBy('rating_avg', 'desc')->orderByDesc('id'),
            'experience_asc'  => $q->orderBy('experience_years', 'asc')->orderByDesc('id'),
            'experience_desc' => $q->orderBy('experience_years', 'desc')->orderByDesc('id'),
            'title_asc'       => $q->orderBy('title', 'asc')->orderByDesc('id'),
            'title_desc'      => $q->orderBy('title', 'desc')->orderByDesc('id'),
            default           => $q->sorted(),
        };
    }

    /* ================= Accessors ================ */

    /** Атрибут имени пользователя */
    public function getPublicNameAttribute(): string
    {
        return $this->title ?: ($this->user->name ?? 'Инструктор');
    }

    /** Либо одно изображение инструктора, либо массив */
    public function getPrimaryImageAttribute(): ?InstructorProfileImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                ->first();
        }

        return $this->images()
            ->orderBy('instructor_profile_has_images.order', 'asc')
            ->first();
    }
}
