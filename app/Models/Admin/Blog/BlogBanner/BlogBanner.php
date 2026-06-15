<?php

namespace App\Models\Admin\Blog\BlogBanner;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class BlogBanner extends Model
{
    use HasFactory;

    protected $table = 'blog_banners';

    protected $fillable = [
        'user_id',

        'sort',
        'activity',
        'left',
        'main',
        'right',

        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',

        'comment',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'user_id' => 'integer',

        'sort' => 'integer',
        'activity' => 'boolean',
        'left' => 'boolean',
        'main' => 'boolean',
        'right' => 'boolean',

        'moderation_status' => 'integer',
        'moderated_by' => 'integer',
        'moderated_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Владелец баннера */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Модератор баннера */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Переводы баннера */
    public function translations(): HasMany
    {
        return $this->hasMany(BlogBannerTranslation::class, 'banner_id');
    }

    /** Текущий перевод по locale */
    public function translation(?string $locale = null): ?BlogBannerTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->first();
    }

    /** Перевод с fallback */
    public function translationOrFallback(?string $locale = null, string $fallback = 'ru'): ?BlogBannerTranslation
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations->firstWhere('locale', $locale)
            ?: $this->translations->firstWhere('locale', $fallback)
                ?: $this->translations->first();
    }

    /** Изображения баннера */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogBannerImage::class,
            'blog_banner_has_images',
            'banner_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }

    /* ======================== HELPERS ======================== */

    /** Активен ли баннер */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Одобрен ли баннер */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Если в левом блоке */
    public function isLeft(): bool
    {
        return (bool) $this->left;
    }

    /** Если в главном блоке */
    public function isMain(): bool
    {
        return (bool) $this->main;
    }

    /** Если в правом блоке */
    public function isRight(): bool
    {
        return (bool) $this->right;
    }

    /** Получить title из текущего перевода */
    public function getTranslatedTitle(?string $locale = null, string $fallback = 'ru'): ?string
    {
        return $this->translationOrFallback($locale, $fallback)?->title;
    }

    /* ======================== Scopes ======================== */

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc');
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

    /** Левый блок */
    public function scopeLeft(Builder $query): Builder
    {
        return $query->where('left', true);
    }

    /** Главный блок */
    public function scopeMain(Builder $query): Builder
    {
        return $query->where('main', true);
    }

    /** Правый блок */
    public function scopeRight(Builder $query): Builder
    {
        return $query->where('right', true);
    }

    /** Позиция: left|main|right|any */
    public function scopeWherePosition(Builder $query, string $position): Builder
    {
        return match ($position) {
            'left'  => $query->left(),
            'main'  => $query->main(),
            'right' => $query->right(),
            default => $query,
        };
    }

    /** Публичный набор */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->active();
    }

    /** С картинками */
    public function scopeWithImages(Builder $query): Builder
    {
        return $query->with('images');
    }

    /** Готовый набор для фронта */
    public function scopeForFrontend(Builder $query): Builder
    {
        return $query
            ->forPublic()
            ->withImages()
            ->ordered();
    }

    /** Поиск по словам */
    public function scopeSearch(Builder $query, ?string $term = null, ?string $locale = null): Builder
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
                        ->where('blog_banners.comment', 'like', "%{$word}%")
                        ->orWhere('blog_banners.moderation_note', 'like', "%{$word}%")

                        ->orWhereHas('translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('link', 'like', "%{$word}%");
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
            'idAsc' => $query->orderBy('blog_banners.id', 'asc'),
            'idDesc' => $query->orderBy('blog_banners.id', 'desc'),

            'sortAsc' => $query->orderBy('blog_banners.sort', 'asc')->orderBy('blog_banners.id', 'asc'),
            'sortDesc' => $query->orderBy('blog_banners.sort', 'desc')->orderByDesc('blog_banners.id'),

            'titleAsc' => $query
                ->leftJoin('blog_banner_translations as bbt_sort', function ($join) use ($locale) {
                    $join->on('bbt_sort.banner_id', '=', 'blog_banners.id')
                        ->where('bbt_sort.locale', '=', $locale);
                })
                ->orderBy('bbt_sort.title', 'asc')
                ->orderBy('blog_banners.id', 'asc')
                ->select('blog_banners.*'),

            'titleDesc' => $query
                ->leftJoin('blog_banner_translations as bbt_sort', function ($join) use ($locale) {
                    $join->on('bbt_sort.banner_id', '=', 'blog_banners.id')
                        ->where('bbt_sort.locale', '=', $locale);
                })
                ->orderBy('bbt_sort.title', 'desc')
                ->orderByDesc('blog_banners.id')
                ->select('blog_banners.*'),

            'ownerNameAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'blog_banners.user_id')
                ->orderBy('owner_sort.name', 'asc')
                ->orderByDesc('blog_banners.id')
                ->select('blog_banners.*'),

            'ownerNameDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'blog_banners.user_id')
                ->orderBy('owner_sort.name', 'desc')
                ->orderByDesc('blog_banners.id')
                ->select('blog_banners.*'),

            'ownerEmailAsc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'blog_banners.user_id')
                ->orderBy('owner_sort.email', 'asc')
                ->orderByDesc('blog_banners.id')
                ->select('blog_banners.*'),

            'ownerEmailDesc' => $query
                ->leftJoin('users as owner_sort', 'owner_sort.id', '=', 'blog_banners.user_id')
                ->orderBy('owner_sort.email', 'desc')
                ->orderByDesc('blog_banners.id')
                ->select('blog_banners.*'),

            'imagesAsc' => $query->withCount('images')->orderBy('images_count', 'asc')->orderByDesc('blog_banners.id'),
            'imagesDesc' => $query->withCount('images')->orderBy('images_count', 'desc')->orderByDesc('blog_banners.id'),

            'activityAsc' => $query->orderBy('blog_banners.activity', 'asc')->orderByDesc('blog_banners.id'),
            'activityDesc' => $query->orderBy('blog_banners.activity', 'desc')->orderByDesc('blog_banners.id'),
            'activity' => $query->where('blog_banners.activity', true)->orderByDesc('blog_banners.id'),
            'inactive' => $query->where('blog_banners.activity', false)->orderByDesc('blog_banners.id'),

            'leftAsc' => $query->orderBy('blog_banners.left', 'asc')->orderByDesc('blog_banners.id'),
            'leftDesc' => $query->orderBy('blog_banners.left', 'desc')->orderByDesc('blog_banners.id'),
            'left' => $query->where('blog_banners.left', true)->orderByDesc('blog_banners.id'),
            'noLeft' => $query->where('blog_banners.left', false)->orderByDesc('blog_banners.id'),

            'mainAsc' => $query->orderBy('blog_banners.main', 'asc')->orderByDesc('blog_banners.id'),
            'mainDesc' => $query->orderBy('blog_banners.main', 'desc')->orderByDesc('blog_banners.id'),
            'main' => $query->where('blog_banners.main', true)->orderByDesc('blog_banners.id'),
            'noMain' => $query->where('blog_banners.main', false)->orderByDesc('blog_banners.id'),

            'rightAsc' => $query->orderBy('blog_banners.right', 'asc')->orderByDesc('blog_banners.id'),
            'rightDesc' => $query->orderBy('blog_banners.right', 'desc')->orderByDesc('blog_banners.id'),
            'right' => $query->where('blog_banners.right', true)->orderByDesc('blog_banners.id'),
            'noRight' => $query->where('blog_banners.right', false)->orderByDesc('blog_banners.id'),

            'createdAtAsc' => $query->orderBy('blog_banners.created_at', 'asc')->orderByDesc('blog_banners.id'),
            'createdAtDesc' => $query->orderBy('blog_banners.created_at', 'desc')->orderByDesc('blog_banners.id'),

            'updatedAtAsc' => $query->orderBy('blog_banners.updated_at', 'asc')->orderByDesc('blog_banners.id'),
            'updatedAtDesc' => $query->orderBy('blog_banners.updated_at', 'desc')->orderByDesc('blog_banners.id'),

            'moderationStatusAsc' => $query->orderBy('blog_banners.moderation_status', 'asc')->orderByDesc('blog_banners.id'),
            'moderationStatusDesc' => $query->orderBy('blog_banners.moderation_status', 'desc')->orderByDesc('blog_banners.id'),
            'moderationPending' => $query->where('blog_banners.moderation_status', 0)->orderByDesc('blog_banners.id'),
            'moderationApproved' => $query->where('blog_banners.moderation_status', 1)->orderByDesc('blog_banners.id'),
            'moderationRejected' => $query->where('blog_banners.moderation_status', 2)->orderByDesc('blog_banners.id'),

            default => $query->ordered(),
        };
    }

    /* ======================== MODEL EVENTS ======================== */

    protected static function booted(): void
    {
        static::saved(function (BlogBanner $banner) {
            Log::info('Banner saved: ' . $banner->id);
        });

        static::deleted(function (BlogBanner $banner) {
            Log::info('Banner deleted: ' . $banner->id);
        });
    }
}
