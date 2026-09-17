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
        return $q->where(
            'school_lessons.activity',
            true
        );
    }

    /** Опубликованные */
    public function scopePublished(Builder $q): Builder
    {
        return $q
            ->where(
                'school_lessons.status',
                'published'
            )
            ->where(
                'school_lessons.activity',
                true
            )
            ->whereNotNull(
                'school_lessons.published_at'
            );
    }

    /** Бесплатные уроки */
    public function scopeFree(Builder $q): Builder
    {
        return $q->where(
            'school_lessons.access_type',
            'free'
        );
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

    /**
     * Публичный набор.
     *
     * Урок должен быть:
     * - активным;
     * - опубликованным;
     * - не private;
     * - иметь current или fallback перевод.
     *
     * Загружаются только current + fallback translations.
     */
    public function scopeForPublic(
        Builder $q,
        ?string $locale = null
    ): Builder {
        $locale ??= app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        return $q
            ->active()
            ->published()
            ->where(
                'school_lessons.availability',
                '!=',
                'private'
            )
            ->whereHas(
                'translations',
                fn (Builder $query) =>
                $query->whereIn(
                    'locale',
                    $locales
                )
            )
            ->with([
                'translations' =>
                    fn ($query) =>
                    $query->whereIn(
                        'locale',
                        $locales
                    ),
            ]);
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

    /**
     * Публичный поиск.
     *
     * Public-контракт:
     * - id;
     * - slug;
     * - resolved translation.title;
     * - resolved translation.short.
     *
     * Fallback-перевод участвует в поиске только тогда,
     * когда перевода текущей локали у урока нет.
     */
    public function scopePublicSearch(
        Builder $q,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim((string) $term);

        if ($term === '') {
            return $q;
        }

        $locale ??= app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        return $q->where(function (Builder $query) use ($term, $locale, $fallbackLocale) {
            $query
                ->where('school_lessons.id', 'like', "%{$term}%")
                ->orWhere('school_lessons.slug', 'like', "%{$term}%")
                ->orWhereHas('translations', function (Builder $translationQuery) use ($term, $locale) {
                    $translationQuery
                        ->where('locale', $locale)
                        ->where(function (Builder $translation) use ($term) {
                            $translation
                                ->where('title', 'like', "%{$term}%")
                                ->orWhere('short', 'like', "%{$term}%");
                        });
                });

            if ($fallbackLocale !== $locale) {
                $query->orWhere(function (Builder $fallbackQuery) use ($term, $locale, $fallbackLocale) {
                    $fallbackQuery
                        ->whereDoesntHave(
                            'translations',
                            fn (Builder $translationQuery) =>
                            $translationQuery->where('locale', $locale)
                        )
                        ->whereHas('translations', function (Builder $translationQuery) use ($term, $fallbackLocale) {
                            $translationQuery
                                ->where('locale', $fallbackLocale)
                                ->where(function (Builder $translation) use ($term) {
                                    $translation
                                        ->where('title', 'like', "%{$term}%")
                                        ->orWhere('short', 'like', "%{$term}%");
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

            'slugAsc' => $q->orderBy('slug', 'asc')->orderByDesc('id'),
            'slugDesc' => $q->orderBy('slug', 'desc')->orderByDesc('id'),

            'moduleAsc' => $q->orderBy('school_module_id', 'asc')->orderByDesc('id'),
            'moduleDesc' => $q->orderBy('school_module_id', 'desc')->orderByDesc('id'),

            'titleAsc' => $q
                ->leftJoin(
                    'school_lesson_translations as slt_sort',
                    function ($join) use ($locale) {
                        $join->on(
                            'slt_sort.school_lesson_id',
                            '=',
                            'school_lessons.id'
                        )->where(
                            'slt_sort.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->orderBy(
                    'slt_sort.title',
                    'asc'
                )
                ->orderByDesc(
                    'school_lessons.id'
                )
                ->addSelect(
                    'school_lessons.*'
                ),

            'titleDesc' => $q
                ->leftJoin(
                    'school_lesson_translations as slt_sort',
                    function ($join) use ($locale) {
                        $join->on(
                            'slt_sort.school_lesson_id',
                            '=',
                            'school_lessons.id'
                        )->where(
                            'slt_sort.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->orderBy(
                    'slt_sort.title',
                    'desc'
                )
                ->orderByDesc(
                    'school_lessons.id'
                )
                ->addSelect(
                    'school_lessons.*'
                ),

            'statusAsc' => $q->orderBy('status', 'asc')->orderByDesc('id'),
            'statusDesc' => $q->orderBy('status', 'desc')->orderByDesc('id'),

            'availabilityAsc' => $q->orderBy('availability', 'asc')->orderByDesc('id'),
            'availabilityDesc' => $q->orderBy('availability', 'desc')->orderByDesc('id'),

            'accessTypeAsc' => $q->orderBy('access_type', 'asc')->orderByDesc('id'),
            'accessTypeDesc' => $q->orderBy('access_type', 'desc')->orderByDesc('id'),

            'contentTypeAsc' => $q->orderBy('content_type', 'asc')->orderByDesc('id'),
            'contentTypeDesc' => $q->orderBy('content_type', 'desc')->orderByDesc('id'),

            'contentIdAsc' => $q->orderBy('content_id', 'asc')->orderByDesc('id'),
            'contentIdDesc' => $q->orderBy('content_id', 'desc')->orderByDesc('id'),

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

    /**
     * Публичная сортировка.
     *
     * Содержит только варианты,
     * необходимые Public-интерфейсу.
     *
     * title использует resolved translation:
     * current locale → fallback locale.
     */
    public function scopePublicSortByParam(
        Builder $q,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale ??= app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'ru');

        return match ($sort) {
            'idAsc' => $q->orderBy('school_lessons.id', 'asc'),
            'idDesc' => $q->orderBy('school_lessons.id', 'desc'),

            'sortAsc' => $q
                ->orderBy('school_lessons.sort', 'asc')
                ->orderByDesc('school_lessons.id'),

            'sortDesc' => $q
                ->orderBy('school_lessons.sort', 'desc')
                ->orderByDesc('school_lessons.id'),

            'titleAsc' => $this->scopePublicSortByTitle(
                $q,
                'asc',
                $locale,
                $fallbackLocale
            ),

            'titleDesc' => $this->scopePublicSortByTitle(
                $q,
                'desc',
                $locale,
                $fallbackLocale
            ),

            'difficultyAsc' => $q
                ->orderBy('school_lessons.difficulty', 'asc')
                ->orderByDesc('school_lessons.id'),

            'difficultyDesc' => $q
                ->orderBy('school_lessons.difficulty', 'desc')
                ->orderByDesc('school_lessons.id'),

            'durationAsc' => $q
                ->orderBy('school_lessons.duration', 'asc')
                ->orderByDesc('school_lessons.id'),

            'durationDesc' => $q
                ->orderBy('school_lessons.duration', 'desc')
                ->orderByDesc('school_lessons.id'),

            'viewsAsc' => $q
                ->orderBy('school_lessons.views', 'asc')
                ->orderByDesc('school_lessons.id'),

            'viewsDesc' => $q
                ->orderBy('school_lessons.views', 'desc')
                ->orderByDesc('school_lessons.id'),

            'likesAsc' => $q
                ->withCount('likes')
                ->orderBy('likes_count', 'asc')
                ->orderByDesc('school_lessons.id'),

            'likesDesc' => $q
                ->withCount('likes')
                ->orderBy('likes_count', 'desc')
                ->orderByDesc('school_lessons.id'),

            'popularityAsc' => $q
                ->orderBy('school_lessons.popularity', 'asc')
                ->orderByDesc('school_lessons.id'),

            'popularityDesc' => $q
                ->orderBy('school_lessons.popularity', 'desc')
                ->orderByDesc('school_lessons.id'),

            'ratingCountAsc' => $q
                ->orderBy('school_lessons.rating_count', 'asc')
                ->orderByDesc('school_lessons.id'),

            'ratingCountDesc' => $q
                ->orderBy('school_lessons.rating_count', 'desc')
                ->orderByDesc('school_lessons.id'),

            'ratingAvgAsc' => $q
                ->orderBy('school_lessons.rating_avg', 'asc')
                ->orderByDesc('school_lessons.id'),

            'ratingAvgDesc' => $q
                ->orderBy('school_lessons.rating_avg', 'desc')
                ->orderByDesc('school_lessons.id'),

            'publishedAtAsc', 'dateAsc' => $q
                ->orderBy('school_lessons.published_at', 'asc')
                ->orderByDesc('school_lessons.id'),

            'publishedAtDesc', 'dateDesc' => $q
                ->orderBy('school_lessons.published_at', 'desc')
                ->orderByDesc('school_lessons.id'),

            default => $q->sorted(),
        };
    }

    /**
     * Public-сортировка по resolved title:
     *
     * current locale → fallback locale.
     */
    protected function scopePublicSortByTitle(
        Builder $q,
        string $direction,
        string $locale,
        string $fallbackLocale
    ): Builder {
        $direction = strtolower($direction) === 'desc'
            ? 'desc'
            : 'asc';

        $q->leftJoin(
            'school_lesson_translations as slt_public_current',
            function ($join) use ($locale) {
                $join
                    ->on(
                        'slt_public_current.school_lesson_id',
                        '=',
                        'school_lessons.id'
                    )
                    ->where(
                        'slt_public_current.locale',
                        '=',
                        $locale
                    );
            }
        );

        if ($fallbackLocale !== $locale) {
            $q->leftJoin(
                'school_lesson_translations as slt_public_fallback',
                function ($join) use ($fallbackLocale) {
                    $join
                        ->on(
                            'slt_public_fallback.school_lesson_id',
                            '=',
                            'school_lessons.id'
                        )
                        ->where(
                            'slt_public_fallback.locale',
                            '=',
                            $fallbackLocale
                        );
                }
            );

            $q->orderByRaw(
                "COALESCE(slt_public_current.title, slt_public_fallback.title) {$direction}"
            );
        } else {
            $q->orderBy(
                'slt_public_current.title',
                $direction
            );
        }

        return $q
            ->orderByDesc('school_lessons.id')
            ->addSelect('school_lessons.*');
    }

    /* ======================== Helpers ======================== */

    /**
     * Возвращает перевод:
     *
     * current locale → fallback locale.
     *
     * Метод работает только с уже загруженной
     * relation translations и не создаёт скрытых SQL-запросов.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?SchoolLessonTranslation {
        $locale ??= app()->getLocale();
        $fallbackLocale ??= config('app.fallback_locale', 'ru');

        $translations = $this->relationLoaded('translations')
            ? $this->translations
            : collect();

        return $translations->firstWhere('locale', $locale)
            ?? $translations->firstWhere('locale', $fallbackLocale);
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
