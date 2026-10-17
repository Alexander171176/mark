<?php

namespace App\Models\Admin\Slider\SliderSlideAdvantage;

use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SliderSlideAdvantage extends Model
{
    use HasFactory;

    protected $table = 'slider_slide_advantages';

    protected $fillable = [
        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',
        'sort',
        'activity',
        'icon_type',
        'icon',
        'icon_color',
        'style',
        'settings',
    ];

    protected $casts = [
        'moderation_status' => 'integer',
        'moderated_by' => 'integer',
        'moderated_at' => 'datetime',
        'sort' => 'integer',
        'activity' => 'boolean',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* =========================================================
     | RELATIONS
     ========================================================= */

    /** Пользователь, выполнивший модерацию. */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Слайды, использующие преимущество */
    public function slides(): BelongsToMany
    {
        return $this->belongsToMany(
            SliderSlide::class,
            'slider_slide_has_advantages',
            'slider_slide_advantage_id',
            'slider_slide_id'
        )
            ->withPivot(['id', 'sort', 'activity'])
            ->withTimestamps()
            ->orderBy('slider_slides.id');
    }

    /** Все переводы преимущества */
    public function translations(): HasMany
    {
        return $this->hasMany(
            SliderSlideAdvantageTranslation::class,
            'slider_slide_advantage_id'
        );
    }

    /** Перевод преимущества текущего языка */
    public function translation(): HasOne
    {
        return $this->hasOne(
            SliderSlideAdvantageTranslation::class,
            'slider_slide_advantage_id'
        )->where(
            'locale',
            app()->getLocale()
        );
    }

    /**
     * Перевод преимущества:
     * текущая локаль → fallback → null.
     *
     * Использует заранее загруженные translations,
     * не выполняя дополнительных запросов к БД.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallback = null
    ): ?SliderSlideAdvantageTranslation {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        $locale ??= app()->getLocale();

        $fallback ??= config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $this->translations
            ->firstWhere('locale', $locale);

        if ($translation) {
            return $translation;
        }

        if ($fallback !== $locale) {
            return $this->translations
                ->firstWhere('locale', $fallback);
        }

        return null;
    }

    /* =========================================================
     | BASE SCOPES
     ========================================================= */

    /** Одобренные записи. */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('slider_slide_advantages.moderation_status', 1);
    }

    /** Запись ожидает модерации. */
    public function isPendingModeration(): bool
    {
        return (int) $this->moderation_status === 0;
    }

    /** Запись одобрена. */
    public function isApproved(): bool
    {
        return (int) $this->moderation_status === 1;
    }

    /** Запись отклонена. */
    public function isRejected(): bool
    {
        return (int) $this->moderation_status === 2;
    }

    /** Активные преимущества */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'slider_slide_advantages.activity',
            true
        );
    }

    /** Преимущества определённого типа иконки */
    public function scopeOfIconType(
        Builder $query,
        string $type
    ): Builder {
        return $query->where(
            'slider_slide_advantages.icon_type',
            $type
        );
    }

    /** Преимущества с иконками Lucide */
    public function scopeLucide(Builder $query): Builder
    {
        return $query->ofIconType('lucide');
    }

    /** Преимущества без иконок */
    public function scopeWithoutIcon(Builder $query): Builder
    {
        return $query->where(
            'slider_slide_advantages.icon_type',
            'none'
        );
    }

    /** Преимущества определённого оформления */
    public function scopeOfStyle(
        Builder $query,
        string $style
    ): Builder {
        return $query->where(
            'slider_slide_advantages.style',
            $style
        );
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('slider_slide_advantages.sort', 'asc')
            ->orderByDesc('slider_slide_advantages.id');
    }

    /**
     * Преимущества для публичного отображения.
     *
     * Проверяется активность преимущества.
     * Публичность родительского слайда и слайдера
     * контролируется на уровне их запросов.
     */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query->active()->approved();
    }

    /* =========================================================
     | SEARCH
     ========================================================= */

    /** Поиск для административного списка */
    public function scopeSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $locale ??= app()->getLocale();

        return $query->where(
            function (Builder $q) use ($term, $locale) {
                $q->where('slider_slide_advantages.moderation_note', 'like', "%{$term}%")
                    ->orWhere(
                        'slider_slide_advantages.icon_type',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'slider_slide_advantages.icon',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'slider_slide_advantages.style',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'slider_slide_advantages.icon_color',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhereHas(
                        'translations',
                        function (Builder $translationQuery) use (
                            $term,
                            $locale
                        ) {
                            $translationQuery
                                ->where('locale', $locale)
                                ->where(
                                    function (Builder $searchQuery) use ($term) {
                                        $searchQuery
                                            ->where(
                                                'title',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'text',
                                                'like',
                                                "%{$term}%"
                                            );
                                    }
                                );
                        }
                    );
            }
        );
    }

    /* =========================================================
     | SORTING
     ========================================================= */

    /** Сортировка административного списка */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale ??= app()->getLocale();

        return match ($sort) {
            /* ID */
            'idAsc' => $query
                ->orderBy('slider_slide_advantages.id', 'asc'),

            'idDesc' => $query
                ->orderBy('slider_slide_advantages.id', 'desc'),

            /* Порядок */
            'sortAsc' => $query
                ->orderBy('slider_slide_advantages.sort', 'asc')
                ->orderByDesc('slider_slide_advantages.id'),


            'sortDesc' => $query
                ->orderBy('slider_slide_advantages.sort', 'desc')
                ->orderByDesc('slider_slide_advantages.id'),


            /* Заголовок преимущества */
            'titleAsc' => $this->applyTitleSort(
                $query,
                $locale,
                'asc'
            ),

            'titleDesc' => $this->applyTitleSort(
                $query,
                $locale,
                'desc'
            ),

            /* Тип иконки */
            'iconTypeAsc' => $query
                ->orderBy('slider_slide_advantages.icon_type', 'asc')
                ->orderByDesc('slider_slide_advantages.id'),

            'iconTypeDesc' => $query
                ->orderBy('slider_slide_advantages.icon_type', 'desc')
                ->orderByDesc('slider_slide_advantages.id'),

            /* Название иконки */
            'iconAsc' => $query
                ->orderBy('slider_slide_advantages.icon', 'asc')
                ->orderByDesc('slider_slide_advantages.id'),

            'iconDesc' => $query
                ->orderBy('slider_slide_advantages.icon', 'desc')
                ->orderByDesc('slider_slide_advantages.id'),

            /* Вариант оформления */
            'styleAsc' => $query
                ->orderBy('slider_slide_advantages.style', 'asc')
                ->orderByDesc('slider_slide_advantages.id'),

            'styleDesc' => $query
                ->orderBy('slider_slide_advantages.style', 'desc')
                ->orderByDesc('slider_slide_advantages.id'),

            /* Статус модерации: сортировка, а не фильтрация. */
            'moderationStatusAsc' => $query
                ->orderBy('slider_slide_advantages.moderation_status', 'asc')
                ->orderByDesc('slider_slide_advantages.id'),

            'moderationStatusDesc' => $query
                ->orderBy('slider_slide_advantages.moderation_status', 'desc')
                ->orderByDesc('slider_slide_advantages.id'),

            /* Активность */

            'moderationPending' => $query
                ->where('slider_slide_advantages.moderation_status', 0)
                ->orderByDesc('slider_slide_advantages.id'),

            'moderationApproved' => $query
                ->where('slider_slide_advantages.moderation_status', 1)
                ->orderByDesc('slider_slide_advantages.id'),

            'moderationRejected' => $query
                ->where('slider_slide_advantages.moderation_status', 2)
                ->orderByDesc('slider_slide_advantages.id'),

            'activityAsc' => $query
                ->orderBy('slider_slide_advantages.activity', 'asc')
                ->orderByDesc('slider_slide_advantages.id'),

            'activityDesc' => $query
                ->orderBy('slider_slide_advantages.activity', 'desc')
                ->orderByDesc('slider_slide_advantages.id'),

            'activity' => $query
                ->where('slider_slide_advantages.activity', true)
                ->orderByDesc('slider_slide_advantages.id'),

            'inactive' => $query
                ->where('slider_slide_advantages.activity', false)
                ->orderByDesc('slider_slide_advantages.id'),

            /* Дата создания */
            'createdAtAsc', 'dateAsc' => $query
                ->orderBy('slider_slide_advantages.created_at', 'asc')
                ->orderByDesc('slider_slide_advantages.id'),

            'createdAtDesc', 'dateDesc' => $query
                ->orderBy('slider_slide_advantages.created_at', 'desc')
                ->orderByDesc('slider_slide_advantages.id'),

            /* Дата изменения */
            'updatedAtAsc' => $query
                ->orderBy('slider_slide_advantages.updated_at', 'asc')
                ->orderByDesc('slider_slide_advantages.id'),

            'updatedAtDesc' => $query
                ->orderBy('slider_slide_advantages.updated_at', 'desc')
                ->orderByDesc('slider_slide_advantages.id'),

            default => $query->ordered(),
        };
    }

    /**
     * Сортировка по заголовку: текущая локаль → fallback.
     * Коррелированные подзапросы не размножают строки при пагинации.
     */
    private function applyTitleSort(
        Builder $query,
        string $locale,
        string $direction
    ): Builder {
        $fallback = config('app.fallback_locale', 'ru');
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $currentTitle = SliderSlideAdvantageTranslation::query()
            ->select('title')
            ->whereColumn('slider_slide_advantage_id', 'slider_slide_advantages.id')
            ->where('locale', $locale)
            ->limit(1);

        $fallbackTitle = SliderSlideAdvantageTranslation::query()
            ->select('title')
            ->whereColumn('slider_slide_advantage_id', 'slider_slide_advantages.id')
            ->where('locale', $fallback)
            ->limit(1);

        $expression = 'COALESCE((' . $currentTitle->toSql() . '), ('
            . $fallbackTitle->toSql() . '))';

        return $query
            ->orderByRaw($expression . ' ' . $direction, [
                ...$currentTitle->getBindings(),
                ...$fallbackTitle->getBindings(),
            ])
            ->orderByDesc('slider_slide_advantages.id');
    }
}
