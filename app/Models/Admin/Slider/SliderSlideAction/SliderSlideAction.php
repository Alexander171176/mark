<?php

namespace App\Models\Admin\Slider\SliderSlideAction;

use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SliderSlideAction extends Model
{
    use HasFactory;

    protected $table = 'slider_slide_actions';

    protected $fillable = [
        'slider_slide_id',
        'sort',
        'activity',
        'is_primary',
        'action_type',
        'action_value',
        'route_params',
        'target',
        'style',
        'icon',
        'icon_position',
        'css_class',
        'settings',
    ];

    protected $casts = [
        'slider_slide_id' => 'integer',
        'sort' => 'integer',
        'activity' => 'boolean',
        'is_primary' => 'boolean',
        'route_params' => 'array',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* =========================================================
     | RELATIONS
     ========================================================= */

    /** Слайд, которому принадлежит кнопка */
    public function slide(): BelongsTo
    {
        return $this->belongsTo(
            SliderSlide::class,
            'slider_slide_id'
        );
    }

    /** Все переводы кнопки */
    public function translations(): HasMany
    {
        return $this->hasMany(
            SliderSlideActionTranslation::class,
            'slider_slide_action_id'
        );
    }

    /** Перевод кнопки текущего языка */
    public function translation(): HasOne
    {
        return $this->hasOne(
            SliderSlideActionTranslation::class,
            'slider_slide_action_id'
        )->where(
            'locale',
            app()->getLocale()
        );
    }

    /**
     * Перевод кнопки:
     * текущая локаль → fallback → null.
     *
     * Использует заранее загруженные translations,
     * не выполняя дополнительных запросов к БД.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallback = null
    ): ?SliderSlideActionTranslation {
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

    /** Активные кнопки */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'slider_slide_actions.activity',
            true
        );
    }

    /** Основные кнопки */
    public function scopePrimary(Builder $query): Builder
    {
        return $query->where(
            'slider_slide_actions.is_primary',
            true
        );
    }

    /** Второстепенные кнопки */
    public function scopeSecondary(Builder $query): Builder
    {
        return $query->where(
            'slider_slide_actions.is_primary',
            false
        );
    }

    /** Кнопки определённого типа действия */
    public function scopeOfType(
        Builder $query,
        string $type
    ): Builder {
        return $query->where(
            'slider_slide_actions.action_type',
            $type
        );
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('slider_slide_actions.sort', 'asc')
            ->orderBy('slider_slide_actions.id', 'asc');
    }

    /**
     * Кнопки для публичного отображения.
     *
     * Проверяется активность самой кнопки.
     * Публикация слайда и родительского слайдера
     * контролируется на уровне их запросов.
     */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query->active();
    }

    /* =========================================================
     | SEARCH
     ========================================================= */

    /** Поиск кнопок для административного списка */
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
                    'slider_slide_actions.action_type',
                    'like',
                    "%{$term}%"
                )
                    ->orWhere(
                        'slider_slide_actions.action_value',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'slider_slide_actions.style',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'slider_slide_actions.icon',
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
                                                'label',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'title',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'aria_label',
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
                ->orderBy('slider_slide_actions.id', 'asc'),

            'idDesc' => $query
                ->orderBy('slider_slide_actions.id', 'desc'),

            /* Порядок */
            'sortAsc' => $query
                ->orderBy('slider_slide_actions.sort', 'asc')
                ->orderBy('slider_slide_actions.id', 'asc'),

            'sortDesc' => $query
                ->orderBy('slider_slide_actions.sort', 'desc')
                ->orderByDesc('slider_slide_actions.id'),

            /* Название кнопки */
            'labelAsc' => $this->applyLabelSort(
                $query,
                $locale,
                'asc'
            ),

            'labelDesc' => $this->applyLabelSort(
                $query,
                $locale,
                'desc'
            ),

            /* Тип действия */
            'actionTypeAsc' => $query
                ->orderBy('slider_slide_actions.action_type', 'asc')
                ->orderByDesc('slider_slide_actions.id'),

            'actionTypeDesc' => $query
                ->orderBy('slider_slide_actions.action_type', 'desc')
                ->orderByDesc('slider_slide_actions.id'),

            /* Значение действия */
            'actionValueAsc' => $query
                ->orderBy('slider_slide_actions.action_value', 'asc')
                ->orderByDesc('slider_slide_actions.id'),

            'actionValueDesc' => $query
                ->orderBy('slider_slide_actions.action_value', 'desc')
                ->orderByDesc('slider_slide_actions.id'),

            /* Активность */
            'activityAsc' => $query
                ->orderBy('slider_slide_actions.activity', 'asc')
                ->orderByDesc('slider_slide_actions.id'),

            'activityDesc' => $query
                ->orderBy('slider_slide_actions.activity', 'desc')
                ->orderByDesc('slider_slide_actions.id'),

            'activity' => $query
                ->where('slider_slide_actions.activity', true)
                ->orderByDesc('slider_slide_actions.id'),

            'inactive' => $query
                ->where('slider_slide_actions.activity', false)
                ->orderByDesc('slider_slide_actions.id'),

            /* Основная кнопка */
            'primary' => $query
                ->where('slider_slide_actions.is_primary', true)
                ->orderByDesc('slider_slide_actions.id'),

            'secondary' => $query
                ->where('slider_slide_actions.is_primary', false)
                ->orderByDesc('slider_slide_actions.id'),

            /* Дата создания */
            'createdAtAsc', 'dateAsc' => $query
                ->orderBy('slider_slide_actions.created_at', 'asc')
                ->orderByDesc('slider_slide_actions.id'),

            'createdAtDesc', 'dateDesc' => $query
                ->orderBy('slider_slide_actions.created_at', 'desc')
                ->orderByDesc('slider_slide_actions.id'),

            /* Дата изменения */
            'updatedAtAsc' => $query
                ->orderBy('slider_slide_actions.updated_at', 'asc')
                ->orderByDesc('slider_slide_actions.id'),

            'updatedAtDesc' => $query
                ->orderBy('slider_slide_actions.updated_at', 'desc')
                ->orderByDesc('slider_slide_actions.id'),

            default => $query->ordered(),
        };
    }

    /** Сортировка по тексту кнопки текущего языка */
    private function applyLabelSort(
        Builder $query,
        string $locale,
        string $direction
    ): Builder {
        return $query
            ->leftJoin(
                'slider_slide_action_translations as sort_translations',
                function ($join) use ($locale) {
                    $join->on(
                        'sort_translations.slider_slide_action_id',
                        '=',
                        'slider_slide_actions.id'
                    )->where(
                        'sort_translations.locale',
                        '=',
                        $locale
                    );
                }
            )
            ->addSelect('slider_slide_actions.*')
            ->orderBy('sort_translations.label', $direction)
            ->orderByDesc('slider_slide_actions.id');
    }
}
