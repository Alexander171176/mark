<?php

namespace App\Models\Admin\Blog\BlogRubric;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class BlogRubric extends Model
{
    use HasFactory;

    protected $table = 'blog_rubrics';

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
        'url',
        'views',
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

    /** Модератор рубрики */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Родительская рубрика */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** Дочерние рубрики */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort')
            ->orderByDesc('id');
    }

    /** Рекурсивная загрузка дочерних рубрик */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with([
            'owner',
            'images',
            'translations',
            'childrenRecursive',
        ]);
    }

    /** Переводы рубрики */
    public function translations(): HasMany
    {
        return $this->hasMany(BlogRubricTranslation::class, 'rubric_id');
    }

    /** Текущий перевод по locale */
    public function translation(?string $locale = null): ?BlogRubricTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first();
    }

    /** Перевод с fallback */
    public function translationOrFallback(?string $locale = null, string $fallback = 'ru'): ?BlogRubricTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }

    /**
     * Изображения рубрики (many-to-many) + сортировка через pivot.order
     * Таблицы: blog_rubric_images, blog_rubric_has_images
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogRubricImage::class,
            'blog_rubric_has_images',
            'rubric_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /** Статьи, привязанные к рубрике */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogArticle::class,
            'blog_article_has_rubric',
            'rubric_id',
            'article_id'
        );
    }

    /* ======================== MODEL EVENTS ======================== */

    protected static function booted(): void
    {
        static::saved(function (BlogRubric $rubric) {
            Log::info('Рубрика блога сохранена: ' . $rubric->id . ' / ' . $rubric->url);
        });

        static::deleted(function (BlogRubric $rubric) {
            Log::info('Рубрика блога удалена: ' . $rubric->id . ' / ' . $rubric->url);
        });
    }

    /* ======================== HELPERS ======================== */

    /** Активна ли рубрика */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Одобрена ли рубрика */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Получить title из текущего перевода */
    public function getTranslatedTitle(?string $locale = null, string $fallback = 'ru'): ?string
    {
        return $this->translationOrFallback($locale, $fallback)?->title;
    }

    /* ======================== Scopes ======================== */

    /** Сортировка: sort ↑ затем id ↓ */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderByDesc('id');
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

    /** Только родительские */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** Показ в меню */
    public function scopeInMenu(Builder $query): Builder
    {
        return $query->where('in_menu', true);
    }

    /** Публичный scope */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->active();
    }

    /** Корневые рубрики */
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    /** Для меню */
    public function scopeForMenu(Builder $query): Builder
    {
        return $query
            ->forPublic()
            ->inMenu()
            ->ordered();
    }

    /**
     * Поиск по переводимым полям и url
     */
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
                                ->orWhere('description', 'like', "%{$term}%");
                        });
                });
        });
    }

    /**
     * Сортировка по параметру
     * По title сортируем через translations текущей локали
     */
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

            'title_asc' => $query
                ->leftJoin('blog_rubric_translations as brt_sort', function ($join) use ($locale) {
                    $join->on('brt_sort.rubric_id', '=', 'blog_rubrics.id')
                        ->where('brt_sort.locale', '=', $locale);
                })
                ->orderBy('brt_sort.title', 'asc')
                ->orderByDesc('blog_rubrics.id')
                ->select('blog_rubrics.*'),

            'title_desc' => $query
                ->leftJoin('blog_rubric_translations as brt_sort', function ($join) use ($locale) {
                    $join->on('brt_sort.rubric_id', '=', 'blog_rubrics.id')
                        ->where('brt_sort.locale', '=', $locale);
                })
                ->orderBy('brt_sort.title', 'desc')
                ->orderByDesc('blog_rubrics.id')
                ->select('blog_rubrics.*'),

            default => $query->ordered(),
        };
    }
}
