<?php

namespace App\Models\Admin\Review;

use App\Models\Admin\Market\MarketProduct\MarketProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'user_id',

        'rating',

        'advantages',
        'disadvantages',
        'comment',

        'verified',

        'reply',
        'replied_by',
        'replied_at',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'likes',
        'activity',
    ];

    protected $casts = [
        'reviewable_id' => 'integer',
        'user_id' => 'integer',

        'rating' => 'integer',
        'verified' => 'boolean',

        'replied_by' => 'integer',
        'replied_at' => 'datetime',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',
        'moderated_at' => 'datetime',

        'likes' => 'integer',
        'activity' => 'boolean',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Сущность, к которой относится отзыв */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Автор отзыва */
    public function author(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /** Пользователь, оставивший ответ */
    public function replier(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'replied_by'
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
            ReviewImage::class,
            'review_has_images',
            'review_id',
            'review_image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /* ======================== Scopes ======================== */

    /** Только активные отзывы */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'reviews.activity',
            true
        );
    }

    /** Только одобренные отзывы */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where(
            'reviews.moderation_status',
            1
        );
    }

    /** Только отзывы с подтверждённым опытом */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where(
            'reviews.verified',
            true
        );
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
            ->orderByDesc('reviews.created_at')
            ->orderByDesc('reviews.id');
    }

    /** Отзывы с указанной оценкой */
    public function scopeWithRating(
        Builder $query,
        ?int $rating
    ): Builder {
        if ($rating === null) {
            return $query;
        }

        return $query->where(
            'reviews.rating',
            $rating
        );
    }

    /** Отзывы конкретного пользователя */
    public function scopeForUser(
        Builder $query,
        int $userId
    ): Builder {
        return $query->where(
            'reviews.user_id',
            $userId
        );
    }

    /** Отзывы конкретного типа сущности */
    public function scopeForReviewableType(
        Builder $query,
        ?string $type
    ): Builder {
        $type = trim((string) $type);

        if ($type === '') {
            return $query;
        }

        return $query->where(
            'reviews.reviewable_type',
            $type
        );
    }

    /** Отзывы конкретной полиморфной сущности */
    public function scopeForReviewable(
        Builder $query,
        Model $reviewable
    ): Builder {
        return $query
            ->where(
                'reviews.reviewable_type',
                $reviewable->getMorphClass()
            )
            ->where(
                'reviews.reviewable_id',
                $reviewable->getKey()
            );
    }

    /**
     * Поиск по отзыву, связанным пользователям
     * и полиморфной reviewable-сущности.
     *
     * Для переводимых reviewable-сущностей
     * поиск выполняется только по currentLocale.
     */
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

        $words = collect(
            preg_split(
                '/[\s:#№,"\'«»(){}\[\].!?\/\\\\|;+=*&^%$@<>`~_-]+/u',
                $term
            )
        )
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => mb_strlen($word) >= 2)
            ->values();

        if ($words->isEmpty()) {
            return $query;
        }

        return $query->where(
            function (Builder $searchQuery) use (
                $words,
                $locale
            ): void {
                foreach ($words as $word) {
                    $searchQuery->where(
                        function (Builder $wordQuery) use (
                            $word,
                            $locale
                        ): void {
                            $wordQuery
                                /** Основные поля отзыва */
                                ->where(
                                    'reviews.advantages',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'reviews.disadvantages',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'reviews.comment',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'reviews.reply',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'reviews.moderation_note',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'reviews.reviewable_type',
                                    'like',
                                    "%{$word}%"
                                )

                                /** Автор отзыва */
                                ->orWhereHas(
                                    'author',
                                    function (
                                        Builder $authorQuery
                                    ) use ($word): void {
                                        $authorQuery
                                            ->where(
                                                'name',
                                                'like',
                                                "%{$word}%"
                                            )
                                            ->orWhere(
                                                'email',
                                                'like',
                                                "%{$word}%"
                                            );
                                    }
                                )

                                /** Пользователь, оставивший ответ */
                                ->orWhereHas(
                                    'replier',
                                    function (
                                        Builder $replierQuery
                                    ) use ($word): void {
                                        $replierQuery
                                            ->where(
                                                'name',
                                                'like',
                                                "%{$word}%"
                                            )
                                            ->orWhere(
                                                'email',
                                                'like',
                                                "%{$word}%"
                                            );
                                    }
                                )

                                /** Модератор */
                                ->orWhereHas(
                                    'moderator',
                                    function (
                                        Builder $moderatorQuery
                                    ) use ($word): void {
                                        $moderatorQuery
                                            ->where(
                                                'name',
                                                'like',
                                                "%{$word}%"
                                            )
                                            ->orWhere(
                                                'email',
                                                'like',
                                                "%{$word}%"
                                            );
                                    }
                                )

                                /**
                                 * MarketProduct.
                                 *
                                 * Поиск по собственным полям товара
                                 * и переводу только currentLocale.
                                 */
                                ->orWhereHasMorph(
                                    'reviewable',
                                    [
                                        MarketProduct::class,
                                    ],
                                    function (
                                        Builder $productQuery
                                    ) use (
                                        $word,
                                        $locale
                                    ): void {
                                        $productQuery
                                            ->where(
                                                function (
                                                    Builder $marketProductQuery
                                                ) use (
                                                    $word,
                                                    $locale
                                                ): void {
                                                    $marketProductQuery
                                                        ->where(
                                                            'market_products.url',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'market_products.sku',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'market_products.vendor_code',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'market_products.barcode',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhereHas(
                                                            'translations',
                                                            function (
                                                                Builder $translationQuery
                                                            ) use (
                                                                $word,
                                                                $locale
                                                            ): void {
                                                                $translationQuery
                                                                    ->where(
                                                                        'locale',
                                                                        $locale
                                                                    )
                                                                    ->where(
                                                                        function (
                                                                            Builder $translationSearch
                                                                        ) use ($word): void {
                                                                            $translationSearch
                                                                                ->where(
                                                                                    'title',
                                                                                    'like',
                                                                                    "%{$word}%"
                                                                                )
                                                                                ->orWhere(
                                                                                    'subtitle',
                                                                                    'like',
                                                                                    "%{$word}%"
                                                                                )
                                                                                ->orWhere(
                                                                                    'short',
                                                                                    'like',
                                                                                    "%{$word}%"
                                                                                )
                                                                                ->orWhere(
                                                                                    'description',
                                                                                    'like',
                                                                                    "%{$word}%"
                                                                                );
                                                                        }
                                                                    );
                                                            }
                                                        );
                                                }
                                            );
                                    }
                                );
                        }
                    );
                }
            }
        );
    }

    /** Сортировка и фильтрация по параметру */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort
    ): Builder {
        return match ($sort) {
            'idAsc' => $query
                ->orderBy('reviews.id', 'asc'),

            'idDesc' => $query
                ->orderBy('reviews.id', 'desc'),

            'authorNameAsc' => $query
                ->orderBy(
                    User::query()
                        ->select('name')
                        ->whereColumn('users.id', 'reviews.user_id')
                        ->limit(1),
                    'asc'
                )
                ->orderByDesc('reviews.id'),

            'authorNameDesc' => $query
                ->orderBy(
                    User::query()
                        ->select('name')
                        ->whereColumn('users.id', 'reviews.user_id')
                        ->limit(1),
                    'desc'
                )
                ->orderByDesc('reviews.id'),

            'authorEmailAsc' => $query
                ->orderBy(
                    User::query()
                        ->select('email')
                        ->whereColumn('users.id', 'reviews.user_id')
                        ->limit(1),
                    'asc'
                )
                ->orderByDesc('reviews.id'),

            'authorEmailDesc' => $query
                ->orderBy(
                    User::query()
                        ->select('email')
                        ->whereColumn('users.id', 'reviews.user_id')
                        ->limit(1),
                    'desc'
                )
                ->orderByDesc('reviews.id'),

            'ratingAsc' => $query
                ->orderBy('reviews.rating', 'asc')
                ->orderByDesc('reviews.id'),

            'ratingDesc' => $query
                ->orderBy('reviews.rating', 'desc')
                ->orderByDesc('reviews.id'),

            'likesAsc' => $query
                ->orderBy('reviews.likes', 'asc')
                ->orderByDesc('reviews.id'),

            'likesDesc' => $query
                ->orderBy('reviews.likes', 'desc')
                ->orderByDesc('reviews.id'),

            'imagesAsc' => $query
                ->withCount('images')
                ->orderBy('images_count', 'asc')
                ->orderByDesc('reviews.id'),

            'imagesDesc' => $query
                ->withCount('images')
                ->orderBy('images_count', 'desc')
                ->orderByDesc('reviews.id'),

            'commentAsc' => $query
                ->orderBy('reviews.comment', 'asc')
                ->orderByDesc('reviews.id'),

            'commentDesc' => $query
                ->orderBy('reviews.comment', 'desc')
                ->orderByDesc('reviews.id'),

            'reviewableTypeAsc' => $query
                ->orderBy('reviews.reviewable_type', 'asc')
                ->orderByDesc('reviews.id'),

            'reviewableTypeDesc' => $query
                ->orderBy('reviews.reviewable_type', 'desc')
                ->orderByDesc('reviews.id'),

            'verifiedAsc' => $query
                ->orderBy('reviews.verified', 'asc')
                ->orderByDesc('reviews.id'),

            'verifiedDesc' => $query
                ->orderBy('reviews.verified', 'desc')
                ->orderByDesc('reviews.id'),

            'verified' => $query
                ->where('reviews.verified', true)
                ->orderByDesc('reviews.id'),

            'notVerified' => $query
                ->where('reviews.verified', false)
                ->orderByDesc('reviews.id'),

            'replyAsc' => $query
                ->orderByRaw(
                    "CASE WHEN reply IS NULL OR TRIM(reply) = '' THEN 0 ELSE 1 END ASC"
                )
                ->orderByDesc('reviews.id'),

            'replyDesc' => $query
                ->orderByRaw(
                    "CASE WHEN reply IS NULL OR TRIM(reply) = '' THEN 0 ELSE 1 END DESC"
                )
                ->orderByDesc('reviews.id'),

            'hasReply' => $query
                ->whereNotNull('reviews.reply')
                ->whereRaw("TRIM(reviews.reply) <> ''")
                ->orderByDesc('reviews.id'),

            'noReply' => $query
                ->where(function (Builder $replyQuery) {
                    $replyQuery
                        ->whereNull('reviews.reply')
                        ->orWhereRaw("TRIM(reviews.reply) = ''");
                })
                ->orderByDesc('reviews.id'),

            'createdAtAsc', 'dateAsc' => $query
                ->orderBy('reviews.created_at', 'asc')
                ->orderByDesc('reviews.id'),

            'createdAtDesc', 'dateDesc' => $query
                ->orderBy('reviews.created_at', 'desc')
                ->orderByDesc('reviews.id'),

            'updatedAtAsc' => $query
                ->orderBy('reviews.updated_at', 'asc')
                ->orderByDesc('reviews.id'),

            'updatedAtDesc' => $query
                ->orderBy('reviews.updated_at', 'desc')
                ->orderByDesc('reviews.id'),

            'activityAsc' => $query
                ->orderBy('reviews.activity', 'asc')
                ->orderByDesc('reviews.id'),

            'activityDesc' => $query
                ->orderBy('reviews.activity', 'desc')
                ->orderByDesc('reviews.id'),

            'activity' => $query
                ->where('reviews.activity', true)
                ->orderByDesc('reviews.id'),

            'inactive' => $query
                ->where('reviews.activity', false)
                ->orderByDesc('reviews.id'),

            'moderationStatusAsc' => $query
                ->orderBy('reviews.moderation_status', 'asc')
                ->orderByDesc('reviews.id'),

            'moderationStatusDesc' => $query
                ->orderBy('reviews.moderation_status', 'desc')
                ->orderByDesc('reviews.id'),

            'moderationPending' => $query
                ->where('reviews.moderation_status', 0)
                ->orderByDesc('reviews.id'),

            'moderationApproved' => $query
                ->where('reviews.moderation_status', 1)
                ->orderByDesc('reviews.id'),

            'moderationRejected' => $query
                ->where('reviews.moderation_status', 2)
                ->orderByDesc('reviews.id'),

            default => $query->ordered(),
        };
    }

    /* ======================== Helpers ======================== */

    /** Отзыв активен */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Отзыв ожидает модерации */
    public function isPending(): bool
    {
        return (int) $this->moderation_status === 0;
    }

    /** Отзыв одобрен */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Отзыв отклонён */
    public function isRejected(): bool
    {
        return (int) $this->moderation_status === 2;
    }

    /** Опыт пользователя подтверждён */
    public function isVerified(): bool
    {
        return (bool) $this->verified;
    }

    /** У отзыва есть ответ */
    public function hasReply(): bool
    {
        return filled($this->reply);
    }

    /** У отзыва есть изображения */
    public function hasImages(): bool
    {
        if ($this->relationLoaded('images')) {
            return $this->images->isNotEmpty();
        }

        return $this->images()->exists();
    }

    /** Получить тип сущности из morphMap */
    public function getReviewableType(): string
    {
        return (string) $this->reviewable_type;
    }
}
