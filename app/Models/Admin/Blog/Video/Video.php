<?php

namespace App\Models\Admin\Blog\Video;

use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Comment\Comment;
use App\Models\User;
use App\Models\User\Like\VideoLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Video extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'videos';

    protected $fillable = [
        'user_id',

        'sort',

        'activity',
        'is_private',

        'left',
        'main',
        'right',

        'locale',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'title',
        'url',
        'short',
        'description',

        'pseudonym',

        'published_at',
        'show_from_at',
        'show_to_at',

        'duration',
        'source_type',
        'embed_code',
        'external_video_id',

        'views',

        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    protected $casts = [
        'user_id' => 'integer',

        'sort'       => 'integer',
        'activity'   => 'boolean',
        'is_private' => 'boolean',
        'left'       => 'boolean',
        'main'       => 'boolean',
        'right'      => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by'      => 'integer',
        'moderated_at'      => 'datetime',

        'published_at' => 'date',
        'show_from_at' => 'datetime',
        'show_to_at'   => 'datetime',

        'duration' => 'integer',
        'views'    => 'integer',
    ];

    /* -------------------------------------------------
     | MEDIA (локальный файл видео)
     |------------------------------------------------- */

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('videos')->singleFile();
    }

    /* ======================== Relations ======================== */

    /** Владелец видео */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** модератор видео */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Видео используется в статьях. */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            Article::class,
            'article_has_video',
            'video_id',
            'article_id'
        )
            ->withPivot('sort')
            ->orderBy('article_has_video.sort', 'desc');
    }

    /** Превью изображения видео (pivot.order) */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            VideoImage::class,
            'video_has_images',
            'video_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /** Рекомендованные видео (pivot.sort) */
    public function relatedVideos(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'video_related',
            'video_id',
            'related_video_id'
        )
            ->withPivot('sort')
            ->orderBy('video_related.sort', 'asc');
    }

    /** Комментарии */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /** Лайки */
    public function likes(): HasMany
    {
        return $this->hasMany(VideoLike::class, 'video_id');
    }

    /* -------------------------------------------------
     | ACCESSORS
     |------------------------------------------------- */

    public function getIsPublicAttribute(): bool
    {
        return !$this->is_private;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->source_type === 'local'
            ? $this->getFirstMediaUrl('videos')
            : null;
    }

    public function getEmbedUrlAttribute(): ?string
    {
        // youtube
        if ($this->source_type === 'youtube' && $this->external_video_id) {
            if (preg_match('/(?:v=|youtu\.be\/)([^&\s]+)/', $this->external_video_id, $m)) {
                return "https://www.youtube.com/embed/{$m[1]}";
            }
        }

        // vimeo
        if ($this->source_type === 'vimeo' && $this->external_video_id) {
            if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $this->external_video_id, $m)) {
                return "https://player.vimeo.com/video/{$m[1]}";
            }
        }

        return null;
    }

    /* ======================== Scopes ======================== */

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

    /**
     * Видимые по окну показа:
     * - show_from_at NULL или <= now
     * - show_to_at NULL или >= now
     */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull('show_from_at')
                    ->orWhere('show_from_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('show_to_at')
                    ->orWhere('show_to_at', '>=', now());
            });
    }

    /** Только приватные */
    public function scopePublicOnly(Builder $query): Builder
    {
        return $query->where('is_private', false);
    }

    public function scopePrivateOnly(Builder $query): Builder
    {
        return $query->where('is_private', true);
    }

    /** Только видимые */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->publicOnly()
            ->approved()
            ->active()
            ->inShowWindow();
    }

    /** Публичный скоуп: approved + active + locale + окно показа */
    public function scopeForPublic(Builder $query, ?string $locale = null): Builder
    {
        return $query
            ->publicOnly() // ← добавили
            ->approved()
            ->active()
            ->inShowWindow()
            ->byLocale($locale);
    }

    /** Поиск по заголовку/подзаголовку/описанию */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('short', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('url', 'like', "%{$term}%")
                ->orWhere('pseudonym', 'like', "%{$term}%")
                ->orWhere('external_video_id', 'like', "%{$term}%");
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'sort_asc'      => $query->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc'     => $query->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc'      => $query->orderBy('created_at', 'asc')->orderByDesc('id'),
            'date_desc'     => $query->orderBy('created_at', 'desc')->orderByDesc('id'),
            'views_asc'     => $query->orderBy('views', 'asc')->orderByDesc('id'),
            'views_desc'    => $query->orderBy('views', 'desc')->orderByDesc('id'),
            'likes_asc'  => $query->withCount('likes')->orderBy('likes_count', 'asc')->orderByDesc('id'),
            'likes_desc' => $query->withCount('likes')->orderBy('likes_count', 'desc')->orderByDesc('id'),
            'duration_asc'  => $query->orderBy('duration', 'asc')->orderByDesc('id'),
            'duration_desc' => $query->orderBy('duration', 'desc')->orderByDesc('id'),
            'title_asc'     => $query->orderBy('title', 'asc')->orderByDesc('id'),
            'title_desc'    => $query->orderBy('title', 'desc')->orderByDesc('id'),
            default         => $query->orderBy('sort', 'asc')->orderByDesc('id'),
        };
    }

    /* ======================== MODEL EVENTS ======================== */
    protected static function booted(): void
    {
        static::saved(fn (Video $video) => Log::info('Video saved: '.$video->id));
        static::deleted(fn (Video $video) => Log::info('Video deleted: '.$video->id));
    }
}
