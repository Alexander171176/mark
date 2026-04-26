<?php

namespace App\Models\Admin\Blog\BlogTag;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    use HasFactory;

    protected $table = 'blog_tags';

    protected $fillable = [
        'user_id',

        'sort',
        'activity',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'icon',
        'slug',
        'views',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'sort' => 'integer',
        'activity' => 'boolean',
        'views' => 'integer',

        'moderation_status' => 'integer',
        'moderated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Владелец тега */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Модератор тега */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Переводы тега */
    public function translations(): HasMany
    {
        return $this->hasMany(BlogTagTranslation::class, 'tag_id');
    }

    /** Текущий перевод по locale */
    public function translation(?string $locale = null): ?BlogTagTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first();
    }

    /** Перевод с fallback */
    public function translationOrFallback(?string $locale = null, string $fallback = 'ru'): ?BlogTagTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }

    /** Статьи, связанные с тегом */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogArticle::class,
            'blog_article_has_tag',
            'tag_id',
            'article_id'
        )
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    /* ======================== MODEL EVENTS ======================== */

    protected static function booted(): void
    {
        static::saving(function (BlogTag $tag) {
            if (empty($tag->slug)) {
                $translatedName = $tag->translationOrFallback()?->name;

                if (!empty($translatedName)) {
                    $tag->slug = Str::slug($translatedName);
                }
            }
        });

        static::saved(function (BlogTag $tag) {
            Log::info('Tag saved: ' . $tag->id . ' / ' . $tag->slug);
        });

        static::deleted(function (BlogTag $tag) {
            Log::info('Tag deleted: ' . $tag->id . ' / ' . $tag->slug);
        });
    }

    /* ======================== HELPERS ======================== */

    /** Активен ли тег */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Прошёл ли модерацию */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Получить name из текущего перевода */
    public function getTranslatedName(?string $locale = null, string $fallback = 'ru'): ?string
    {
        return $this->translationOrFallback($locale, $fallback)?->name;
    }

    /* ======================== Scopes ======================== */

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $query
            ->leftJoin('blog_tag_translations as btt_order', function ($join) use ($locale) {
                $join->on('btt_order.tag_id', '=', 'blog_tags.id')
                    ->where('btt_order.locale', '=', $locale);
            })
            ->orderBy('blog_tags.sort', 'asc')
            ->orderBy('btt_order.name', 'asc')
            ->select('blog_tags.*');
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

    /** Популярные */
    public function scopePopular(Builder $query, int $minViews = 0): Builder
    {
        return $query
            ->when($minViews > 0, fn (Builder $q) => $q->where('views', '>=', $minViews))
            ->orderByDesc('views');
    }

    /** Публичный набор */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->active();
    }

    /** Поиск по переводимым полям и slug */
    public function scopeSearch(Builder $query, ?string $term = null, ?string $locale = null): Builder
    {
        if (!$term) {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();
        $term = trim($term);

        return $query->where(function (Builder $q) use ($term, $locale) {
            $q->where('slug', 'like', "%{$term}%")
                ->orWhereHas('translations', function (Builder $tq) use ($term, $locale) {
                    $tq->where('locale', $locale)
                        ->where(function (Builder $sq) use ($term) {
                            $sq->where('name', 'like', "%{$term}%")
                                ->orWhere('subtitle', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%")
                                ->orWhere('description', 'like', "%{$term}%");
                        });
                });
        });
    }

    /** По slug */
    public function scopeWhereSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /** Набор для облака тегов */
    public function scopeForTagCloud(Builder $query, int $minViews = 0, ?string $locale = null): Builder
    {
        return $query
            ->forPublic()
            ->popular($minViews)
            ->ordered($locale);
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

            'name_asc' => $query
                ->leftJoin('blog_tag_translations as btt_sort', function ($join) use ($locale) {
                    $join->on('btt_sort.tag_id', '=', 'blog_tags.id')
                        ->where('btt_sort.locale', '=', $locale);
                })
                ->orderBy('btt_sort.name', 'asc')
                ->orderByDesc('blog_tags.id')
                ->select('blog_tags.*'),

            'name_desc' => $query
                ->leftJoin('blog_tag_translations as btt_sort', function ($join) use ($locale) {
                    $join->on('btt_sort.tag_id', '=', 'blog_tags.id')
                        ->where('btt_sort.locale', '=', $locale);
                })
                ->orderBy('btt_sort.name', 'desc')
                ->orderByDesc('blog_tags.id')
                ->select('blog_tags.*'),

            default => $query->ordered($locale),
        };
    }
}
