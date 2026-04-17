<?php

namespace App\Models\Admin\School\Lesson;

use App\Models\Admin\School\Hashtag\Hashtag;
use App\Models\Admin\School\Module\Module;
use App\Models\User\Like\LessonLike;
use App\Traits\HashtagsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Урок внутри модуля курса.
 *
 * Основное содержимое урока хранится через полиморфную связь:
 *  - content_type: FQCN модели (Article, Video, Quiz, Assignment, Live и т.п.)
 *  - content_id:   PK соответствующей таблицы
 *
 * При этом сам Lesson хранит «обёртку»:
 *  - заголовки, описания, мета
 *  - статус/доступность/превью
 *  - денормы: views, likes, rating и т.д.
 *
 * @property-read Collection|Hashtag[] $hashtags
 */
class Lesson extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HashtagsTrait;

    protected $table = 'lessons';

    /**
     * Массово заполняемые поля — строго по миграции.
     */
    protected $fillable = [
        'module_id',

        'sort',
        'activity',
        'locale',

        'title',
        'slug',
        'subtitle',
        'short',
        'description',

        'meta_title',
        'meta_keywords',
        'meta_desc',

        'content_type',
        'content_id',

        'published_at',
        'status',
        'availability',

        'access_type',
        'difficulty',
        'duration',

        'preview_mode',
        'preview_value',

        'popularity',
        'rating_count',
        'rating_avg',
        'views',
        'likes',
    ];

    /**
     * Касты под новые поля.
     */
    protected $casts = [
        'module_id'     => 'int',
        'sort'          => 'int',
        'activity'      => 'bool',
        'difficulty'    => 'int',
        'duration'      => 'int',
        'preview_value' => 'int',

        'popularity'    => 'int',
        'rating_count'  => 'int',
        'rating_avg'    => 'float',
        'views'         => 'int',
        'likes'         => 'int',

        'published_at'  => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /**
     * Родительский модуль.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Полиморфный контент урока:
     * Article, Video, Quiz, Assignment, Live и т.д.
     */
    public function content(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Изображения, привязанные к уроку через pivot lesson_has_images.
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            LessonImage::class,
            'lesson_has_images',
            'lesson_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('lesson_has_images.order', 'asc');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(LessonLike::class, 'lesson_id');
    }
    /* ======================== Media (Spatie) ======================== */

    /**
     * Медиа для урока: доп. материалы, вложения, PDF и т.п.
     * Основное содержимое (видео, статья и т.д.) живёт в связанных моделях
     * Article / Video / Quiz / Assignment и т.п.
     */
    public function registerMediaCollections(): void
    {
        // Общая коллекция вложений для урока
        $this->addMediaCollection('attachments');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // Простейший thumb для изображений во вложениях
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(225)
            ->nonQueued();
    }

    /* ======================== Scopes ======================== */

    /**
     * Только опубликованные и видимые уроки (по статусу).
     */
    public function scopePublished($q)
    {
        return $q->where('status', 'published')
            ->where('activity', true)
            ->whereNotNull('published_at');
    }

    /**
     * Сортировка по sort, затем по id (для стабильного порядка).
     * (оставляем твой Ordered, но ещё добавим Sorted по аналогии с курсами)
     */
    public function scopeOrdered($q)
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    /**
     * Единообразная сортировка как у курсов: sort ↑, id ↓.
     */
    public function scopeSorted($q)
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /**
     * Только активные уроки.
     */
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    /**
     * Бесплатные уроки (по типу доступа).
     */
    public function scopeFree($q)
    {
        return $q->where('access_type', 'free');
    }

    /**
     * Фильтр по локали.
     */
    public function scopeByLocale($q, string $locale)
    {
        return $q->where('locale', $locale);
    }

    /**
     * Поиск по заголовку/подзаголовку/краткому и полному описанию — аналогично курсам.
     */
    public function scopeSearch($q, ?string $term)
    {
        if (!$term) {
            return $q;
        }

        return $q->where(function ($qq) use ($term) {
            $qq->where('title', 'like', "%{$term}%")
                ->orWhere('subtitle', 'like', "%{$term}%")
                ->orWhere('short', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'sort_asc'        => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sort_desc'       => $q->orderBy('sort', 'desc')->orderByDesc('id'),
            'date_asc'        => $q->orderBy('published_at', 'asc')->orderByDesc('id'),
            'date_desc'       => $q->orderBy('published_at', 'desc')->orderByDesc('id'),
            'views_asc'       => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'views_desc'      => $q->orderBy('views', 'desc')->orderByDesc('id'),
            'likes_asc'       => $q->withCount('likes')->orderBy('likes_count', 'asc')->orderByDesc('id'),
            'likes_desc'      => $q->withCount('likes')->orderBy('likes_count', 'desc')->orderByDesc('id'),
            'rating_asc'      => $q->orderBy('rating_avg', 'asc')->orderByDesc('id'),
            'rating_desc'     => $q->orderBy('rating_avg', 'desc')->orderByDesc('id'),
            'popularity_asc'  => $q->orderBy('popularity', 'asc')->orderByDesc('id'),
            'popularity_desc' => $q->orderBy('popularity', 'desc')->orderByDesc('id'),
            'duration_asc'    => $q->orderBy('duration', 'asc')->orderByDesc('id'),
            'duration_desc'   => $q->orderBy('duration', 'desc')->orderByDesc('id'),
            'title_asc'       => $q->orderBy('title', 'asc')->orderByDesc('id'),
            'title_desc'      => $q->orderBy('title', 'desc')->orderByDesc('id'),
            default           => $q->sorted(),
        };
    }
}
