<?php

namespace App\Models\Admin\School\SchoolInstructorProfile;

use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolPayout\SchoolPayout;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolInstructorProfile extends Model
{
    use HasFactory;

    protected $table = 'school_instructor_profiles';

    protected $fillable = [
        'sort',
        'activity',
        'user_id',
        'slug',
        'experience_years',
        'hourly_rate',
        'rating_count',
        'rating_avg',
        'views',
        'social_links',
    ];

    protected $casts = [
        'sort' => 'integer',
        'activity' => 'boolean',
        'experience_years' => 'integer',
        'hourly_rate' => 'decimal:2',
        'rating_count' => 'integer',
        'rating_avg' => 'float',
        'views' => 'integer',
        'social_links' => 'array',
    ];

    /* ======================== Translations ======================== */

    /**
     * Все переводы инструктора.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(
            SchoolInstructorProfileTranslation::class,
            'school_instructor_profile_id'
        );
    }

    /**
     * Перевод текущей локали.
     *
     * Сохраняем существующую relation
     * для совместимости с Admin.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(
            SchoolInstructorProfileTranslation::class,
            'school_instructor_profile_id'
        )
            ->where(
                'locale',
                app()->getLocale()
            );
    }

    /**
     * Получить перевод:
     *
     * current locale
     * → fallback locale
     * → первый доступный.
     *
     * Helper не выполняет отдельный SQL-запрос,
     * если translations уже загружены.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?SchoolInstructorProfileTranslation {
        $locale ??= app()->getLocale();

        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        if ($this->relationLoaded('translations')) {
            return $this->translations
                ->firstWhere(
                    'locale',
                    $locale
                )
                ?: $this->translations
                    ->firstWhere(
                        'locale',
                        $fallbackLocale
                    )
                    ?: $this->translations->first();
        }

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        $translations = $this->translations()
            ->whereIn(
                'locale',
                $locales
            )
            ->get();

        return $translations
            ->firstWhere(
                'locale',
                $locale
            )
            ?: $translations
                ->firstWhere(
                    'locale',
                    $fallbackLocale
                )
                ?: $translations->first();
    }

    /* ======================== Relations ======================== */

    /**
     * Пользователь-инструктор.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Курсы инструктора.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(
            SchoolCourse::class,
            'school_instructor_profile_id'
        );
    }

    /**
     * Выплаты инструктору.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(
            SchoolPayout::class,
            'school_instructor_profile_id'
        );
    }

    /**
     * Изображения инструктора.
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolInstructorProfileImage::class,
            'school_instructor_profile_has_images',
            'school_instructor_profile_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy(
                'school_instructor_profile_has_images.order',
                'asc'
            );
    }

    /* ======================== Common Scopes ======================== */

    /**
     * Только активные.
     */
    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'activity',
            true
        );
    }

    /**
     * Базовая сортировка.
     *
     * sort ↑
     * id ↓
     */
    public function scopeSorted(
        Builder $query
    ): Builder {
        return $query
            ->orderBy(
                'sort',
                'asc'
            )
            ->orderByDesc('id');
    }

    /**
     * Подгрузка перевода
     * конкретной локали.
     *
     * Сохраняем для существующей
     * Admin-логики.
     */
    public function scopeWithLocale(
        Builder $query,
        ?string $locale = null
    ): Builder {
        $locale ??= app()->getLocale();

        return $query->with([
            'translations' => fn ($translationQuery) =>
            $translationQuery->where(
                'locale',
                $locale
            ),
        ]);
    }

    /**
     * Инструкторы с хорошим рейтингом.
     *
     * Общий scope:
     * корректен и для Admin,
     * и для Public.
     */
    public function scopeWithGoodRating(
        Builder $query,
        float $min = 4.5,
        int $minCount = 10
    ): Builder {
        return $query
            ->where(
                'rating_avg',
                '>=',
                $min
            )
            ->where(
                'rating_count',
                '>=',
                $minCount
            );
    }

    /* ======================== Public Scopes ======================== */

    /**
     * Публичный набор инструкторов.
     *
     * Условия:
     * - профиль активен;
     * - существует перевод текущей
     *   или fallback локали.
     *
     * Переводы здесь НЕ eager-load.
     * Это ответственность Public Controller.
     */
    public function scopeForPublic(
        Builder $query,
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): Builder {
        $locale ??= app()->getLocale();

        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        $locales = array_values(
            array_unique([
                $locale,
                $fallbackLocale,
            ])
        );

        return $query
            ->active()
            ->whereHas(
                'translations',
                fn (Builder $translationQuery) =>
                $translationQuery->whereIn(
                    'locale',
                    $locales
                )
            );
    }

    /**
     * Публичный поиск.
     *
     * Полностью соответствует данным,
     * по которым выполняется поиск
     * в Public Index.vue:
     *
     * - id;
     * - slug;
     * - title;
     * - short;
     * - user.name.
     *
     * Не ищем по:
     * - bio;
     * - email;
     * - payouts;
     * - другим внутренним данным.
     *
     * Переводы:
     * current locale → fallback locale.
     */
    public function scopePublicSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): Builder {
        $term = trim(
            (string) $term
        );

        if ($term === '') {
            return $query;
        }

        $locale ??= app()->getLocale();

        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        return $query->where(
            function (Builder $publicQuery) use (
                $term,
                $locale,
                $fallbackLocale
            ) {
                $publicQuery
                    /**
                     * Основная таблица.
                     */
                    ->where(
                        'school_instructor_profiles.id',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'school_instructor_profiles.slug',
                        'like',
                        "%{$term}%"
                    )

                    /**
                     * Текущая локаль.
                     */
                    ->orWhereHas(
                        'translations',
                        function (
                            Builder $translationQuery
                        ) use (
                            $term,
                            $locale
                        ) {
                            $translationQuery
                                ->where(
                                    'locale',
                                    $locale
                                )
                                ->where(
                                    function (
                                        Builder $textQuery
                                    ) use ($term) {
                                        $textQuery
                                            ->where(
                                                'title',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'short',
                                                'like',
                                                "%{$term}%"
                                            );
                                    }
                                );
                        }
                    )

                    /**
                     * Fallback используется
                     * только при отсутствии
                     * перевода текущей локали.
                     */
                    ->orWhere(
                        function (
                            Builder $fallbackQuery
                        ) use (
                            $term,
                            $locale,
                            $fallbackLocale
                        ) {
                            if (
                                $locale === $fallbackLocale
                            ) {
                                return;
                            }

                            $fallbackQuery
                                ->whereDoesntHave(
                                    'translations',
                                    fn (
                                        Builder $translationQuery
                                    ) =>
                                    $translationQuery->where(
                                        'locale',
                                        $locale
                                    )
                                )
                                ->whereHas(
                                    'translations',
                                    function (
                                        Builder $translationQuery
                                    ) use (
                                        $term,
                                        $fallbackLocale
                                    ) {
                                        $translationQuery
                                            ->where(
                                                'locale',
                                                $fallbackLocale
                                            )
                                            ->where(
                                                function (
                                                    Builder $textQuery
                                                ) use ($term) {
                                                    $textQuery
                                                        ->where(
                                                            'title',
                                                            'like',
                                                            "%{$term}%"
                                                        )
                                                        ->orWhere(
                                                            'short',
                                                            'like',
                                                            "%{$term}%"
                                                        );
                                                }
                                            );
                                    }
                                );
                        }
                    )

                    /**
                     * Публичное имя пользователя.
                     */
                    ->orWhereHas(
                        'user',
                        fn (Builder $userQuery) =>
                        $userQuery->where(
                            'name',
                            'like',
                            "%{$term}%"
                        )
                    );
            }
        );
    }

    /**
     * Публичная сортировка.
     *
     * Поддерживаются только варианты,
     * реально существующие в Public Index.vue.
     *
     * courses_count должен быть заранее
     * рассчитан Public Controller только
     * по публичным курсам.
     */
    public function scopePublicSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): Builder {
        $locale ??= app()->getLocale();

        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        return match ($sort) {
            'idAsc' => $query
                ->orderBy(
                    'school_instructor_profiles.id',
                    'asc'
                ),

            'idDesc' => $query
                ->orderBy(
                    'school_instructor_profiles.id',
                    'desc'
                ),

            'sortAsc' => $query
                ->orderBy(
                    'school_instructor_profiles.sort',
                    'asc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            'sortDesc' => $query
                ->orderBy(
                    'school_instructor_profiles.sort',
                    'desc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
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

            'viewsAsc' => $query
                ->orderBy(
                    'school_instructor_profiles.views',
                    'asc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            'viewsDesc' => $query
                ->orderBy(
                    'school_instructor_profiles.views',
                    'desc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            'ratingCountAsc' => $query
                ->orderBy(
                    'school_instructor_profiles.rating_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            'ratingCountDesc' => $query
                ->orderBy(
                    'school_instructor_profiles.rating_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            'ratingAvgAsc' => $query
                ->orderBy(
                    'school_instructor_profiles.rating_avg',
                    'asc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            'ratingAvgDesc' => $query
                ->orderBy(
                    'school_instructor_profiles.rating_avg',
                    'desc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            'experienceAsc' => $query
                ->orderBy(
                    'school_instructor_profiles.experience_years',
                    'asc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            'experienceDesc' => $query
                ->orderBy(
                    'school_instructor_profiles.experience_years',
                    'desc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            /**
             * courses_count уже рассчитан
             * Public Controller через:
             *
             * courses as courses_count
             *     ->forPublic(...)
             *
             * Поэтому повторный withCount()
             * здесь не нужен.
             */
            'coursesAsc' => $query
                ->orderBy(
                    'courses_count',
                    'asc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            'coursesDesc' => $query
                ->orderBy(
                    'courses_count',
                    'desc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            'dateAsc' => $query
                ->orderBy(
                    'school_instructor_profiles.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            'dateDesc' => $query
                ->orderBy(
                    'school_instructor_profiles.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                ),

            default => $query->sorted(),
        };
    }

    /**
     * Публичная сортировка по title.
     *
     * Приоритет:
     *
     * current locale
     * → fallback locale.
     */
    protected function applyPublicTitleSort(
        Builder $query,
        string $locale,
        string $fallbackLocale,
        string $direction
    ): Builder {
        $query->leftJoin(
            'school_instructor_profile_translations as sipt_public_current',
            function ($join) use ($locale) {
                $join
                    ->on(
                        'sipt_public_current.school_instructor_profile_id',
                        '=',
                        'school_instructor_profiles.id'
                    )
                    ->where(
                        'sipt_public_current.locale',
                        '=',
                        $locale
                    );
            }
        );

        /**
         * Если current locale совпадает
         * с fallback locale,
         * второй JOIN не нужен.
         */
        if ($locale === $fallbackLocale) {
            return $query
                ->orderBy(
                    'sipt_public_current.title',
                    $direction
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                )
                ->addSelect(
                    'school_instructor_profiles.*'
                );
        }

        $query->leftJoin(
            'school_instructor_profile_translations as sipt_public_fallback',
            function ($join) use ($fallbackLocale) {
                $join
                    ->on(
                        'sipt_public_fallback.school_instructor_profile_id',
                        '=',
                        'school_instructor_profiles.id'
                    )
                    ->where(
                        'sipt_public_fallback.locale',
                        '=',
                        $fallbackLocale
                    );
            }
        );

        return $query
            ->orderByRaw(
                'COALESCE(
                    sipt_public_current.title,
                    sipt_public_fallback.title
                ) ' . $direction
            )
            ->orderByDesc(
                'school_instructor_profiles.id'
            )
            ->addSelect(
                'school_instructor_profiles.*'
            );
    }

    /* ======================== Admin / Common Scopes ======================== */

    /**
     * Поиск.
     *
     * Существующий scope сохраняем,
     * потому что он используется Admin Controller.
     */
    public function scopeSearch(
        Builder $q,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim(
            (string) $term
        );

        if ($term === '') {
            return $q;
        }

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

        return $q->where(
            function (Builder $query) use (
                $term,
                $locales
            ) {
                /**
                 * Основная таблица.
                 */
                $query
                    ->where(
                        'school_instructor_profiles.slug',
                        'like',
                        "%{$term}%"
                    )

                    /**
                     * Current locale
                     * + fallback locale.
                     */
                    ->orWhereHas(
                        'translations',
                        function (Builder $translationQuery) use (
                            $term,
                            $locales
                        ) {
                            $translationQuery
                                ->whereIn(
                                    'locale',
                                    $locales
                                )
                                ->where(
                                    function (Builder $subQuery) use ($term) {
                                        $subQuery
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
                                                'bio',
                                                'like',
                                                "%{$term}%"
                                            );
                                    }
                                );
                        }
                    )

                    /**
                     * Пользователь.
                     */
                    ->orWhereHas(
                        'user',
                        fn (Builder $userQuery) =>
                        $userQuery->where(
                            'name',
                            'like',
                            "%{$term}%"
                        )
                    );
            }
        );
    }

    /**
     * Сортировка по параметру.
     *
     * Существующий scope сохраняем,
     * потому что он используется Admin Controller.
     */
    public function scopeSortByParam(
        Builder $q,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale ??= app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        return match ($sort) {
            'idAsc' => $q->orderBy('id', 'asc'),
            'idDesc' => $q->orderBy('id', 'desc'),

            'sortAsc' => $q
                ->orderBy('sort', 'asc')
                ->orderByDesc('id'),

            'sortDesc' => $q
                ->orderBy('sort', 'desc')
                ->orderByDesc('id'),

            'slugAsc' => $q
                ->orderBy('slug', 'asc')
                ->orderByDesc('id'),

            'slugDesc' => $q
                ->orderBy('slug', 'desc')
                ->orderByDesc('id'),

            'titleAsc' => $q
                ->leftJoin(
                    'school_instructor_profile_translations as sipt_current',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sipt_current.school_instructor_profile_id',
                                '=',
                                'school_instructor_profiles.id'
                            )
                            ->where(
                                'sipt_current.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->leftJoin(
                    'school_instructor_profile_translations as sipt_fallback',
                    function ($join) use ($fallbackLocale) {
                        $join
                            ->on(
                                'sipt_fallback.school_instructor_profile_id',
                                '=',
                                'school_instructor_profiles.id'
                            )
                            ->where(
                                'sipt_fallback.locale',
                                '=',
                                $fallbackLocale
                            );
                    }
                )
                ->orderByRaw(
                    'COALESCE(sipt_current.title, sipt_fallback.title) ASC'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                )
                ->addSelect(
                    'school_instructor_profiles.*'
                ),

            'titleDesc' => $q
                ->leftJoin(
                    'school_instructor_profile_translations as sipt_current',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'sipt_current.school_instructor_profile_id',
                                '=',
                                'school_instructor_profiles.id'
                            )
                            ->where(
                                'sipt_current.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->leftJoin(
                    'school_instructor_profile_translations as sipt_fallback',
                    function ($join) use ($fallbackLocale) {
                        $join
                            ->on(
                                'sipt_fallback.school_instructor_profile_id',
                                '=',
                                'school_instructor_profiles.id'
                            )
                            ->where(
                                'sipt_fallback.locale',
                                '=',
                                $fallbackLocale
                            );
                    }
                )
                ->orderByRaw(
                    'COALESCE(sipt_current.title, sipt_fallback.title) DESC'
                )
                ->orderByDesc(
                    'school_instructor_profiles.id'
                )
                ->addSelect(
                    'school_instructor_profiles.*'
                ),

            'viewsAsc' => $q
                ->orderBy('views', 'asc')
                ->orderByDesc('id'),

            'viewsDesc' => $q
                ->orderBy('views', 'desc')
                ->orderByDesc('id'),

            'ratingAvgAsc' => $q
                ->orderBy('rating_avg', 'asc')
                ->orderByDesc('id'),

            'ratingAvgDesc' => $q
                ->orderBy('rating_avg', 'desc')
                ->orderByDesc('id'),

            'ratingCountAsc' => $q
                ->orderBy('rating_count', 'asc')
                ->orderByDesc('id'),

            'ratingCountDesc' => $q
                ->orderBy('rating_count', 'desc')
                ->orderByDesc('id'),

            'hourlyRateAsc' => $q
                ->orderBy('hourly_rate', 'asc')
                ->orderByDesc('id'),

            'hourlyRateDesc' => $q
                ->orderBy('hourly_rate', 'desc')
                ->orderByDesc('id'),

            'experienceAsc' => $q
                ->orderBy('experience_years', 'asc')
                ->orderByDesc('id'),

            'experienceDesc' => $q
                ->orderBy('experience_years', 'desc')
                ->orderByDesc('id'),

            'coursesAsc' => $q
                ->withCount('courses')
                ->orderBy('courses_count', 'asc')
                ->orderByDesc('id'),

            'coursesDesc' => $q
                ->withCount('courses')
                ->orderBy('courses_count', 'desc')
                ->orderByDesc('id'),

            'payoutsAsc' => $q
                ->withCount('payouts')
                ->orderBy('payouts_count', 'asc')
                ->orderByDesc('id'),

            'payoutsDesc' => $q
                ->withCount('payouts')
                ->orderBy('payouts_count', 'desc')
                ->orderByDesc('id'),

            'imagesAsc' => $q
                ->withCount('images')
                ->orderBy('images_count', 'asc')
                ->orderByDesc('id'),

            'imagesDesc' => $q
                ->withCount('images')
                ->orderBy('images_count', 'desc')
                ->orderByDesc('id'),

            'createdAtAsc',
            'dateAsc' => $q
                ->orderBy('created_at', 'asc')
                ->orderByDesc('id'),

            'createdAtDesc',
            'dateDesc' => $q
                ->orderBy('created_at', 'desc')
                ->orderByDesc('id'),

            'updatedAtAsc' => $q
                ->orderBy('updated_at', 'asc')
                ->orderByDesc('id'),

            'updatedAtDesc' => $q
                ->orderBy('updated_at', 'desc')
                ->orderByDesc('id'),

            'activityAsc' => $q
                ->orderBy('activity', 'asc')
                ->orderByDesc('id'),

            'activityDesc' => $q
                ->orderBy('activity', 'desc')
                ->orderByDesc('id'),

            'activity' => $q
                ->where('activity', true)
                ->orderByDesc('id'),

            'inactive' => $q
                ->where('activity', false)
                ->orderByDesc('id'),

            default => $q->sorted(),
        };
    }

    /* ======================== Accessors ======================== */

    /**
     * Публичное имя инструктора.
     *
     * Если translations уже eager-loaded,
     * дополнительный запрос не выполняется.
     */
    public function getPublicNameAttribute(): string
    {
        $translation =
            $this->translationOrFallback();

        if ($translation?->title) {
            return $translation->title;
        }

        if ($this->relationLoaded('user')) {
            return $this->user?->name
                ?: 'Инструктор';
        }

        return $this->user()
            ->value('name')
            ?: 'Инструктор';
    }

    /**
     * Главное изображение инструктора.
     */
    public function getPrimaryImageAttribute(): ?SchoolInstructorProfileImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images
                ->sortBy(
                    fn ($image) =>
                        $image->pivot->order
                        ?? PHP_INT_MAX
                )
                ->first();
        }

        return $this->images()
            ->orderBy(
                'school_instructor_profile_has_images.order',
                'asc'
            )
            ->first();
    }
}
