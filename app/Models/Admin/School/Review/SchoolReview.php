<?php

namespace App\Models\Admin\School\Review;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SchoolReview extends Model
{
    use HasFactory;

    protected $table = 'school_reviews';

    protected $fillable = [
        'user_id',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'title',
        'body',
        'status',
        'is_public',
        'published_at',
        'helpful_count',
        'reported_count',
        'meta',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'reviewable_id' => 'integer',
        'rating' => 'integer',
        'is_public' => 'boolean',
        'published_at' => 'datetime',
        'helpful_count' => 'integer',
        'reported_count' => 'integer',
        'meta' => 'array',
    ];

    /** Автор отзыва */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Сущность отзыва */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Опубликованные */
    public function scopePublished(Builder $q): Builder
    {
        return $q
            ->where('status', 'approved')
            ->where('is_public', true)
            ->where(function (Builder $query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /** С минимальным рейтингом */
    public function scopeWithMinRating(Builder $q, int $min = 4): Builder
    {
        return $q->where('rating', '>=', $min);
    }

    /** Ожидают модерации */
    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    /** Одобренные */
    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', 'approved');
    }

    /** Отклонённые */
    public function scopeRejected(Builder $q): Builder
    {
        return $q->where('status', 'rejected');
    }

    /** Одобрить отзыв */
    public function approve(): void
    {
        $this->status = 'approved';
        $this->published_at = now();
        $this->save();
    }

    /** Отклонить отзыв */
    public function reject(?string $reason = null): void
    {
        $this->status = 'rejected';

        if ($reason) {
            $meta = $this->meta ?? [];
            $meta['reject_reason'] = $reason;
            $this->meta = $meta;
        }

        $this->save();
    }

    /** Отметить полезным */
    public function markHelpful(): void
    {
        $this->increment('helpful_count');
    }

    /** Пожаловаться */
    public function report(): void
    {
        $this->increment('reported_count');
    }
}
