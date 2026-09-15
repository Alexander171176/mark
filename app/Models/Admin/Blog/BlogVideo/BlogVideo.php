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
        $this
            ->addMediaCollection('videos')
            ->singleFile();
    }

    /* ======================== Relations ======================== */

    /** Владелец видео */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /** Модератор видео */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'moderated_by'
        );
    }

    /** Переводы видео */
    public function translations(): HasMany
    {
        return $this->hasMany(
            BlogVideoTranslation::class,
            'video_id'
        );
    }

    /** Текущий перевод по locale */
    public function translation(
        ?string $locale = null
    ): ?BlogVideoTranslation {
        $locale = $locale
            ?: app()->getLocale();

        return $this->translations
            ->where(
                'locale',
                $locale
            )
            ->first();
    }

    /** Перевод с fallback */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallback = null
    ): ?BlogVideoTranslation {
        $locale = $locale
            ?: app()->getLocale();

        $fallback = $fallback
            ?: config(
                'app.fallback_locale',
                'ru'
            );

        return $this->translations
            ->firstWhere(
                'locale',
                $locale
            )
            ?: $this->translations
                ->firstWhere(
                    'locale',
                    $fallback
                )
                ?: $this->translations
                    ->first();
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
        return $this->morphMany(
            Comment::class,
            'commentable'
        );
    }

    /** Лайки */
    public function likes(): HasMany
    {
        return $this->hasMany(
            BlogVideoLike::class,
            'video_id'
        );
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
        /**
         * YouTube.
         */
        if (
            $this->source_type === 'youtube'
            && $this->external_video_id
        ) {
            if (
                preg_match(
                    '/(?:v=|youtu\.be\/)([^&\s]+)/',
                    $this->external_video_id,
                    $matches
                )
            ) {
                return 'https://www.youtube.com/embed/'
                    . $matches[1];
            }
        }

        /**
         * Vimeo.
         */
        if (
            $this->source_type === 'vimeo'
            && $this->external_video_id
        ) {
            if (
                preg_match(
                    '/vimeo\.com\/(?:video\/)?(\d+)/',
                    $this->external_video_id,
                    $matches
                )
            ) {
                return 'https://player.vimeo.com/video/'
                    . $matches[1];
            }
        }

        return null;
    }

    /** Получить title из текущего перевода / fallback */
    public function getTranslatedTitle(
        ?string $locale = null,
        ?string $fallback = null
    ): ?string {
        return $this
            ->translationOrFallback(
                $locale,
                $fallback
            )
            ?->title;
    }

    /* ======================== Helpers ======================== */

    /** Активно ли видео */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Прошло ли видео модерацию */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Находится ли видео в окне показа */
    public function isPublishedNow(): bool
    {
        $now = now();

        if (
            $this->show_from_at
            && $now->lt(
                $this->show_from_at
            )
        ) {
            return false;
        }

        if (
            $this->show_to_at
            && $now->gt(
                $this->show_to_at
            )
        ) {
            return false;
        }

        return true;
    }

    /* ======================== Scopes ======================== */

    /** Только активные */
    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'blog_videos.activity',
            true
        );
    }

    /** Только одобренные */
    public function scopeApproved(
        Builder $query
    ): Builder {
        return $query->where(
            'blog_videos.moderation_status',
            1
        );
    }

    /**
     * Видимые по окну показа:
     *
     * show_from_at IS NULL
     * OR show_from_at <= now()
     *
     * show_to_at IS NULL
     * OR show_to_at >= now()
     */
    public function scopeInShowWindow(
        Builder $query
    ): Builder {
        return $query
            ->where(
                function (Builder $query) {
                    $query
                        ->whereNull(
                            'blog_videos.show_from_at'
                        )
                        ->orWhere(
                            'blog_videos.show_from_at',
                            '<=',
                            now()
                        );
                }
            )
            ->where(
                function (Builder $query) {
                    $query
                        ->whereNull(
                            'blog_videos.show_to_at'
                        )
                        ->orWhere(
                            'blog_videos.show_to_at',
                            '>=',
                            now()
                        );
                }
            );
    }

    /** Только публичные */
    public function scopePublicOnly(
        Builder $query
    ): Builder {
        return $query->where(
            'blog_videos.is_private',
            false
        );
    }

    /** Только приватные */
    public function scopePrivateOnly(
        Builder $query
    ): Builder {
        return $query->where(
            'blog_videos.is_private',
            true
        );
    }

    /** Видимые */
    public function scopeVisible(
        Builder $query
    ): Builder {
        return $query
            ->publicOnly()
            ->approved()
            ->active()
            ->inShowWindow();
    }

    /** Публичный набор */
    public function scopeForPublic(
        Builder $query
    ): Builder {
        return $query
            ->publicOnly()
            ->approved()
            ->active()
            ->inShowWindow();
    }

    /** Левый блок */
    public function scopeInLeft(
        Builder $query
    ): Builder {
        return $query->where(
            'blog_videos.left',
            true
        );
    }

    /** Главный блок */
    public function scopeInMain(
        Builder $query
    ): Builder {
        return $query->where(
            'blog_videos.main',
            true
        );
    }

    /** Правый блок */
    public function scopeInRight(
        Builder $query
    ): Builder {
        return $query->where(
            'blog_videos.right',
            true
        );
    }

    /* ======================== Admin search ======================== */

    /** Поиск по словам */
    public function scopeSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim(
            (string) $term
        );

        if ($term === '') {
            return $query;
        }

        $locale = $locale
            ?: app()->getLocale();

        $words = collect(
            preg_split(
                '/[\s:#№,"\'«»(){}\[\].!?\/\\\\|;+=*&^%$@<>`~_-]+/u',
                $term
            )
        )
            ->map(
                fn ($word) => trim($word)
            )
            ->filter(
                fn ($word) =>
                    mb_strlen($word) >= 2
            )
            ->values();

        if ($words->isEmpty()) {
            return $query;
        }

        return $query->where(
            function (Builder $query) use (
                $words,
                $locale
            ) {
                foreach ($words as $word) {
                    $query->where(
                        function (Builder $query) use (
                            $word,
                            $locale
                        ) {
                            $query
                                ->where(
                                    'blog_videos.url',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'blog_videos.external_video_id',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'blog_videos.source_type',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'blog_videos.embed_code',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'blog_videos.moderation_note',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhereHas(
                                    'translations',
                                    function (Builder $query) use (
                                        $word,
                                        $locale
                                    ) {
                                        $query
                                            ->where(
                                                'locale',
                                                $locale
                                            )
                                            ->where(
                                                function (Builder $subQuery) use ($word) {
                                                    $subQuery
                                                        ->where(
                                                            'title',
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
                                                        )
                                                        ->orWhere(
                                                            'pseudonym',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'meta_title',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'meta_keywords',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'meta_desc',
                                                            'like',
                                                            "%{$word}%"
                                                        );
                                                }
                                            );
                                    }
                                )
                                ->orWhereHas(
                                    'owner',
                                    function (Builder $query) use ($word) {
                                        $query
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
                                ->orWhereHas(
                                    'moderator',
                                    function (Builder $query) use ($word) {
                                        $query
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
                                );
                        }
                    );
                }
            }
        );
    }

    /* ======================== Public search ======================== */

    /**
     * Public-поиск видео.
     *
     * Поля соответствуют Public Index:
     *
     * - id;
     * - title;
     * - short;
     * - pseudonym;
     * - url;
     * - source_type;
     * - external_video_id;
     * - owner.name.
     *
     * Переводы:
     *
     * current locale -> fallback.
     */
    public function scopePublicSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim(
            (string) $term
        );

        if ($term === '') {
            return $query;
        }

        $locale = $locale
            ?: app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        return $query->where(
            function (Builder $searchQuery) use (
                $term,
                $locale,
                $fallbackLocale
            ) {
                /**
                 * ID.
                 */
                $searchQuery->where(
                    'blog_videos.id',
                    'like',
                    "%{$term}%"
                );

                /**
                 * URL.
                 */
                $searchQuery->orWhere(
                    'blog_videos.url',
                    'like',
                    "%{$term}%"
                );

                /**
                 * Тип источника.
                 */
                $searchQuery->orWhere(
                    'blog_videos.source_type',
                    'like',
                    "%{$term}%"
                );

                /**
                 * Внешний ID / URL видео.
                 */
                $searchQuery->orWhere(
                    'blog_videos.external_video_id',
                    'like',
                    "%{$term}%"
                );

                /**
                 * Автор.
                 */
                $searchQuery->orWhereHas(
                    'owner',
                    fn (Builder $ownerQuery) =>
                    $ownerQuery->where(
                        'name',
                        'like',
                        "%{$term}%"
                    )
                );

                /**
                 * Эффективный перевод:
                 *
                 * current locale,
                 * либо fallback только если
                 * current translation отсутствует.
                 */
                $searchQuery->orWhere(
                    function (Builder $translationQuery) use (
                        $term,
                        $locale,
                        $fallbackLocale
                    ) {
                        $this->applyPublicTranslationSearch(
                            $translationQuery,
                            $term,
                            $locale,
                            $fallbackLocale
                        );
                    }
                );
            }
        );
    }

    /**
     * Поиск по эффективному переводу.
     */
    protected function applyPublicTranslationSearch(
        Builder $query,
        string $term,
        string $locale,
        string $fallbackLocale
    ): void {
        $query->whereHas(
            'translations',
            function (Builder $translationQuery) use (
                $term,
                $locale
            ) {
                $translationQuery
                    ->where(
                        'locale',
                        $locale
                    )
                    ->where(
                        function (Builder $fieldQuery) use ($term) {
                            $this->applyPublicTranslationFields(
                                $fieldQuery,
                                $term
                            );
                        }
                    );
            }
        );

        if ($locale === $fallbackLocale) {
            return;
        }

        $query->orWhere(
            function (Builder $fallbackQuery) use (
                $term,
                $locale,
                $fallbackLocale
            ) {
                $fallbackQuery
                    ->whereDoesntHave(
                        'translations',
                        function (Builder $currentTranslationQuery) use ($locale) {
                            $currentTranslationQuery->where(
                                'locale',
                                $locale
                            );
                        }
                    )
                    ->whereHas(
                        'translations',
                        function (Builder $translationQuery) use (
                            $term,
                            $fallbackLocale
                        ) {
                            $translationQuery
                                ->where(
                                    'locale',
                                    $fallbackLocale
                                )
                                ->where(
                                    function (Builder $fieldQuery) use ($term) {
                                        $this->applyPublicTranslationFields(
                                            $fieldQuery,
                                            $term
                                        );
                                    }
                                );
                        }
                    );
            }
        );
    }

    /**
     * Поля перевода для Public-поиска.
     */
    protected function applyPublicTranslationFields(
        Builder $query,
        string $term
    ): void {
        $query
            ->where(
                'title',
                'like',
                "%{$term}%"
            )
            ->orWhere(
                'short',
                'like',
                "%{$term}%"
            )
            ->orWhere(
                'pseudonym',
                'like',
                "%{$term}%"
            );
    }

    /* ======================== Admin / Common sort ======================== */

    /** Сортировка по параметру */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale
            ?: app()->getLocale();

        return match ($sort) {
            'idAsc' =>
            $query->orderBy(
                'id',
                'asc'
            ),

            'idDesc' =>
            $query->orderBy(
                'id',
                'desc'
            ),

            'sortAsc' =>
            $query
                ->orderBy(
                    'sort',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'sortDesc' =>
            $query
                ->orderBy(
                    'sort',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'urlAsc' =>
            $query
                ->orderBy(
                    'url',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'urlDesc' =>
            $query
                ->orderBy(
                    'url',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'sourceTypeAsc' =>
            $query
                ->orderBy(
                    'source_type',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'sourceTypeDesc' =>
            $query
                ->orderBy(
                    'source_type',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'publishedAtAsc' =>
            $query
                ->orderBy(
                    'published_at',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'publishedAtDesc' =>
            $query
                ->orderBy(
                    'published_at',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'showFromAtAsc' =>
            $query
                ->orderBy(
                    'show_from_at',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'showFromAtDesc' =>
            $query
                ->orderBy(
                    'show_from_at',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'showToAtAsc' =>
            $query
                ->orderBy(
                    'show_to_at',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'showToAtDesc' =>
            $query
                ->orderBy(
                    'show_to_at',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'createdAtAsc',
            'dateAsc' =>
            $query
                ->orderBy(
                    'created_at',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'createdAtDesc',
            'dateDesc' =>
            $query
                ->orderBy(
                    'created_at',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'updatedAtAsc' =>
            $query
                ->orderBy(
                    'updated_at',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'updatedAtDesc' =>
            $query
                ->orderBy(
                    'updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'viewsAsc' =>
            $query
                ->orderBy(
                    'views',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'viewsDesc' =>
            $query
                ->orderBy(
                    'views',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'likesAsc' =>
            $query
                ->withCount(
                    'likes'
                )
                ->orderBy(
                    'likes_count',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'likesDesc' =>
            $query
                ->withCount(
                    'likes'
                )
                ->orderBy(
                    'likes_count',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'commentsAsc' =>
            $query
                ->withCount(
                    'comments'
                )
                ->orderBy(
                    'comments_count',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'commentsDesc' =>
            $query
                ->withCount(
                    'comments'
                )
                ->orderBy(
                    'comments_count',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'durationAsc' =>
            $query
                ->orderBy(
                    'duration',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),

            'durationDesc' =>
            $query
                ->orderBy(
                    'duration',
                    'desc'
                )
                ->orderByDesc(
                    'id'
                ),

            'titleAsc' =>
            $query
                ->leftJoin(
                    'blog_video_translations as bvt_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'bvt_sort.video_id',
                                '=',
                                'blog_videos.id'
                            )
                            ->where(
                                'bvt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->orderBy(
                    'bvt_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'blog_videos.id'
                )
                ->addSelect(
                    'blog_videos.*'
                ),

            'titleDesc' =>
            $query
                ->leftJoin(
                    'blog_video_translations as bvt_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'bvt_sort.video_id',
                                '=',
                                'blog_videos.id'
                            )
                            ->where(
                                'bvt_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->orderBy(
                    'bvt_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'blog_videos.id'
                )
                ->addSelect(
                    'blog_videos.*'
                ),

            default =>
            $query
                ->orderBy(
                    'sort',
                    'asc'
                )
                ->orderByDesc(
                    'id'
                ),
        };
    }

    /* ======================== Public sort ======================== */

    /**
     * Public-сортировка.
     *
     * Только параметры,
     * используемые Public Index.
     *
     * likes_count / comments_count
     * должны быть добавлены контроллером.
     */
    public function scopePublicSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale
            ?: app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        return match ($sort) {
            'sortAsc' =>
            $query
                ->orderBy(
                    'blog_videos.sort',
                    'asc'
                )
                ->orderBy(
                    'blog_videos.id',
                    'asc'
                ),

            'sortDesc' =>
            $query
                ->orderBy(
                    'blog_videos.sort',
                    'desc'
                )
                ->orderBy(
                    'blog_videos.id',
                    'desc'
                ),

            'titleAsc' =>
            $this->applyPublicTitleSort(
                $query,
                $locale,
                $fallbackLocale,
                'asc'
            ),

            'titleDesc' =>
            $this->applyPublicTitleSort(
                $query,
                $locale,
                $fallbackLocale,
                'desc'
            ),

            'viewsAsc' =>
            $query
                ->orderBy(
                    'blog_videos.views',
                    'asc'
                )
                ->orderByDesc(
                    'blog_videos.id'
                ),

            'viewsDesc' =>
            $query
                ->orderBy(
                    'blog_videos.views',
                    'desc'
                )
                ->orderByDesc(
                    'blog_videos.id'
                ),

            'likesAsc' =>
            $query
                ->orderBy(
                    'likes_count',
                    'asc'
                )
                ->orderByDesc(
                    'blog_videos.id'
                ),

            'likesDesc' =>
            $query
                ->orderBy(
                    'likes_count',
                    'desc'
                )
                ->orderByDesc(
                    'blog_videos.id'
                ),

            'commentsAsc' =>
            $query
                ->orderBy(
                    'comments_count',
                    'asc'
                )
                ->orderByDesc(
                    'blog_videos.id'
                ),

            'commentsDesc' =>
            $query
                ->orderBy(
                    'comments_count',
                    'desc'
                )
                ->orderByDesc(
                    'blog_videos.id'
                ),

            'durationAsc' =>
            $query
                ->orderBy(
                    'blog_videos.duration',
                    'asc'
                )
                ->orderByDesc(
                    'blog_videos.id'
                ),

            'durationDesc' =>
            $query
                ->orderBy(
                    'blog_videos.duration',
                    'desc'
                )
                ->orderByDesc(
                    'blog_videos.id'
                ),

            'publishedAtAsc' =>
            $query
                ->orderByRaw(
                    'COALESCE(
                        blog_videos.published_at,
                        blog_videos.created_at
                    ) ASC'
                )
                ->orderByDesc(
                    'blog_videos.id'
                ),

            'publishedAtDesc' =>
            $query
                ->orderByRaw(
                    'COALESCE(
                        blog_videos.published_at,
                        blog_videos.created_at
                    ) DESC'
                )
                ->orderByDesc(
                    'blog_videos.id'
                ),

            default =>
            $query
                ->orderBy(
                    'blog_videos.sort',
                    'asc'
                )
                ->orderBy(
                    'blog_videos.id',
                    'asc'
                ),
        };
    }

    /**
     * Public-сортировка по title:
     *
     * current locale -> fallback.
     */
    protected function applyPublicTitleSort(
        Builder $query,
        string $locale,
        string $fallbackLocale,
        string $direction
    ): Builder {
        $direction = strtolower($direction) === 'desc'
            ? 'desc'
            : 'asc';

        $query->leftJoin(
            'blog_video_translations as bvt_public_current',
            function ($join) use ($locale) {
                $join
                    ->on(
                        'bvt_public_current.video_id',
                        '=',
                        'blog_videos.id'
                    )
                    ->where(
                        'bvt_public_current.locale',
                        '=',
                        $locale
                    );
            }
        );

        if ($locale === $fallbackLocale) {
            return $query
                ->orderBy(
                    'bvt_public_current.title',
                    $direction
                )
                ->orderByDesc(
                    'blog_videos.id'
                )
                ->addSelect(
                    'blog_videos.*'
                );
        }

        $query->leftJoin(
            'blog_video_translations as bvt_public_fallback',
            function ($join) use ($fallbackLocale) {
                $join
                    ->on(
                        'bvt_public_fallback.video_id',
                        '=',
                        'blog_videos.id'
                    )
                    ->where(
                        'bvt_public_fallback.locale',
                        '=',
                        $fallbackLocale
                    );
            }
        );

        return $query
            ->orderByRaw(
                '
                    CASE
                        WHEN bvt_public_current.id IS NOT NULL
                            THEN bvt_public_current.title
                        ELSE bvt_public_fallback.title
                    END '
                . strtoupper($direction)
            )
            ->orderByDesc(
                'blog_videos.id'
            )
            ->addSelect(
                'blog_videos.*'
            );
    }

    /* ======================== MODEL EVENTS ======================== */

    protected static function booted(): void
    {
        static::saved(
            function (BlogVideo $video) {
                Log::info(
                    'Видео блога сохранено: '
                    . $video->id
                    . ' / '
                    . $video->url
                );
            }
        );

        static::deleted(
            function (BlogVideo $video) {
                Log::info(
                    'Видео блога удалено: '
                    . $video->id
                    . ' / '
                    . $video->url
                );
            }
        );
    }
}
