<?php

namespace App\Models\Admin\School\SchoolLesson;

use App\Models\Admin\School\SchoolHashtag\SchoolHashtag;
use App\Models\Admin\School\SchoolModule\SchoolModule;
use App\Models\User\Like\SchoolLessonLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class SchoolLesson extends Model
{
    use HasFactory;

    protected $table = 'school_lessons';

    protected $fillable = [
        'school_module_id',
        'sort',
        'activity',
        'slug',
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

    protected $casts = [
        'school_module_id' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',
        'published_at' => 'datetime',
        'difficulty' => 'integer',
        'duration' => 'integer',
        'preview_value' => 'integer',
        'popularity' => 'integer',
        'rating_count' => 'integer',
        'rating_avg' => 'float',
        'views' => 'integer',
        'likes' => 'integer',
    ];

    /* ======================== Translations ======================== */

    /** Все переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolLessonTranslation::class, 'school_lesson_id');
    }

    /** Перевод по текущей локали */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolLessonTranslation::class, 'school_lesson_id')
            ->where('locale', app()->getLocale());
    }

    /* ======================== Relations ======================== */

    /** Родительский модуль */
    public function module(): BelongsTo
    {
        return $this->belongsTo(SchoolModule::class, 'school_module_id');
    }

    /** Полиморфный контент урока */
    public function content(): MorphTo
    {
        return $this->morphTo();
    }

    /** Изображения урока */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolLessonImage::class,
            'school_lesson_has_images',
            'school_lesson_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('school_lesson_has_images.order', 'asc');
    }

    /** Лайки урока */
    public function likes(): HasMany
    {
        return $this->hasMany(SchoolLessonLike::class, 'school_lesson_id');
    }

    /** Хештеги урока */
    public function hashtags(): MorphToMany
    {
        return $this->morphToMany(
            SchoolHashtag::class,
            'hashtaggable',
            'school_hashtaggables',
            'hashtaggable_id',
            'school_hashtag_id'
        )->withTimestamps();
    }

    /* ======================== Scopes ======================== */

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Опубликованные */
    public function scopePublished(Builder $q): Builder
    {
        return $q
            ->where('status', 'published')
            ->where('activity', true)
            ->whereNotNull('published_at');
    }

    /** Бесплатные уроки */
    public function scopeFree(Builder $q): Builder
    {
        return $q->where('access_type', 'free');
    }

    /** Сортировка */
    public function scopeSorted(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /** Алиас сортировки */
    public function scopeOrdered(Builder $q): Builder
    {
        return $this->scopeSorted($q);
    }

    /** Подгрузка перевода */
    public function scopeWithLocale(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q->with([
            'translations' => fn ($query) => $query->where('locale', $locale),
        ]);
    }

    /** Публичный набор */
    public function scopeForPublic(Builder $q, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return $q
            ->active()
            ->published()
            ->whereHas('translations', fn ($qq) => $qq->where('locale', $locale))
            ->withLocale($locale);
    }

    /** Поиск */
    public function scopeSearch(Builder $q, ?string $term, ?string $locale = null): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $q;
        }

        $locale = $locale ?: app()->getLocale();

        $words = collect(preg_split('/[\s:#№,"\'«»(){}\[\].!?\/\\\\|]+/u', $term))
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => mb_strlen($word) >= 2)
            ->values();

        if ($words->isEmpty()) {
            return $q;
        }

        return $q->where(function (Builder $query) use ($words, $locale) {
            foreach ($words as $word) {
                $query->where(function (Builder $query) use ($word, $locale) {
                    $query
                        ->where('school_lessons.slug', 'like', "%{$word}%")
                        ->orWhere('school_lessons.id', 'like', "%{$word}%")
                        ->orWhere('school_lessons.school_module_id', 'like', "%{$word}%")
                        ->orWhere('school_lessons.sort', 'like', "%{$word}%")
                        ->orWhere('school_lessons.status', 'like', "%{$word}%")
                        ->orWhere('school_lessons.availability', 'like', "%{$word}%")
                        ->orWhere('school_lessons.access_type', 'like', "%{$word}%")
                        ->orWhere('school_lessons.content_type', 'like', "%{$word}%")

                        ->orWhereHas('translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('module.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('module.course.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('module.course.instructorProfile.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%");
                                });
                        })

                        ->orWhereHas('module.course.instructorProfile.user', function (Builder $qq) use ($word) {
                            $qq->where('name', 'like', "%{$word}%")
                                ->orWhere('email', 'like', "%{$word}%");
                        })

                        ->orWhereHas('hashtags.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('name', 'like', "%{$word}%")
                                        ->orWhere('slug', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%");
                                });
                        });
                });
            }
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $q->orderBy('id', 'asc'),
            'idDesc' => $q->orderBy('id', 'desc'),

            'sortAsc' => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sortDesc' => $q->orderBy('sort', 'desc')->orderByDesc('id'),

            'moduleAsc' => $q->orderBy('school_module_id', 'asc')->orderByDesc('id'),
            'moduleDesc' => $q->orderBy('school_module_id', 'desc')->orderByDesc('id'),

            'titleAsc' => $q
                ->leftJoin('school_lesson_translations as slt_sort', function ($join) use ($locale) {
                    $join->on('slt_sort.school_lesson_id', '=', 'school_lessons.id')
                        ->where('slt_sort.locale', '=', $locale);
                })
                ->orderBy('slt_sort.title', 'asc')
                ->orderByDesc('school_lessons.id')
                ->select('school_lessons.*'),

            'titleDesc' => $q
                ->leftJoin('school_lesson_translations as slt_sort', function ($join) use ($locale) {
                    $join->on('slt_sort.school_lesson_id', '=', 'school_lessons.id')
                        ->where('slt_sort.locale', '=', $locale);
                })
                ->orderBy('slt_sort.title', 'desc')
                ->orderByDesc('school_lessons.id')
                ->select('school_lessons.*'),

            'statusAsc' => $q->orderBy('status', 'asc')->orderByDesc('id'),
            'statusDesc' => $q->orderBy('status', 'desc')->orderByDesc('id'),

            'availabilityAsc' => $q->orderBy('availability', 'asc')->orderByDesc('id'),
            'availabilityDesc' => $q->orderBy('availability', 'desc')->orderByDesc('id'),

            'accessTypeAsc' => $q->orderBy('access_type', 'asc')->orderByDesc('id'),
            'accessTypeDesc' => $q->orderBy('access_type', 'desc')->orderByDesc('id'),

            'difficultyAsc' => $q->orderBy('difficulty', 'asc')->orderByDesc('id'),
            'difficultyDesc' => $q->orderBy('difficulty', 'desc')->orderByDesc('id'),

            'durationAsc' => $q->orderBy('duration', 'asc')->orderByDesc('id'),
            'durationDesc' => $q->orderBy('duration', 'desc')->orderByDesc('id'),

            'previewValueAsc' => $q->orderBy('preview_value', 'asc')->orderByDesc('id'),
            'previewValueDesc' => $q->orderBy('preview_value', 'desc')->orderByDesc('id'),

            'popularityAsc' => $q->orderBy('popularity', 'asc')->orderByDesc('id'),
            'popularityDesc' => $q->orderBy('popularity', 'desc')->orderByDesc('id'),

            'ratingCountAsc' => $q->orderBy('rating_count', 'asc')->orderByDesc('id'),
            'ratingCountDesc' => $q->orderBy('rating_count', 'desc')->orderByDesc('id'),

            'ratingAvgAsc' => $q->orderBy('rating_avg', 'asc')->orderByDesc('id'),
            'ratingAvgDesc' => $q->orderBy('rating_avg', 'desc')->orderByDesc('id'),

            'viewsAsc' => $q->orderBy('views', 'asc')->orderByDesc('id'),
            'viewsDesc' => $q->orderBy('views', 'desc')->orderByDesc('id'),

            'likesAsc' => $q->orderBy('likes', 'asc')->orderByDesc('id'),
            'likesDesc' => $q->orderBy('likes', 'desc')->orderByDesc('id'),

            'likesCountAsc' => $q->withCount('likes')->orderBy('likes_count', 'asc')->orderByDesc('id'),
            'likesCountDesc' => $q->withCount('likes')->orderBy('likes_count', 'desc')->orderByDesc('id'),

            'imagesAsc' => $q->withCount('images')->orderBy('images_count', 'asc')->orderByDesc('id'),
            'imagesDesc' => $q->withCount('images')->orderBy('images_count', 'desc')->orderByDesc('id'),

            'hashtagsAsc' => $q->withCount('hashtags')->orderBy('hashtags_count', 'asc')->orderByDesc('id'),
            'hashtagsDesc' => $q->withCount('hashtags')->orderBy('hashtags_count', 'desc')->orderByDesc('id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $q->where('activity', true)->orderByDesc('id'),
            'inactive' => $q->where('activity', false)->orderByDesc('id'),

            'publishedAtAsc', 'dateAsc' => $q->orderBy('published_at', 'asc')->orderByDesc('id'),
            'publishedAtDesc', 'dateDesc' => $q->orderBy('published_at', 'desc')->orderByDesc('id'),

            'createdAtAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('id'),

            default => $q->sorted(),
        };
    }

    /* ======================== Accessors ======================== */

    /** Главное изображение */
    public function getPrimaryImageAttribute(): ?SchoolLessonImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                ->first();
        }

        return $this->images()
            ->orderBy('school_lesson_has_images.order', 'asc')
            ->first();
    }
}
