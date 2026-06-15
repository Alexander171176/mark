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

    /** Поиск по словам */
    public function scopeSearch(Builder $query, ?string $term, ?string $locale = null): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $locale = $locale ?: app()->getLocale();

        $words = collect(preg_split('/[\s:#№,"\'«»(){}\[\].!?\/\\\\|;+=*&^%$@<>`~_-]+/u', $term))
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => mb_strlen($word) >= 2)
            ->values();

        if ($words->isEmpty()) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($words, $locale) {
            foreach ($words as $word) {
                $query->where(function (Builder $query) use ($word, $locale) {
                    $query
                        ->where('blog_rubrics.url', 'like', "%{$word}%")
                        ->orWhere('blog_rubrics.icon', 'like', "%{$word}%")
                        ->orWhere('blog_rubrics.views', 'like', "%{$word}%")
                        ->orWhere('blog_rubrics.moderation_note', 'like', "%{$word}%")

                        ->orWhereHas('translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('subtitle', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('description', 'like', "%{$word}%")
                                        ->orWhere('meta_title', 'like', "%{$word}%")
                                        ->orWhere('meta_keywords', 'like', "%{$word}%")
                                        ->orWhere('meta_desc', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('parent.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('subtitle', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('owner', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%")
                                ->orWhere('email', 'like', "%{$word}%");
                        })

                        ->orWhereHas('moderator', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%")
                                ->orWhere('email', 'like', "%{$word}%");
                        });
                });
            }
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $query, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $query->orderBy('blog_rubrics.id', 'asc'),
            'idDesc' => $query->orderBy('blog_rubrics.id', 'desc'),

            'sortAsc' => $query->orderBy('blog_rubrics.sort', 'asc')->orderByDesc('blog_rubrics.id'),
            'sortDesc' => $query->orderBy('blog_rubrics.sort', 'desc')->orderByDesc('blog_rubrics.id'),

            'levelAsc' => $query->orderBy('blog_rubrics.level', 'asc')->orderByDesc('blog_rubrics.id'),
            'levelDesc' => $query->orderBy('blog_rubrics.level', 'desc')->orderByDesc('blog_rubrics.id'),

            'parentAsc' => $query->orderBy('blog_rubrics.parent_id', 'asc')->orderByDesc('blog_rubrics.id'),
            'parentDesc' => $query->orderBy('blog_rubrics.parent_id', 'desc')->orderByDesc('blog_rubrics.id'),

            'urlAsc' => $query->orderBy('blog_rubrics.url', 'asc')->orderByDesc('blog_rubrics.id'),
            'urlDesc' => $query->orderBy('blog_rubrics.url', 'desc')->orderByDesc('blog_rubrics.id'),

            'viewsAsc' => $query->orderBy('blog_rubrics.views', 'asc')->orderByDesc('blog_rubrics.id'),
            'viewsDesc' => $query->orderBy('blog_rubrics.views', 'desc')->orderByDesc('blog_rubrics.id'),

            'activityAsc' => $query->orderBy('blog_rubrics.activity', 'asc')->orderByDesc('blog_rubrics.id'),
            'activityDesc' => $query->orderBy('blog_rubrics.activity', 'desc')->orderByDesc('blog_rubrics.id'),
            'activity' => $query->where('blog_rubrics.activity', true)->orderByDesc('blog_rubrics.id'),
            'inactive' => $query->where('blog_rubrics.activity', false)->orderByDesc('blog_rubrics.id'),

            'inMenuAsc' => $query->orderBy('blog_rubrics.in_menu', 'asc')->orderByDesc('blog_rubrics.id'),
            'inMenuDesc' => $query->orderBy('blog_rubrics.in_menu', 'desc')->orderByDesc('blog_rubrics.id'),
            'inMenu' => $query->where('blog_rubrics.in_menu', true)->orderByDesc('blog_rubrics.id'),
            'notInMenu' => $query->where('blog_rubrics.in_menu', false)->orderByDesc('blog_rubrics.id'),

            'articlesAsc' => $query->withCount('articles')->orderBy('articles_count', 'asc')->orderByDesc('blog_rubrics.id'),
            'articlesDesc' => $query->withCount('articles')->orderBy('articles_count', 'desc')->orderByDesc('blog_rubrics.id'),

            'imagesAsc' => $query->withCount('images')->orderBy('images_count', 'asc')->orderByDesc('blog_rubrics.id'),
            'imagesDesc' => $query->withCount('images')->orderBy('images_count', 'desc')->orderByDesc('blog_rubrics.id'),

            'moderationStatusAsc' => $query->orderBy('blog_rubrics.moderation_status', 'asc')->orderByDesc('blog_rubrics.id'),
            'moderationStatusDesc' => $query->orderBy('blog_rubrics.moderation_status', 'desc')->orderByDesc('blog_rubrics.id'),
            'moderationPending' => $query->where('blog_rubrics.moderation_status', 0)->orderByDesc('blog_rubrics.id'),
            'moderationApproved' => $query->where('blog_rubrics.moderation_status', 1)->orderByDesc('blog_rubrics.id'),
            'moderationRejected' => $query->where('blog_rubrics.moderation_status', 2)->orderByDesc('blog_rubrics.id'),

            'createdAtAsc' => $query->orderBy('blog_rubrics.created_at', 'asc')->orderByDesc('blog_rubrics.id'),
            'createdAtDesc' => $query->orderBy('blog_rubrics.created_at', 'desc')->orderByDesc('blog_rubrics.id'),

            'updatedAtAsc' => $query->orderBy('blog_rubrics.updated_at', 'asc')->orderByDesc('blog_rubrics.id'),
            'updatedAtDesc' => $query->orderBy('blog_rubrics.updated_at', 'desc')->orderByDesc('blog_rubrics.id'),

            'titleAsc' => $query
                ->leftJoin('blog_rubric_translations as brt_sort', function ($join) use ($locale) {
                    $join->on('brt_sort.rubric_id', '=', 'blog_rubrics.id')
                        ->where('brt_sort.locale', '=', $locale);
                })
                ->orderBy('brt_sort.title', 'asc')
                ->orderByDesc('blog_rubrics.id')
                ->select('blog_rubrics.*'),

            'titleDesc' => $query
                ->leftJoin('blog_rubric_translations as brt_sort', function ($join) use ($locale) {
                    $join->on('brt_sort.rubric_id', '=', 'blog_rubrics.id')
                        ->where('brt_sort.locale', '=', $locale);
                })
                ->orderBy('brt_sort.title', 'desc')
                ->orderByDesc('blog_rubrics.id')
                ->select('blog_rubrics.*'),

            'ownerNameAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'blog_rubrics.user_id')
                ->orderBy('owner_sort.name', 'asc')
                ->orderByDesc('blog_rubrics.id')
                ->select('blog_rubrics.*'),

            'ownerNameDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'blog_rubrics.user_id')
                ->orderBy('owner_sort.name', 'desc')
                ->orderByDesc('blog_rubrics.id')
                ->select('blog_rubrics.*'),

            'ownerEmailAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'blog_rubrics.user_id')
                ->orderBy('owner_sort.email', 'asc')
                ->orderByDesc('blog_rubrics.id')
                ->select('blog_rubrics.*'),

            'ownerEmailDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'blog_rubrics.user_id')
                ->orderBy('owner_sort.email', 'desc')
                ->orderByDesc('blog_rubrics.id')
                ->select('blog_rubrics.*'),

            default => $query->ordered(),
        };
    }
}
