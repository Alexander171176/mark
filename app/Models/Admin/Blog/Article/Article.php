<?php

namespace App\Models\Admin\Blog\Article;

use App\Models\Admin\Blog\Comment\Comment;
use App\Models\Admin\Blog\Rubric\Rubric;
use App\Models\Admin\Blog\Tag\Tag;
use App\Models\Admin\Blog\Video\Video;
use App\Models\User;
use App\Models\User\Like\ArticleLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

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

        'img',
        'locale',
        'title',
        'url',
        'subtitle',
        'short',
        'description',
        'pseudonym',

        'published_at',
        'show_from_at',
        'show_to_at',

        'views',

        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    protected $casts = [
        'sort' => 'integer',
        'activity' => 'boolean',
        'left' => 'boolean',
        'main' => 'boolean',
        'right' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_at' => 'datetime',

        'published_at' => 'date',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',

        'views' => 'integer',
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

    /** Рубрики, к которым привязана статья (many-to-many) */
    public function rubrics(): BelongsToMany
    {
        return $this->belongsToMany(
            Rubric::class,
            'article_has_rubric',
            'article_id',
            'rubric_id'
        );
    }

    /** Теги статьи (many-to-many) */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'article_has_tag',
            'article_id',
            'tag_id'
        );
    }

    /** Комментарии (полиморфные) */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /** Лайки статьи */
    public function likes(): HasMany
    {
        return $this->hasMany(ArticleLike::class, 'article_id');
    }

    /** Изображения статьи (many-to-many) + сортировка по pivot.order */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            ArticleImage::class,
            'article_has_images',
            'article_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /** Видео, привязанные к статье (many-to-many) + сортировка по pivot.sort */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(
            Video::class,
            'article_has_video',
            'article_id',
            'video_id'
        )
            ->withPivot('sort')
            ->orderBy('pivot_sort');
    }

    /** Рекомендованные статьи (self many-to-many) + сортировка по pivot.sort */
    public function relatedArticles(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'article_related',
            'article_id',
            'related_article_id'
        )
            ->withPivot('sort')
            ->orderBy('pivot_sort');
    }

    /* ======================== MODEL EVENTS ======================== */
    protected static function booted(): void
    {
        static::saved(function (Article $article) {
            Log::info('Article saved: '.$article->id.' / '.$article->title);
        });

        static::deleted(function (Article $article) {
            Log::info('Article deleted: '.$article->id.' / '.$article->title);
        });
    }

    /* ======================== HELPERS ======================== */

    /** Если активные */
    public function isActive(): bool { return (bool) $this->activity; }

    /** Если одобренные */
    public function isApproved(): bool { return (int) $this->moderation_status === 1; }

    /* ======================== Scopes ======================== */

    /** Удобная сортировка: по sort ↑ затем по id ↓ */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderByDesc('id');
    }

    /** По локали */
    public function scopeByLocale(Builder $query, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();
        return $query->where('locale', $locale);
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
     * Окно показа:
     *  - show_from_at IS NULL OR show_from_at <= now()
     *  - show_to_at   IS NULL OR show_to_at   >= now()
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

    /**
     * Публичный скоуп: approved + active + locale + окно показа
     */
    public function scopeForPublic(Builder $query, ?string $locale = null): Builder
    {
        return $query
            ->approved()
            ->active()
            ->byLocale($locale)
            ->inShowWindow();
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

    /** Поиск по заголовку/подзаголовку/описанию */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('subtitle', 'like', "%{$term}%")
                ->orWhere('short', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('url', 'like', "%{$term}%")
                ->orWhere('pseudonym', 'like', "%{$term}%");
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'sort_asc'   => $query->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc'  => $query->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc'   => $query->orderBy('created_at', 'asc')->orderByDesc('id'),
            'date_desc'  => $query->orderBy('created_at', 'desc')->orderByDesc('id'),
            'views_asc'  => $query->orderBy('views', 'asc')->orderByDesc('id'),
            'views_desc' => $query->orderBy('views', 'desc')->orderByDesc('id'),
            'likes_asc'  => $query->withCount('likes')->orderBy('likes_count', 'asc')->orderByDesc('id'),
            'likes_desc' => $query->withCount('likes')->orderBy('likes_count', 'desc')->orderByDesc('id'),
            'title_asc'  => $query->orderBy('title', 'asc')->orderByDesc('id'),
            'title_desc' => $query->orderBy('title', 'desc')->orderByDesc('id'),
            default      => $query->ordered(),
        };
    }
}
