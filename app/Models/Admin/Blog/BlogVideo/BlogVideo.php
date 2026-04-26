<?php

namespace App\Models\Admin\Blog\BlogVideo;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\Comment\Comment;
use App\Models\User;
use App\Models\User\Like\BlogVideoLike;
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

class BlogVideo extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'blog_videos';

    protected $fillable = [
        'user_id',

        'sort',

        'activity',
        'is_private',

        'left',
        'main',
        'right',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'url',

        'published_at',
        'show_from_at',
        'show_to_at',

        'duration',
        'source_type',
        'embed_code',
        'external_video_id',

        'views',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'user_id' => 'integer',

        'sort' => 'integer',
        'activity' => 'boolean',
        'is_private' => 'boolean',
        'left' => 'boolean',
        'main' => 'boolean',
        'right' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',
        'moderated_at' => 'datetime',

        'published_at' => 'date',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',

        'duration' => 'integer',
        'views' => 'integer',
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

    /** Модератор видео */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Переводы видео */
    public function translations(): HasMany
    {
        return $this->hasMany(BlogVideoTranslation::class, 'video_id');
    }

    /** Текущий перевод по locale */
    public function translation(?string $locale = null): ?BlogVideoTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first();
    }

    /** Перевод с fallback */
    public function translationOrFallback(?string $locale = null, string $fallback = 'ru'): ?BlogVideoTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }

    /** Видео используется в статьях */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogArticle::class,
            'blog_article_has_video',
            'video_id',
            'article_id'
        )
            ->withPivot('sort')
            ->orderByPivot('sort');
    }

    /** Превью изображения видео */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogVideoImage::class,
            'blog_video_has_images',
            'video_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /** Рекомендованные видео */
    public function relatedVideos(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'blog_video_related',
            'video_id',
            'related_video_id'
        )
            ->withPivot('sort')
            ->orderByPivot('sort');
    }

    /** Где это видео используется как рекомендованное */
    public function usedInRelatedVideos(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'blog_video_related',
            'related_video_id',
            'video_id'
        )
            ->withPivot('sort');
    }

    /** Комментарии */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /** Лайки */
    public function likes(): HasMany
    {
        return $this->hasMany(BlogVideoLike::class, 'video_id');
    }

    /** Пользователи, лайкнувшие видео */
    public function likedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'blog_video_likes',
            'video_id',
            'user_id'
        )->withTimestamps();
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

    public function getTranslatedTitle(?string $locale = null, string $fallback = 'ru'): ?string
    {
        return $this->translationOrFallback($locale, $fallback)?->title;
    }

    /* ======================== Helpers ======================== */

    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    public function isPublishedNow(): bool
    {
        $now = now();

        if ($this->show_from_at && $now->lt($this->show_from_at)) {
            return false;
        }

        if ($this->show_to_at && $now->gt($this->show_to_at)) {
            return false;
        }

        return true;
    }

    /* ======================== Scopes ======================== */

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

    /** Только публичные */
    public function scopePublicOnly(Builder $query): Builder
    {
        return $query->where('is_private', false);
    }

    /** Только приватные */
    public function scopePrivateOnly(Builder $query): Builder
    {
        return $query->where('is_private', true);
    }

    /** Видимые */
    public function scopeVisible(Builder $query): Builder
    {
        return $query
            ->publicOnly()
            ->approved()
            ->active()
            ->inShowWindow();
    }

    /** Публичный набор */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->publicOnly()
            ->approved()
            ->active()
            ->inShowWindow();
    }

    /** Левый блок */
    public function scopeInLeft(Builder $query): Builder
    {
        return $query->where('left', true);
    }

    /** Главный блок */
    public function scopeInMain(Builder $query): Builder
    {
        return $query->where('main', true);
    }

    /** Правый блок */
    public function scopeInRight(Builder $query): Builder
    {
        return $query->where('right', true);
    }

    /** Поиск по переводимым полям и служебным полям */
    public function scopeSearch(Builder $query, ?string $term, ?string $locale = null): Builder
    {
        if (!$term) {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();

        return $query->where(function (Builder $q) use ($term, $locale) {
            $q->where('url', 'like', "%{$term}%")
                ->orWhere('external_video_id', 'like', "%{$term}%")
                ->orWhereHas('translations', function (Builder $tq) use ($term, $locale) {
                    $tq->where('locale', $locale)
                        ->where(function (Builder $sq) use ($term) {
                            $sq->where('title', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%")
                                ->orWhere('pseudonym', 'like', "%{$term}%");
                        });
                });
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $query, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'sort_asc'      => $query->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc'     => $query->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc'      => $query->orderBy('created_at', 'asc')->orderByDesc('id'),
            'date_desc'     => $query->orderBy('created_at', 'desc')->orderByDesc('id'),
            'views_asc'     => $query->orderBy('views', 'asc')->orderByDesc('id'),
            'views_desc'    => $query->orderBy('views', 'desc')->orderByDesc('id'),
            'likes_asc'     => $query->withCount('likes')->orderBy('likes_count', 'asc')->orderByDesc('id'),
            'likes_desc'    => $query->withCount('likes')->orderBy('likes_count', 'desc')->orderByDesc('id'),
            'duration_asc'  => $query->orderBy('duration', 'asc')->orderByDesc('id'),
            'duration_desc' => $query->orderBy('duration', 'desc')->orderByDesc('id'),

            'title_asc' => $query
                ->leftJoin('blog_video_translations as bvt_sort', function ($join) use ($locale) {
                    $join->on('bvt_sort.video_id', '=', 'blog_videos.id')
                        ->where('bvt_sort.locale', '=', $locale);
                })
                ->orderBy('bvt_sort.title', 'asc')
                ->orderByDesc('blog_videos.id')
                ->select('blog_videos.*'),

            'title_desc' => $query
                ->leftJoin('blog_video_translations as bvt_sort', function ($join) use ($locale) {
                    $join->on('bvt_sort.video_id', '=', 'blog_videos.id')
                        ->where('bvt_sort.locale', '=', $locale);
                })
                ->orderBy('bvt_sort.title', 'desc')
                ->orderByDesc('blog_videos.id')
                ->select('blog_videos.*'),

            default => $query->orderBy('sort', 'asc')->orderByDesc('id'),
        };
    }

    /* ======================== MODEL EVENTS ======================== */

    protected static function booted(): void
    {
        static::saved(function (BlogVideo $video) {
            Log::info('Video saved: ' . $video->id);
        });

        static::deleted(function (BlogVideo $video) {
            Log::info('Video deleted: ' . $video->id);
        });
    }
}
