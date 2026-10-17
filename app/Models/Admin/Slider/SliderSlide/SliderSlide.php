<?php

namespace App\Models\Admin\Slider\SliderSlide;

use App\Models\Admin\Slider\Slider\Slider;
use App\Models\Admin\Slider\SliderSlideAction\SliderSlideAction;
use App\Models\Admin\Slider\SliderSlideAdvantage\SliderSlideAdvantage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SliderSlide extends Model
{
    use HasFactory;

    protected $table = 'slider_slides';

    protected $fillable = [
        'moderation_status',
        'moderated_by',
        'moderated_at',
        'moderation_note',
        'sort',
        'activity',
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
        'moderation_status' => 'integer',
        'moderated_by' => 'integer',
        'moderated_at' => 'datetime',
        'sort' => 'integer',
        'activity' => 'boolean',
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

    /** Пользователь, выполнивший модерацию. */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /** Слайдеры, в которых используется данный слайд. */
    public function sliders(): BelongsToMany
    {
        return $this->belongsToMany(
            Slider::class,
            'slider_has_slides',
            'slider_slide_id',
            'slider_id'
        )
            ->withPivot(['id', 'sort', 'activity', 'is_main'])
            ->withTimestamps()
            ->orderBy('sliders.id');
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

    /** Изображения слайда; media загружается отдельно через images.media. */
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

    /** Кнопки, подключённые к слайду. */
    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(
            SliderSlideAction::class,
            'slider_slide_has_actions',
            'slider_slide_id',
            'slider_slide_action_id'
        )
            ->withPivot(['id', 'sort', 'activity', 'is_primary'])
            ->withTimestamps()
            ->orderByPivot('sort')
            ->orderBy('slider_slide_actions.id');
    }

    /** Преимущества, подключённые к слайду. */
    public function advantages(): BelongsToMany
    {
        return $this->belongsToMany(
            SliderSlideAdvantage::class,
            'slider_slide_has_advantages',
            'slider_slide_id',
            'slider_slide_advantage_id'
        )
            ->withPivot(['id', 'sort', 'activity'])
            ->withTimestamps()
            ->orderByPivot('sort')
            ->orderBy('slider_slide_advantages.id');
    }

    /* =========================================================
     | BASE SCOPES
     ========================================================= */

    /** Одобренные записи. */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('slider_slides.moderation_status', 1);
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

    /** Слайды, отмеченные как главные хотя бы в одном слайдере. */
    public function scopeMain(Builder $query): Builder
    {
        return $query->whereHas('sliders', function (Builder $sliderQuery) {
            $sliderQuery->where('slider_has_slides.is_main', true);
        });
    }

    /** Сортировка самостоятельного каталога слайдов. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('slider_slides.sort', 'asc')
            ->orderByDesc('slider_slides.id');
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
            ->approved()
            ->published()
            ->inShowWindow();
    }

    /**
     * Публичные слайды, подключённые хотя бы к одному публичному
     * слайдеру через активную связь.
     */
    public function scopeWithPublicSlider(Builder $query): Builder
    {
        return $query
            ->forPublic()
            ->whereHas('sliders', function (Builder $sliderQuery) {
                $sliderQuery->forPublic()
                    ->where('slider_has_slides.activity', true);
            });
    }

    /* =========================================================
     | SEARCH
     ========================================================= */

    /** Серверный поиск для режима server; frontend фильтрует полученную коллекцию в Vue. */
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
                $q->where('slider_slides.moderation_note', 'like', "%{$term}%")
                    ->orWhere(
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

    /** Серверная сортировка для режима server; frontend сортирует в Vue. */
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
                ->orderByDesc('slider_slides.id'),


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

            'moderationPending' => $query
                ->where('slider_slides.moderation_status', 0)
                ->orderByDesc('slider_slides.id'),

            'moderationApproved' => $query
                ->where('slider_slides.moderation_status', 1)
                ->orderByDesc('slider_slides.id'),

            'moderationRejected' => $query
                ->where('slider_slides.moderation_status', 2)
                ->orderByDesc('slider_slides.id'),


            'activity' => $query
                ->where('slider_slides.activity', true)
                ->orderByDesc('slider_slides.id'),

            'inactive' => $query
                ->where('slider_slides.activity', false)
                ->orderByDesc('slider_slides.id'),

            'main' => $query->main()->orderByDesc('slider_slides.id'),

            'notMain' => $query->whereDoesntHave('sliders', function (Builder $sliderQuery) {
                $sliderQuery->where('slider_has_slides.is_main', true);
            })->orderByDesc('slider_slides.id'),

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

    /** Проверка глобальной активности без запроса к БД. */
    public function isActive(): bool
    {
        return (bool) $this->activity;
    }

    /** Проверка состояния публикации без запроса к БД. */
    public function isPublished(): bool
    {
        return $this->status === 'published';
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
