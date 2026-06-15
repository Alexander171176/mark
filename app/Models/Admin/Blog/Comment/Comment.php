<?php

namespace App\Models\Admin\Blog\Comment;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $fillable = [
        'user_id',

        'commentable_id',
        'commentable_type',

        'parent_id',
        'content',

        'activity',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'parent_id' => 'integer',

        'activity' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',
        'moderated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Чей комментарий */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** модератор комментария */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** полиморфная связь */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Родительская ветка */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /* ======================== HELPERS ======================== */

    /** Если активные */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Если одобренные */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
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

    /** На одобрении */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('moderation_status', 0);
    }

    /** Не одобренные */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('moderation_status', 2);
    }

    /** Родительская ветка */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Публичные комментарии: approved + active
     */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query->approved()->active();
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('comments.id');
    }

    /** Поиск по словам */
    public function scopeSearch(Builder $query, ?string $term = null): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $escapeLike = static function (string $value): string {
            return str_replace(
                ['\\', '%', '_'],
                ['\\\\', '\%', '\_'],
                $value
            );
        };

        $original = $escapeLike($term);

        $words = collect(preg_split('/[^\pL\pN]+/u', $term))
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => mb_strlen($word) >= 2)
            ->unique()
            ->values();

        return $query->where(function (Builder $query) use ($words, $original, $escapeLike) {
            // 1. Ищем исходную строку как есть
            $query
                ->where('comments.content', 'like', "%{$original}%")
                ->orWhere('comments.moderation_note', 'like', "%{$original}%")
                ->orWhere('comments.commentable_type', 'like', "%{$original}%");

            // 2. Ищем по словам, если в запросе были точки/знаки/символы
            foreach ($words as $word) {
                $word = $escapeLike($word);

                $query->orWhere(function (Builder $query) use ($word) {
                    $query
                        ->where('comments.content', 'like', "%{$word}%")
                        ->orWhere('comments.moderation_note', 'like', "%{$word}%")
                        ->orWhere('comments.commentable_type', 'like', "%{$word}%")

                        ->orWhereHas('user', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%")
                                ->orWhere('email', 'like', "%{$word}%");
                        })

                        ->orWhereHas('moderator', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%")
                                ->orWhere('email', 'like', "%{$word}%");
                        })

                        ->orWhereHas('parent', function (Builder $qq) use ($word) {
                            $qq->where('content', 'like', "%{$word}%");
                        })

                        ->orWhereHasMorph(
                            'commentable',
                            [BlogArticle::class, BlogVideo::class],
                            function (Builder $qq) use ($word) {
                                $qq->whereHas('translations', function (Builder $translationQuery) use ($word) {
                                    $translationQuery
                                        ->where('title', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('description', 'like', "%{$word}%");
                                });
                            }
                        );
                });
            }
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'idAsc' => $query->orderBy('comments.id', 'asc'),
            'idDesc' => $query->orderBy('comments.id', 'desc'),

            'userNameAsc' => $query
                ->leftJoin('users as user_sort', 'user_sort.id', '=', 'comments.user_id')
                ->orderBy('user_sort.name', 'asc')
                ->orderByDesc('comments.id')
                ->select('comments.*'),

            'userNameDesc' => $query
                ->leftJoin('users as user_sort', 'user_sort.id', '=', 'comments.user_id')
                ->orderBy('user_sort.name', 'desc')
                ->orderByDesc('comments.id')
                ->select('comments.*'),

            'userEmailAsc' => $query
                ->leftJoin('users as user_sort', 'user_sort.id', '=', 'comments.user_id')
                ->orderBy('user_sort.email', 'asc')
                ->orderByDesc('comments.id')
                ->select('comments.*'),

            'userEmailDesc' => $query
                ->leftJoin('users as user_sort', 'user_sort.id', '=', 'comments.user_id')
                ->orderBy('user_sort.email', 'desc')
                ->orderByDesc('comments.id')
                ->select('comments.*'),

            'contentAsc' => $query->orderBy('comments.content', 'asc')->orderByDesc('comments.id'),
            'contentDesc' => $query->orderBy('comments.content', 'desc')->orderByDesc('comments.id'),

            'typeAsc' => $query->orderBy('comments.commentable_type', 'asc')->orderByDesc('comments.id'),
            'typeDesc' => $query->orderBy('comments.commentable_type', 'desc')->orderByDesc('comments.id'),

            'commentableTitleAsc' => $query
                ->leftJoin('blog_article_translations as bat_sort', function ($join) {
                    $join->on('bat_sort.article_id', '=', 'comments.commentable_id')
                        ->where('comments.commentable_type', '=', BlogArticle::class);
                })
                ->leftJoin('blog_video_translations as bvt_sort', function ($join) {
                    $join->on('bvt_sort.video_id', '=', 'comments.commentable_id')
                        ->where('comments.commentable_type', '=', BlogVideo::class);
                })
                ->orderByRaw('COALESCE(bat_sort.title, bvt_sort.title) asc')
                ->orderByDesc('comments.id')
                ->select('comments.*'),

            'commentableTitleDesc' => $query
                ->leftJoin('blog_article_translations as bat_sort', function ($join) {
                    $join->on('bat_sort.article_id', '=', 'comments.commentable_id')
                        ->where('comments.commentable_type', '=', BlogArticle::class);
                })
                ->leftJoin('blog_video_translations as bvt_sort', function ($join) {
                    $join->on('bvt_sort.video_id', '=', 'comments.commentable_id')
                        ->where('comments.commentable_type', '=', BlogVideo::class);
                })
                ->orderByRaw('COALESCE(bat_sort.title, bvt_sort.title) desc')
                ->orderByDesc('comments.id')
                ->select('comments.*'),

            'repliesAsc' => $query->withCount('replies')->orderBy('replies_count', 'asc')->orderByDesc('comments.id'),
            'repliesDesc' => $query->withCount('replies')->orderBy('replies_count', 'desc')->orderByDesc('comments.id'),

            'createdAtAsc' => $query->orderBy('comments.created_at', 'asc')->orderByDesc('comments.id'),
            'createdAtDesc' => $query->orderBy('comments.created_at', 'desc')->orderByDesc('comments.id'),

            'updatedAtAsc' => $query->orderBy('comments.updated_at', 'asc')->orderByDesc('comments.id'),
            'updatedAtDesc' => $query->orderBy('comments.updated_at', 'desc')->orderByDesc('comments.id'),

            'activityAsc' => $query->orderBy('comments.activity', 'asc')->orderByDesc('comments.id'),
            'activityDesc' => $query->orderBy('comments.activity', 'desc')->orderByDesc('comments.id'),
            'activity' => $query->where('comments.activity', true)->orderByDesc('comments.id'),
            'inactive' => $query->where('comments.activity', false)->orderByDesc('comments.id'),

            'moderationStatusAsc' => $query->orderBy('comments.moderation_status', 'asc')->orderByDesc('comments.id'),
            'moderationStatusDesc' => $query->orderBy('comments.moderation_status', 'desc')->orderByDesc('comments.id'),
            'moderationPending' => $query->where('comments.moderation_status', 0)->orderByDesc('comments.id'),
            'moderationApproved' => $query->where('comments.moderation_status', 1)->orderByDesc('comments.id'),
            'moderationRejected' => $query->where('comments.moderation_status', 2)->orderByDesc('comments.id'),

            default => $query->ordered(),
        };
    }
}
