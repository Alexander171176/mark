<?php

namespace App\Models\Admin\Blog\Rubric;

use App\Models\Admin\Blog\Article\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Rubric extends Model
{
    use HasFactory;

    protected $table = 'rubrics';

    protected $fillable = [
        'user_id',
        'parent_id',
        'level',
        'in_menu',
        'sort',
        'activity',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'icon',
        'locale',
        'title',
        'url',
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
        'in_menu' => 'boolean',
        'level' => 'integer',
        'views' => 'integer',

        'moderation_status' => 'integer',
        'moderated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Владелец рубрики */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** модератор рубрики */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Родительская рубрика */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Дочерняя рубрика */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort')->orderBy('title');
    }

    /**
     * Рекурсивная связь для загрузки всех дочерних элементов (опционально).
     * Используйте с осторожностью для глубоких деревьев из-за производительности.
     * Лучше использовать ->with('children', 'children.children', ...) или специализированные запросы.
     *
     * @return HasMany
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with([
            'owner',
            'images',
            'childrenRecursive',
        ]);
    }

    /**
     * Изображения рубрики (many-to-many) + сортировка через pivot.order
     * Таблицы: rubric_images, rubric_has_images
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            RubricImage::class,
            'rubric_has_images',
            'rubric_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /** Статьи привязанные к рубрике */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            Article::class,
            'article_has_rubric',
            'rubric_id',
            'article_id'
        );
    }

    /* ======================== MODEL EVENTS ======================== */
    protected static function booted(): void
    {
        static::saved(function (Rubric $rubric) {
            Log::info('Рубрика сохранена: '.$rubric->id.' / '.$rubric->title);
        });

        static::deleted(function (Rubric $rubric) {
            Log::info('Рубрика удалена: '.$rubric->id.' / '.$rubric->title);
        });
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

    /** Сортировка: по sort ↑ затем по id ↓ */
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

    /**
     * Только одобренные
     * 0=pending, 1=approved, 2=rejected
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 1);
    }

    /** Только родители */
    public function scopeRoot(Builder $q): Builder
    {
        return $q->whereNull('parent_id');
    }

    /** В меню */
    public function scopeInMenu(Builder $query): Builder
    {
        return $query->where('in_menu', true);
    }

    /**
     * Публичный скоуп: активные + одобренные + локаль
     */
    public function scopeForPublic(Builder $query, ?string $locale = null): Builder
    {
        return $query
            ->approved()
            ->active()
            ->byLocale($locale);
    }

    /**
     * Корневые рубрики (для дерева)
     * Корень у тебя = parent_id NULL, level=1
     */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Готовый скоуп для меню: публичные + in_menu + корневые/любые (по необходимости) + сортировка
     */
    public function scopeForMenu(Builder $query, ?string $locale = null): Builder
    {
        return $query
            ->forPublic($locale)
            ->inMenu()
            ->ordered();
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
                ->orWhere('url', 'like', "%{$term}%");
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'sort_asc'   => $query->orderBy('sort', 'asc')->orderBy('title'),
            'sort_desc'  => $query->orderBy('sort', 'desc')->orderBy('title'),
            'date_asc'   => $query->orderBy('created_at', 'asc')->orderByDesc('id'),
            'date_desc'  => $query->orderBy('created_at', 'desc')->orderByDesc('id'),
            'views_asc'  => $query->orderBy('views', 'asc')->orderByDesc('id'),
            'views_desc' => $query->orderBy('views', 'desc')->orderByDesc('id'),
            'title_asc'  => $query->orderBy('title', 'asc')->orderByDesc('id'),
            'title_desc' => $query->orderBy('title', 'desc')->orderByDesc('id'),
            default      => $query->ordered(),
        };
    }
}
