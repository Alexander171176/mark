<?php

namespace App\Models\Admin\Slider\SliderSlide;

use App\Models\Admin\Slider\Slider\Slider;
use App\Models\Admin\Slider\SliderSlideAction\SliderSlideAction;
use App\Models\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SliderSlide extends Model
{
    use HasFactory;

    protected $table = 'slider_slides';

    protected $fillable = [
        'slider_id',
        'activity',
        'sort',
        'is_main',
        'status',
        'published_at',
        'show_from_at',
        'show_to_at',
        'background_color',
        'text_color',
        'accent_color',
        'overlay_color',
        'overlay_opacity',
        'image_position',
        'content_position',
        'animation_type',
        'animation_duration',
        'animation_delay',
        'animation_stagger',
        'animation_once',
        'animation_settings',
        'settings',
    ];

    protected $casts = [
        'slider_id' => 'integer',
        'activity' => 'boolean',
        'sort' => 'integer',
        'is_main' => 'boolean',
        'published_at' => 'datetime',
        'show_from_at' => 'datetime',
        'show_to_at' => 'datetime',
        'overlay_opacity' => 'integer',
        'animation_duration' => 'integer',
        'animation_delay' => 'integer',
        'animation_stagger' => 'integer',
        'animation_once' => 'boolean',
        'animation_settings' => 'array',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* =========================================================
     | RELATIONS
     ========================================================= */

    /** Родительский слайдер */
    public function slider(): BelongsTo
    {
        return $this->belongsTo(
            Slider::class,
            'slider_id'
        );
    }

    /** Все переводы слайда */
    public function translations(): HasMany
    {
        return $this->hasMany(
            SliderSlideTranslation::class,
            'slider_slide_id'
        );
    }

    /** Перевод текущего языка */
    public function translation(): HasOne
    {
        return $this->hasOne(
            SliderSlideTranslation::class,
            'slider_slide_id'
        )->where('locale', app()->getLocale());
    }

    /**
     * Перевод слайда:
     * текущая локаль → fallback → null.
     *
     * Использует заранее загруженные translations.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallback = null
    ): ?SliderSlideTranslation {
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

    /** Изображения слайда */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SliderSlideImage::class,
            'slider_slide_has_images',
            'slider_slide_id',
            'slider_slide_image_id'
        )
            ->withPivot('purpose', 'order')
            ->orderByPivot('order');
    }

    /** Изображения для компьютеров */
    public function desktopImages(): BelongsToMany
    {
        return $this->images()
            ->wherePivot('purpose', 'desktop');
    }

    /** Изображения для мобильных устройств */
    public function mobileImages(): BelongsToMany
    {
        return $this->images()
            ->wherePivot('purpose', 'mobile');
    }

    /** Кнопки и действия слайда */
    public function actions(): HasMany
    {
        return $this->hasMany(
            SliderSlideAction::class,
            'slider_slide_id'
        )
            ->orderBy('sort')
            ->orderBy('id');
    }

    /** Преимущества слайда */
    public function advantages(): HasMany
    {
        return $this->hasMany(
            SliderSlideAdvantage::class,
            'slider_slide_id'
        )
            ->orderBy('sort')
            ->orderBy('id');
    }

    /* =========================================================
     | BASE SCOPES
     ========================================================= */

    /** Активные слайды */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'slider_slides.activity',
            true
        );
    }

    /** Опубликованные слайды */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('slider_slides.status', 'published')
            ->where('slider_slides.activity', true)
            ->whereNotNull('slider_slides.published_at')
            ->where('slider_slides.published_at', '<=', now());
    }

    /** Главные слайды с заголовком h1 */
    public function scopeMain(Builder $query): Builder
    {
        return $query->where(
            'slider_slides.is_main',
            true
        );
    }

    /** Сортировка по умолчанию */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('slider_slides.sort', 'asc')
            ->orderBy('slider_slides.id', 'asc');
    }

    /** Период отображения слайда */
    public function scopeInShowWindow(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull('slider_slides.show_from_at')
                    ->orWhere(
                        'slider_slides.show_from_at',
                        '<=',
                        now()
                    );
            })
            ->where(function (Builder $q) {
                $q->whereNull('slider_slides.show_to_at')
                    ->orWhere(
                        'slider_slides.show_to_at',
                        '>=',
                        now()
                    );
            });
    }

    /**
     * Публичные слайды.
     *
     * Проверяет собственные условия публикации.
     * Условия родительского слайдера проверяются
     * отдельно при получении слайдера по code.
     */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->published()
            ->inShowWindow();
    }

    /**
     * Публичные слайды вместе с проверкой
     * родительского слайдера.
     */
    public function scopeWithPublicSlider(Builder $query): Builder
    {
        return $query
            ->forPublic()
            ->whereHas(
                'slider',
                fn (Builder $sliderQuery) =>
                $sliderQuery->forPublic()
            );
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
                    'slider_slides.status',
                    'like',
                    "%{$term}%"
                )
                    ->orWhere(
                        'slider_slides.animation_type',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'slider_slides.content_position',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhereHas(
                        'translations',
                        function (Builder $translationQuery) use ($term, $locale) {
                            $translationQuery
                                ->where('locale', $locale)
                                ->where(function (Builder $searchQuery) use ($term) {
                                    $searchQuery
                                        ->where('label', 'like', "%{$term}%")
                                        ->orWhere('title', 'like', "%{$term}%")
                                        ->orWhere('accent', 'like', "%{$term}%")
                                        ->orWhere('description', 'like', "%{$term}%");
                                });
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
            'idAsc' => $query
                ->orderBy('slider_slides.id', 'asc'),

            'idDesc' => $query
                ->orderBy('slider_slides.id', 'desc'),

            'sortAsc' => $query
                ->orderBy('slider_slides.sort', 'asc')
                ->orderBy('slider_slides.id', 'asc'),

            'sortDesc' => $query
                ->orderBy('slider_slides.sort', 'desc')
                ->orderByDesc('slider_slides.id'),

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

            'statusAsc' => $query
                ->orderBy('slider_slides.status', 'asc')
                ->orderByDesc('slider_slides.id'),

            'statusDesc' => $query
                ->orderBy('slider_slides.status', 'desc')
                ->orderByDesc('slider_slides.id'),

            'statusDraft' => $query
                ->where('slider_slides.status', 'draft')
                ->orderByDesc('slider_slides.id'),

            'statusPublished' => $query
                ->where('slider_slides.status', 'published')
                ->orderByDesc('slider_slides.id'),

            'statusArchived' => $query
                ->where('slider_slides.status', 'archived')
                ->orderByDesc('slider_slides.id'),

            'activity' => $query
                ->where('slider_slides.activity', true)
                ->orderByDesc('slider_slides.id'),

            'inactive' => $query
                ->where('slider_slides.activity', false)
                ->orderByDesc('slider_slides.id'),

            'main' => $query
                ->where('slider_slides.is_main', true)
                ->orderByDesc('slider_slides.id'),

            'notMain' => $query
                ->where('slider_slides.is_main', false)
                ->orderByDesc('slider_slides.id'),

            'publishedAtAsc' => $query
                ->orderBy('slider_slides.published_at', 'asc')
                ->orderByDesc('slider_slides.id'),

            'publishedAtDesc' => $query
                ->orderBy('slider_slides.published_at', 'desc')
                ->orderByDesc('slider_slides.id'),

            'showFromAtAsc' => $query
                ->orderBy('slider_slides.show_from_at', 'asc')
                ->orderByDesc('slider_slides.id'),

            'showFromAtDesc' => $query
                ->orderBy('slider_slides.show_from_at', 'desc')
                ->orderByDesc('slider_slides.id'),

            'showToAtAsc' => $query
                ->orderBy('slider_slides.show_to_at', 'asc')
                ->orderByDesc('slider_slides.id'),

            'showToAtDesc' => $query
                ->orderBy('slider_slides.show_to_at', 'desc')
                ->orderByDesc('slider_slides.id'),

            'createdAtAsc', 'dateAsc' => $query
                ->orderBy('slider_slides.created_at', 'asc')
                ->orderByDesc('slider_slides.id'),

            'createdAtDesc', 'dateDesc' => $query
                ->orderBy('slider_slides.created_at', 'desc')
                ->orderByDesc('slider_slides.id'),

            'updatedAtAsc' => $query
                ->orderBy('slider_slides.updated_at', 'asc')
                ->orderByDesc('slider_slides.id'),

            'updatedAtDesc' => $query
                ->orderBy('slider_slides.updated_at', 'desc')
                ->orderByDesc('slider_slides.id'),

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
                'slider_slide_translations as sort_translations',
                function ($join) use ($locale) {
                    $join->on(
                        'sort_translations.slider_slide_id',
                        '=',
                        'slider_slides.id'
                    )->where(
                        'sort_translations.locale',
                        '=',
                        $locale
                    );
                }
            )
            ->addSelect('slider_slides.*')
            ->orderBy('sort_translations.title', $direction)
            ->orderByDesc('slider_slides.id');
    }
}
