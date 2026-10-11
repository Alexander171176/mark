<?php

namespace App\Models\Admin\Slider\SliderSlideAdvantage;

use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SliderSlideAdvantage extends Model
{
    use HasFactory;

    protected $table = 'slider_slide_advantages';

    protected $fillable = [
        'slider_slide_id',
        'sort',
        'activity',
        'icon_type',
        'icon',
        'icon_color',
        'style',
        'settings',
    ];

    protected $casts = [
        'slider_slide_id' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* =========================================================
     | RELATIONS
     ========================================================= */

    /** Слайд, которому принадлежит преимущество */
    public function slide(): BelongsTo
    {
        return $this->belongsTo(
            SliderSlide::class,
            'slider_slide_id'
        );
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
            ->orderBy('slider_slide_advantages.id', 'asc');
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
        return $query->active();
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
                $q->where(
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
                ->orderBy('slider_slide_advantages.id', 'asc'),

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

            /* Активность */
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

    /** Сортировка по заголовку текущего перевода */
    private function applyTitleSort(
        Builder $query,
        string $locale,
        string $direction
    ): Builder {
        return $query
            ->leftJoin(
                'slider_slide_advantage_translations as sort_translations',
                function ($join) use ($locale) {
                    $join->on(
                        'sort_translations.slider_slide_advantage_id',
                        '=',
                        'slider_slide_advantages.id'
                    )->where(
                        'sort_translations.locale',
                        '=',
                        $locale
                    );
                }
            )
            ->addSelect('slider_slide_advantages.*')
            ->orderBy('sort_translations.title', $direction)
            ->orderByDesc('slider_slide_advantages.id');
    }
}
