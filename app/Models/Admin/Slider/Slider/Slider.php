<?php

namespace App\Models\Admin\Slider\Slider;

use App\Models\Admin\Slider\SliderSlide\SliderSlide;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Slider extends Model
{
    use HasFactory;

    protected $table = 'sliders';

    protected $fillable = [
        'user_id',
        'code',
        'type',
        'status',
        'moderation_status',
        'activity',
        'sort',
        'published_at',
        'show_from_at',
        'show_to_at',
        'autoplay_delay',
        'autoplay_disable_on_interaction',
        'pause_on_hover',
        'effect',
        'speed',
        'effect_options',
        'loop',
        'keyboard',
        'allow_touch_move',
        'grab_cursor',
        'slides_per_view',
        'space_between',
        'auto_height',
        'breakpoints',
        'show_navigation',
        'show_pagination',
        'settings',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'moderation_status' => 'integer',
        'activity' => 'boolean',
        'sort' => 'integer',
        'published_at' => 'datetime',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',
        'autoplay_delay' => 'integer',
        'autoplay_disable_on_interaction' => 'boolean',
        'pause_on_hover' => 'boolean',
        'speed' => 'integer',
        'effect_options' => 'array',
        'loop' => 'boolean',
        'keyboard' => 'boolean',
        'allow_touch_move' => 'boolean',
        'grab_cursor' => 'boolean',
        'slides_per_view' => 'decimal:2',
        'space_between' => 'integer',
        'auto_height' => 'boolean',
        'breakpoints' => 'array',
        'show_navigation' => 'boolean',
        'show_pagination' => 'boolean',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* =========================================================
     | RELATIONS
     ========================================================= */

    /** Пользователь, создавший слайдер */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Все переводы слайдера */
    public function translations(): HasMany
    {
        return $this->hasMany(
            SliderTranslation::class,
            'slider_id'
        );
    }

    /** Перевод текущего языка */
    public function translation(): HasOne
    {
        return $this->hasOne(
            SliderTranslation::class,
            'slider_id'
        )->where('locale', app()->getLocale());
    }

    /**
     * Перевод текущего языка с fallback.
     *
     * Использует заранее загруженные translations,
     * чтобы избежать дополнительных запросов к БД.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallback = null
    ): ?SliderTranslation {
        if (! $this->relationLoaded('translations')) {
            return null;
        }

        $locale ??= app()->getLocale();

        $fallback ??= config('app.fallback_locale', 'ru');

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

    /** Слайды слайдера */
    public function slides(): HasMany
    {
        return $this->hasMany(
            SliderSlide::class,
            'slider_id'
        )
            ->orderBy('sort')
            ->orderBy('id');
    }

    /* =========================================================
     | BASE SCOPES
     ========================================================= */

    /** Активные слайдеры */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('sliders.activity', true);
    }

    /** Опубликованные слайдеры */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('sliders.status', 'published')
            ->where('sliders.activity', true)
            ->whereNotNull('sliders.published_at')
            ->where('sliders.published_at', '<=', now());
    }

    /** Одобренные слайдеры */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where(
            'sliders.moderation_status',
            1
        );
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sliders.sort', 'asc')
            ->orderByDesc('sliders.id');
    }

    /** Период отображения */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull('sliders.show_from_at')
                    ->orWhere('sliders.show_from_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('sliders.show_to_at')
                    ->orWhere('sliders.show_to_at', '>=', now());
            });
    }

    /** Слайдеры, доступные для публичного отображения */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /** Поиск слайдера по уникальному коду */
    public function scopeByCode(
        Builder $query,
        string $code
    ): Builder {
        return $query->where('sliders.code', $code);
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
                $q->where('sliders.code', 'like', "%{$term}%")
                    ->orWhere('sliders.type', 'like', "%{$term}%")
                    ->orWhere('sliders.status', 'like', "%{$term}%")
                    ->orWhereHas(
                        'translations',
                        function (Builder $translationQuery) use ($term, $locale) {
                            $translationQuery
                                ->where('locale', $locale)
                                ->where(function (Builder $searchQuery) use ($term) {
                                    $searchQuery
                                        ->where('title', 'like', "%{$term}%")
                                        ->orWhere('subtitle', 'like', "%{$term}%")
                                        ->orWhere('description', 'like', "%{$term}%");
                                });
                        }
                    )
                    ->orWhereHas(
                        'owner',
                        function (Builder $ownerQuery) use ($term) {
                            $ownerQuery
                                ->where('name', 'like', "%{$term}%")
                                ->orWhere('email', 'like', "%{$term}%");
                        }
                    );
            }
        );
    }

    /* =========================================================
     | SORTING
     ========================================================= */

    /** Сортировка для административного списка */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale ??= app()->getLocale();

        return match ($sort) {
            'idAsc' => $query->orderBy('sliders.id', 'asc'),
            'idDesc' => $query->orderBy('sliders.id', 'desc'),

            'sortAsc' => $query
                ->orderBy('sliders.sort', 'asc')
                ->orderByDesc('sliders.id'),

            'sortDesc' => $query
                ->orderBy('sliders.sort', 'desc')
                ->orderByDesc('sliders.id'),

            'codeAsc' => $query
                ->orderBy('sliders.code', 'asc')
                ->orderByDesc('sliders.id'),

            'codeDesc' => $query
                ->orderBy('sliders.code', 'desc')
                ->orderByDesc('sliders.id'),

            'typeAsc' => $query
                ->orderBy('sliders.type', 'asc')
                ->orderByDesc('sliders.id'),

            'typeDesc' => $query
                ->orderBy('sliders.type', 'desc')
                ->orderByDesc('sliders.id'),

            'titleAsc' => $this->applyTitleSort($query, $locale, 'asc'),
            'titleDesc' => $this->applyTitleSort($query, $locale, 'desc'),

            'statusAsc' => $query
                ->orderBy('sliders.status', 'asc')
                ->orderByDesc('sliders.id'),

            'statusDesc' => $query
                ->orderBy('sliders.status', 'desc')
                ->orderByDesc('sliders.id'),

            'statusDraft' => $query
                ->where('sliders.status', 'draft')
                ->orderByDesc('sliders.id'),

            'statusPublished' => $query
                ->where('sliders.status', 'published')
                ->orderByDesc('sliders.id'),

            'statusArchived' => $query
                ->where('sliders.status', 'archived')
                ->orderByDesc('sliders.id'),

            'moderationPending' => $query
                ->where('sliders.moderation_status', 0)
                ->orderByDesc('sliders.id'),

            'moderationApproved' => $query
                ->where('sliders.moderation_status', 1)
                ->orderByDesc('sliders.id'),

            'moderationRejected' => $query
                ->where('sliders.moderation_status', 2)
                ->orderByDesc('sliders.id'),

            'activity' => $query
                ->where('sliders.activity', true)
                ->orderByDesc('sliders.id'),

            'inactive' => $query
                ->where('sliders.activity', false)
                ->orderByDesc('sliders.id'),

            'publishedAtAsc' => $query
                ->orderBy('sliders.published_at', 'asc')
                ->orderByDesc('sliders.id'),

            'publishedAtDesc' => $query
                ->orderBy('sliders.published_at', 'desc')
                ->orderByDesc('sliders.id'),

            'showFromAtAsc' => $query
                ->orderBy('sliders.show_from_at', 'asc')
                ->orderByDesc('sliders.id'),

            'showFromAtDesc' => $query
                ->orderBy('sliders.show_from_at', 'desc')
                ->orderByDesc('sliders.id'),

            'showToAtAsc' => $query
                ->orderBy('sliders.show_to_at', 'asc')
                ->orderByDesc('sliders.id'),

            'showToAtDesc' => $query
                ->orderBy('sliders.show_to_at', 'desc')
                ->orderByDesc('sliders.id'),

            'createdAtAsc', 'dateAsc' => $query
                ->orderBy('sliders.created_at', 'asc')
                ->orderByDesc('sliders.id'),

            'createdAtDesc', 'dateDesc' => $query
                ->orderBy('sliders.created_at', 'desc')
                ->orderByDesc('sliders.id'),

            'updatedAtAsc' => $query
                ->orderBy('sliders.updated_at', 'asc')
                ->orderByDesc('sliders.id'),

            'updatedAtDesc' => $query
                ->orderBy('sliders.updated_at', 'desc')
                ->orderByDesc('sliders.id'),

            'ownerNameAsc' => $this->applyOwnerSort($query, 'name', 'asc'),
            'ownerNameDesc' => $this->applyOwnerSort($query, 'name', 'desc'),

            'ownerEmailAsc' => $this->applyOwnerSort($query, 'email', 'asc'),
            'ownerEmailDesc' => $this->applyOwnerSort($query, 'email', 'desc'),

            default => $query->ordered(),
        };
    }

    /** Сортировка по названию текущего перевода */
    private function applyTitleSort(
        Builder $query,
        string $locale,
        string $direction
    ): Builder {
        return $query
            ->leftJoin(
                'slider_translations as sort_translations',
                function ($join) use ($locale) {
                    $join->on(
                        'sort_translations.slider_id',
                        '=',
                        'sliders.id'
                    )->where(
                        'sort_translations.locale',
                        '=',
                        $locale
                    );
                }
            )
            ->addSelect('sliders.*')
            ->orderBy('sort_translations.title', $direction)
            ->orderByDesc('sliders.id');
    }

    /** Сортировка по владельцу */
    private function applyOwnerSort(
        Builder $query,
        string $field,
        string $direction
    ): Builder {
        return $query
            ->leftJoin(
                'users as owner_sort',
                'owner_sort.id',
                '=',
                'sliders.user_id'
            )
            ->addSelect('sliders.*')
            ->orderBy("owner_sort.{$field}", $direction)
            ->orderByDesc('sliders.id');
    }
}
