<?php

namespace App\Models\Admin\Blog\Tag;

use App\Models\Admin\Blog\Article\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = [
        'user_id',

        'sort',
        'activity',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'icon',
        'locale',
        'name',
        'slug',
        'subtitle',
        'short',
        'description',
        'views',

        'meta_title',
        'meta_keywords',
        'meta_desc',
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

    /** модератор тега */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Статьи, связанные с тегом (many-to-many) */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            Article::class,
            'article_has_tag',
            'tag_id',
            'article_id'
        )
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    /* ======================== MODEL EVENTS ======================== */

    protected static function booted(): void
    {
        static::saved(function (Tag $tag) {
            Log::info('Tag saved: '.$tag->id.' / '.$tag->name);
        });

        static::deleted(function (Tag $tag) {
            Log::info('Tag deleted: '.$tag->id.' / '.$tag->name);
        });

        static::saving(function (Tag $tag) {
            if (empty($tag->slug) && !empty($tag->name)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /* ======================== HELPERS ======================== */

    /** Если активные */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Если прошли модерацию */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /* ======================== Scopes ======================== */

    /** Сортировка: по sort ↑ затем по id ↓ */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('name');
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

    /** Популярные */
    public function scopePopular(Builder $query, int $minViews = 0): Builder
    {
        return $query
            ->when($minViews > 0, fn (Builder $q) => $q->where('views', '>=', $minViews))
            ->orderByDesc('views');
    }

    /**
     * Публичный набор: approved + active + locale
     */
    public function scopeForPublic(Builder $query, ?string $locale = null): Builder
    {
        return $query
            ->approved()
            ->active()
            ->byLocale($locale);
    }

    /** Поиск */
    public function scopeSearch(Builder $query, ?string $term = null): Builder
    {
        if (! $term) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('subtitle', 'like', "%{$term}%")
                ->orWhere('short', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /** slug */
    public function scopeWhereSlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /** Группа тегов (облако тегов) */
    public function scopeForTagCloud(Builder $query, ?string $locale = null, int $minViews = 0): Builder
    {
        return $query
            ->forPublic($locale)
            ->popular($minViews)
            ->ordered();
    }
}
