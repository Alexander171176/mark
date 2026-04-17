<?php

namespace App\Models\Admin\Blog\Banner;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banners';

    protected $fillable = [
        'user_id',

        'sort',
        'activity',
        'left',
        'main',
        'right',

        'locale',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'title',
        'link',
        'short',
        'comment',
    ];

    protected $casts = [
        'user_id' => 'integer',

        'sort'     => 'integer',
        'activity' => 'boolean',
        'left'     => 'boolean',
        'main'     => 'boolean',
        'right'    => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by'      => 'integer',
        'moderated_at'      => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Владелец статьи */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** модератор статьи */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Изображения баннера (pivot.order) */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            BannerImage::class,
            'banner_has_images',
            'banner_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /* ======================== HELPERS ======================== */

    /** Если активные */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Если в левой колонке */
    public function isLeft(): bool { return (bool) $this->left; }

    /** Если в главном */
    public function isMain(): bool { return (bool) $this->main; }

    /** Если в правой колонке */
    public function isRight(): bool { return (bool) $this->right; }

    /* ======================== Scopes ======================== */

    /** Сортировка: по sort ↑ затем по id ↓ */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc');
    }

    /** По локали */
    public function scopeByLocale(Builder $query, ?string $locale = null): Builder
    {
        return $query->where('locale', $locale ?? app()->getLocale());
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

    /** Избранные (для левой колонки) — теперь по флагу left */
    public function scopeLeft(Builder $query): Builder
    {
        return $query->where('left', true);
    }

    /** Избранные (для главного окна) — теперь по флагу main */
    public function scopeMain(Builder $query): Builder
    {
        return $query->where('main', true);
    }

    /** Избранные (для правой колонки) — теперь по флагу right */
    public function scopeRight(Builder $query): Builder
    {
        return $query->where('right', true);
    }

    /** position: left|main|right|any */
    public function scopeWherePosition(Builder $query, string $position): Builder
    {
        return match ($position) {
            'left'  => $query->left(),
            'main'  => $query->main(),
            'right' => $query->right(),
            default => $query,
        };
    }

    /** Публичный набор: approved + active + locale */
    public function scopeForPublic(Builder $query, ?string $locale = null): Builder
    {
        return $query
            ->approved()
            ->active()
            ->byLocale($locale);
    }

    /** Изображения */
    public function scopeWithImages(Builder $query): Builder
    {
        return $query->with('images');
    }

    /**
     * Готовый набор для фронта: публичные + картинки + сортировка
     */
    public function scopeForFrontend(Builder $query, ?string $locale = null): Builder
    {
        return $query
            ->forPublic($locale)
            ->withImages()
            ->ordered();
    }

    /* ======================== MODEL EVENTS ======================== */
    protected static function booted(): void
    {
        static::saved(fn (Banner $banner) => Log::info('Banner saved: '.$banner->id));
        static::deleted(fn (Banner $banner) => Log::info('Banner deleted: '.$banner->id));
    }
}
