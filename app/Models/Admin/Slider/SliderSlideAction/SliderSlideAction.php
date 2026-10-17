<?php

namespace App\Models\Admin\Slider\SliderSlideAction;

use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SliderSlideAction extends Model
{
    use HasFactory;

    protected $table = 'slider_slide_actions';

    protected $fillable = [
        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',
        'sort',
        'activity',
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
        'moderation_status' => 'integer',
        'moderated_by' => 'integer',
        'moderated_at' => 'datetime',
        'sort' => 'integer',
        'activity' => 'boolean',
        'route_params' => 'array',
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

    /** Слайды, использующие кнопку */
    public function slides(): BelongsToMany
    {
        return $this->belongsToMany(
            SliderSlide::class,
            'slider_slide_has_actions',
            'slider_slide_action_id',
            'slider_slide_id'
        )
            ->withPivot(['id', 'sort', 'activity', 'is_primary'])
            ->withTimestamps()
            ->orderBy('slider_slides.id');
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

        return $this->translations->first();
    }

    /* =========================================================
     | BASE SCOPES
     ========================================================= */

    /** Одобренные записи. */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('slider_slide_actions.moderation_status', 1);
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
        return $query->whereHas('slides', fn (Builder $slideQuery) =>
        $slideQuery->where('slider_slide_has_actions.is_primary', true)
        );
    }

    /** Второстепенные кнопки */
    public function scopeSecondary(Builder $query): Builder
    {
        return $query->whereHas('slides', fn (Builder $slideQuery) =>
        $slideQuery->where('slider_slide_has_actions.is_primary', false)
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
            ->orderByDesc('slider_slide_actions.id');
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
        return $query->active()->approved();
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
                $q->where('slider_slide_actions.moderation_note', 'like', "%{$term}%")
                    ->orWhere(
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
                ->orderByDesc('slider_slide_actions.id'),


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

            /* Модерация */
            'moderationStatusAsc' => $query
                ->orderBy('slider_slide_actions.moderation_status', 'asc')
                ->orderByDesc('slider_slide_actions.id'),

            'moderationStatusDesc' => $query
                ->orderBy('slider_slide_actions.moderation_status', 'desc')
                ->orderByDesc('slider_slide_actions.id'),

            'moderationPending' => $query
                ->where('slider_slide_actions.moderation_status', 0)
                ->orderByDesc('slider_slide_actions.id'),

            'moderationApproved' => $query
                ->where('slider_slide_actions.moderation_status', 1)
                ->orderByDesc('slider_slide_actions.id'),

            'moderationRejected' => $query
                ->where('slider_slide_actions.moderation_status', 2)
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
                ->primary()
                ->orderByDesc('slider_slide_actions.id'),

            'secondary' => $query
                ->secondary()
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

    /**
     * Сортировка по label с учётом текущей и резервной локалей.
     *
     * Коррелированные подзапросы не размножают строки основной таблицы.
     * Результат совместим с paginate() и withCount().
     */
    private function applyLabelSort(
        Builder $query,
        string $locale,
        string $direction
    ): Builder {
        $fallback = config('app.fallback_locale', 'ru');

        $currentLabel = SliderSlideActionTranslation::query()
            ->select('label')
            ->whereColumn(
                'slider_slide_action_translations.slider_slide_action_id',
                'slider_slide_actions.id'
            )
            ->where('locale', $locale)
            ->limit(1);

        $fallbackLabel = SliderSlideActionTranslation::query()
            ->select('label')
            ->whereColumn(
                'slider_slide_action_translations.slider_slide_action_id',
                'slider_slide_actions.id'
            )
            ->where('locale', $fallback)
            ->limit(1);

        // Встроенные параметры запросов передаются через bindings, не конкатенацией SQL.
        $sql = 'COALES((' . $currentLabel->toSql() . '), ('
            . $fallbackLabel->toSql() . '))';

        $query->orderByRaw(
            $sql . ($direction === 'desc' ? ' DESC' : ' ASC'),
            array_merge($currentLabel->getBindings(), $fallbackLabel->getBindings())
        );

        return $query->orderByDesc('slider_slide_actions.id');
    }
}
