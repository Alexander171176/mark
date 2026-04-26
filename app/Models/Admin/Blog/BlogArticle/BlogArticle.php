<?php

namespace App\Models\Admin\Blog\BlogArticle;

use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\Admin\Blog\Comment\Comment;
use App\Models\User;
use App\Models\User\Like\BlogArticleLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;

class BlogArticle extends Model
{
    use HasFactory;

    protected $table = 'blog_articles';

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
        'url',

        'published_at',
        'show_from_at',
        'show_to_at',

        'views',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
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

    /** Владелец/автор статьи */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Модератор статьи */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Переводы статьи */
    public function translations(): HasMany
    {
        return $this->hasMany(BlogArticleTranslation::class, 'article_id');
    }

    /** Текущий перевод по locale */
    public function translation(?string $locale = null): ?BlogArticleTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first();
    }

    /** Перевод с fallback */
    public function translationOrFallback(?string $locale = null, string $fallback = 'ru'): ?BlogArticleTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }

    /** Комментарии (полиморфные) */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /** Лайки статьи */
    public function likes(): HasMany
    {
        return $this->hasMany(BlogArticleLike::class, 'article_id');
    }

    /** Пользователи, лайкнувшие статью */
    public function likedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'blog_article_likes',
            'article_id',
            'user_id'
        )->withTimestamps();
    }

    /** Изображения статьи */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogArticleImage::class,
            'blog_article_has_images',
            'article_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /** Рубрики статьи */
    public function rubrics(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogRubric::class,
            'blog_article_has_rubric',
            'article_id',
            'rubric_id'
        );
    }

    /** Теги статьи */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogTag::class,
            'blog_article_has_tag',
            'article_id',
            'tag_id'
        );
    }

    /** Видео статьи */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogVideo::class,
            'blog_article_has_video',
            'article_id',
            'video_id'
        )
            ->withPivot('sort')
            ->orderByPivot('sort');
    }

    /** Рекомендованные статьи */
    public function relatedArticles(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'blog_article_related',
            'article_id',
            'related_article_id'
        )
            ->withPivot('sort')
            ->orderByPivot('sort');
    }

    /** Где эта статья используется как рекомендованная */
    public function usedInRelatedArticles(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'blog_article_related',
            'related_article_id',
            'article_id'
        )
            ->withPivot('sort');
    }

    /* ======================== MODEL EVENTS ======================== */

    protected static function booted(): void
    {
        static::saved(function (BlogArticle $article) {
            Log::info('Статья блога сохранена: ' . $article->id . ' / ' . $article->url);
        });

        static::deleted(function (BlogArticle $article) {
            Log::info('Статья блога удалена: ' . $article->id . ' / ' . $article->url);
        });
    }

    /* ======================== HELPERS ======================== */

    /** Активна ли статья */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Одобрена ли статья */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Находится ли статья в окне показа */
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

    /** Получить title из текущего перевода */
    public function getTranslatedTitle(?string $locale = null, string $fallback = 'ru'): ?string
    {
        return $this->translationOrFallback($locale, $fallback)?->title;
    }

    /* ======================== Scopes ======================== */

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderByDesc('id');
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

    /** Публичные статьи */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
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

    /** Поиск по переводимым полям и url */
    public function scopeSearch(Builder $query, ?string $term, ?string $locale = null): Builder
    {
        if (!$term) {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();

        return $query->where(function (Builder $q) use ($term, $locale) {
            $q->where('url', 'like', "%{$term}%")
                ->orWhereHas('translations', function (Builder $tq) use ($term, $locale) {
                    $tq->where('locale', $locale)
                        ->where(function (Builder $sq) use ($term) {
                            $sq->where('title', 'like', "%{$term}%")
                                ->orWhere('subtitle', 'like', "%{$term}%")
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
            'sort_asc'   => $query->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc'  => $query->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc'   => $query->orderBy('created_at', 'asc')->orderByDesc('id'),
            'date_desc'  => $query->orderBy('created_at', 'desc')->orderByDesc('id'),
            'views_asc'  => $query->orderBy('views', 'asc')->orderByDesc('id'),
            'views_desc' => $query->orderBy('views', 'desc')->orderByDesc('id'),
            'likes_asc'  => $query->withCount('likes')->orderBy('likes_count', 'asc')->orderByDesc('id'),
            'likes_desc' => $query->withCount('likes')->orderBy('likes_count', 'desc')->orderByDesc('id'),

            'title_asc' => $query
                ->leftJoin('blog_article_translations as bat_sort', function ($join) use ($locale) {
                    $join->on('bat_sort.article_id', '=', 'blog_articles.id')
                        ->where('bat_sort.locale', '=', $locale);
                })
                ->orderBy('bat_sort.title', 'asc')
                ->orderByDesc('blog_articles.id')
                ->select('blog_articles.*'),

            'title_desc' => $query
                ->leftJoin('blog_article_translations as bat_sort', function ($join) use ($locale) {
                    $join->on('bat_sort.article_id', '=', 'blog_articles.id')
                        ->where('bat_sort.locale', '=', $locale);
                })
                ->orderBy('bat_sort.title', 'desc')
                ->orderByDesc('blog_articles.id')
                ->select('blog_articles.*'),

            default => $query->ordered(),
        };
    }
}
