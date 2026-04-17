<?php

namespace App\Models\Admin\Blog\Comment;

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
}
