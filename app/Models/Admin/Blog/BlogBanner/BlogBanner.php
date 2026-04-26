<?php

namespace App\Models\Admin\Blog\BlogBanner;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class BlogBanner extends Model
{
    use HasFactory;

    protected $table = 'blog_banners';

    protected $fillable = [
        'user_id',

        'sort',
        'activity',
        'left',
        'main',
        'right',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'comment',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'user_id' => 'integer',

        'sort' => 'integer',
        'activity' => 'boolean',
        'left' => 'boolean',
        'main' => 'boolean',
        'right' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',
        'moderated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Владелец баннера */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Модератор баннера */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Переводы баннера */
    public function translations(): HasMany
    {
        return $this->hasMany(BlogBannerTranslation::class, 'banner_id');
    }

    /** Текущий перевод по locale */
    public function translation(?string $locale = null): ?BlogBannerTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first();
    }

    /** Перевод с fallback */
    public function translationOrFallback(?string $locale = null, string $fallback = 'ru'): ?BlogBannerTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }

    /** Изображения баннера */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogBannerImage::class,
            'blog_banner_has_images',
            'banner_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /* ======================== HELPERS ======================== */

    /** Активен ли баннер */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Одобрен ли баннер */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Если в левом блоке */
    public function isLeft(): bool
    {
        return (bool) $this->left;
    }

    /** Если в главном блоке */
    public function isMain(): bool
    {
        return (bool) $this->main;
    }

    /** Если в правом блоке */
    public function isRight(): bool
    {
        return (bool) $this->right;
    }

    /** Получить title из текущего перевода */
    public function getTranslatedTitle(?string $locale = null, string $fallback = 'ru'): ?string
    {
        return $this->translationOrFallback($locale, $fallback)?->title;
    }

    /* ======================== Scopes ======================== */

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc');
    }

    /** Только активные */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('activity', true);
    }

    /** Только одобренные */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 1);
    }

    /** Левый блок */
    public function scopeLeft(Builder $query): Builder
    {
        return $query->where('left', true);
    }

    /** Главный блок */
    public function scopeMain(Builder $query): Builder
    {
        return $query->where('main', true);
    }

    /** Правый блок */
    public function scopeRight(Builder $query): Builder
    {
        return $query->where('right', true);
    }

    /** Позиция: left|main|right|any */
    public function scopeWherePosition(Builder $query, string $position): Builder
    {
        return match ($position) {
            'left'  => $query->left(),
            'main'  => $query->main(),
            'right' => $query->right(),
            default => $query,
        };
    }

    /** Публичный набор */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->active();
    }

    /** С картинками */
    public function scopeWithImages(Builder $query): Builder
    {
        return $query->with('images');
    }

    /** Готовый набор для фронта */
    public function scopeForFrontend(Builder $query): Builder
    {
        return $query
            ->forPublic()
            ->withImages()
            ->ordered();
    }

    /** Поиск по переводимым полям */
    public function scopeSearch(Builder $query, ?string $term = null, ?string $locale = null): Builder
    {
        if (!$term) {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();
        $term = trim($term);

        return $query->where(function (Builder $q) use ($term, $locale) {
            $q->where('comment', 'like', "%{$term}%")
                ->orWhereHas('translations', function (Builder $tq) use ($term, $locale) {
                    $tq->where('locale', $locale)
                        ->where(function (Builder $sq) use ($term) {
                            $sq->where('title', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('link', 'like', "%{$term}%");
                        });
                });
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $query, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'sort_asc'   => $query->orderBy('sort', 'asc')->orderBy('id', 'asc'),
            'sort_desc'  => $query->orderBy('sort', 'desc')->orderBy('id', 'desc'),
            'date_asc'   => $query->orderBy('created_at', 'asc')->orderByDesc('id'),
            'date_desc'  => $query->orderBy('created_at', 'desc')->orderByDesc('id'),

            'title_asc' => $query
                ->leftJoin('blog_banner_translations as bbt_sort', function ($join) use ($locale) {
                    $join->on('bbt_sort.banner_id', '=', 'blog_banners.id')
                        ->where('bbt_sort.locale', '=', $locale);
                })
                ->orderBy('bbt_sort.title', 'asc')
                ->orderBy('blog_banners.id', 'asc')
                ->select('blog_banners.*'),

            'title_desc' => $query
                ->leftJoin('blog_banner_translations as bbt_sort', function ($join) use ($locale) {
                    $join->on('bbt_sort.banner_id', '=', 'blog_banners.id')
                        ->where('bbt_sort.locale', '=', $locale);
                })
                ->orderBy('bbt_sort.title', 'desc')
                ->orderByDesc('blog_banners.id')
                ->select('blog_banners.*'),

            default => $query->ordered(),
        };
    }

    /* ======================== MODEL EVENTS ======================== */

    protected static function booted(): void
    {
        static::saved(function (BlogBanner $banner) {
            Log::info('Banner saved: ' . $banner->id);
        });

        static::deleted(function (BlogBanner $banner) {
            Log::info('Banner deleted: ' . $banner->id);
        });
    }
}
