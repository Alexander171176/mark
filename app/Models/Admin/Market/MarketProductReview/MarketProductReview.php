<?php

namespace App\Models\Admin\Market\MarketProductReview;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MarketProductReview extends Model
{
    use HasFactory;

    protected $table = 'market_product_reviews';

    protected $fillable = [
        'market_product_id',
        'user_id',

        'rating',

        'advantages',
        'disadvantages',
        'comment',

        'verified_purchase',

        'seller_reply',
        'seller_reply_at',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'likes',
        'activity',
    ];

    protected $casts = [
        'market_product_id' => 'integer',
        'user_id' => 'integer',

        'rating' => 'integer',
        'verified_purchase' => 'boolean',

        'seller_reply_at' => 'datetime',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',
        'moderated_at' => 'datetime',

        'likes' => 'integer',
        'activity' => 'boolean',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Товар отзыва */
    public function product(): BelongsTo
    {
        return $this->belongsTo(
            MarketProduct::class,
            'market_product_id'
        );
    }

    /** Автор отзыва */
    public function author(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /** Модератор отзыва */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'moderated_by'
        );
    }

    /** Изображения отзыва */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            MarketProductReviewImage::class,
            'market_product_review_has_images',
            'market_product_review_id',
            'market_product_review_image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /* ======================== Scopes ======================== */

    /** Только активные отзывы */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('activity', true);
    }

    /** Только одобренные отзывы */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 1);
    }

    /** Только подтверждённые покупки */
    public function scopeVerifiedPurchase(Builder $query): Builder
    {
        return $query->where('verified_purchase', true);
    }

    /** Публичные отзывы */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->active()
            ->approved();
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    /** Отзывы с указанной оценкой */
    public function scopeWithRating(
        Builder $query,
        ?int $rating
    ): Builder {
        if ($rating === null) {
            return $query;
        }

        return $query->where('rating', $rating);
    }

    /** Поиск по отзыву и пользователю */
    public function scopeSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();

        return $query->where(function (Builder $q) use ($term, $locale) {
            $q->where('advantages', 'like', "%{$term}%")
                ->orWhere('disadvantages', 'like', "%{$term}%")
                ->orWhere('comment', 'like', "%{$term}%")
                ->orWhere('seller_reply', 'like', "%{$term}%")
                ->orWhere('moderation_note', 'like', "%{$term}%")

                ->orWhereHas('author', function (Builder $authorQuery) use ($term) {
                    $authorQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                })

                ->orWhereHas('product.translations', function (Builder $productQuery) use ($term, $locale) {
                    $productQuery
                        ->where('locale', $locale)
                        ->where(function (Builder $translationQuery) use ($term) {
                            $translationQuery
                                ->where('title', 'like', "%{$term}%")
                                ->orWhere('subtitle', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%");
                        });
                });
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort
    ): Builder {
        return match ($sort) {
            'idAsc' => $query->orderBy('market_product_reviews.id', 'asc'),
            'idDesc' => $query->orderBy('market_product_reviews.id', 'desc'),

            'ratingAsc' => $query
                ->orderBy('market_product_reviews.rating', 'asc')
                ->orderByDesc('market_product_reviews.id'),

            'ratingDesc' => $query
                ->orderBy('market_product_reviews.rating', 'desc')
                ->orderByDesc('market_product_reviews.id'),

            'likesAsc' => $query
                ->orderBy('market_product_reviews.likes', 'asc')
                ->orderByDesc('market_product_reviews.id'),

            'likesDesc' => $query
                ->orderBy('market_product_reviews.likes', 'desc')
                ->orderByDesc('market_product_reviews.id'),

            'verifiedPurchaseAsc' => $query
                ->orderBy('market_product_reviews.verified_purchase', 'asc')
                ->orderByDesc('market_product_reviews.id'),

            'verifiedPurchaseDesc' => $query
                ->orderBy('market_product_reviews.verified_purchase', 'desc')
                ->orderByDesc('market_product_reviews.id'),

            'verifiedPurchase' => $query
                ->where('market_product_reviews.verified_purchase', true)
                ->orderByDesc('market_product_reviews.id'),

            'activityAsc' => $query
                ->orderBy('market_product_reviews.activity', 'asc')
                ->orderByDesc('market_product_reviews.id'),

            'activityDesc' => $query
                ->orderBy('market_product_reviews.activity', 'desc')
                ->orderByDesc('market_product_reviews.id'),

            'activity' => $query
                ->where('market_product_reviews.activity', true)
                ->orderByDesc('market_product_reviews.id'),

            'inactive' => $query
                ->where('market_product_reviews.activity', false)
                ->orderByDesc('market_product_reviews.id'),

            'moderationStatusAsc' => $query
                ->orderBy('market_product_reviews.moderation_status', 'asc')
                ->orderByDesc('market_product_reviews.id'),

            'moderationStatusDesc' => $query
                ->orderBy('market_product_reviews.moderation_status', 'desc')
                ->orderByDesc('market_product_reviews.id'),

            'moderationPending' => $query
                ->where('market_product_reviews.moderation_status', 0)
                ->orderByDesc('market_product_reviews.id'),

            'moderationApproved' => $query
                ->where('market_product_reviews.moderation_status', 1)
                ->orderByDesc('market_product_reviews.id'),

            'moderationRejected' => $query
                ->where('market_product_reviews.moderation_status', 2)
                ->orderByDesc('market_product_reviews.id'),

            'imagesAsc' => $query
                ->withCount('images')
                ->orderBy('images_count', 'asc')
                ->orderByDesc('market_product_reviews.id'),

            'imagesDesc' => $query
                ->withCount('images')
                ->orderBy('images_count', 'desc')
                ->orderByDesc('market_product_reviews.id'),

            'createdAtAsc', 'dateAsc' => $query
                ->orderBy('market_product_reviews.created_at', 'asc')
                ->orderByDesc('market_product_reviews.id'),

            'createdAtDesc', 'dateDesc' => $query
                ->orderBy('market_product_reviews.created_at', 'desc')
                ->orderByDesc('market_product_reviews.id'),

            'updatedAtAsc' => $query
                ->orderBy('market_product_reviews.updated_at', 'asc')
                ->orderByDesc('market_product_reviews.id'),

            'updatedAtDesc' => $query
                ->orderBy('market_product_reviews.updated_at', 'desc')
                ->orderByDesc('market_product_reviews.id'),

            default => $query->ordered(),
        };
    }

    /* ======================== Helpers ======================== */

    /** Отзыв активен */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Отзыв одобрен */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Отзыв оставлен после подтверждённой покупки */
    public function isVerifiedPurchase(): bool
    {
        return (bool) $this->verified_purchase;
    }

    /** Есть ответ продавца */
    public function hasSellerReply(): bool
    {
        return filled($this->seller_reply);
    }
}
