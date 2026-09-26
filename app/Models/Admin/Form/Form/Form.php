<?php

namespace App\Models\Admin\Form\Form;

use App\Models\Admin\Form\FormField\FormField;
use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Form extends Model
{
    use HasFactory;

    protected $table = 'forms';

    /*
    |--------------------------------------------------------------------------
    | Statuses
    |--------------------------------------------------------------------------
    */

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    /*
    |--------------------------------------------------------------------------
    | Mass assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'code',
        'status',
        'activity',
        'sort',

        // Защита от спама
        'spam_protection',
        'honeypot_enabled',
        'min_submit_seconds',
        'rate_limit',
        'rate_limit_minutes',
        'captcha_enabled',

        // Поведение формы
        'auth_required',

        // Дополнительные настройки
        'settings',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'user_id' => 'integer',

        'activity' => 'boolean',
        'sort' => 'integer',

        'spam_protection' => 'boolean',
        'honeypot_enabled' => 'boolean',
        'min_submit_seconds' => 'integer',
        'rate_limit' => 'integer',
        'rate_limit_minutes' => 'integer',
        'captcha_enabled' => 'boolean',

        'auth_required' => 'boolean',

        'settings' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Владелец формы.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Все переводы формы.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(FormTranslation::class);
    }

    /**
     * Перевод для текущей локали приложения.
     *
     * В отличие от translationOrFallback(),
     * fallback здесь намеренно не применяется.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(FormTranslation::class)
            ->where('locale', app()->getLocale());
    }

    /**
     * Все поля формы.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)
            ->orderBy('sort')
            ->orderBy('id');
    }

    /**
     * Только активные поля формы.
     */
    public function activeFields(): HasMany
    {
        return $this->hasMany(FormField::class)
            ->where('activity', true)
            ->orderBy('sort')
            ->orderBy('id');
    }

    /**
     * Заявки, отправленные через форму.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Translation helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить перевод указанной локали.
     *
     * Порядок поиска:
     * 1. указанная / текущая локаль;
     * 2. fallback locale приложения;
     * 3. первый доступный перевод.
     *
     * Если relation translations уже загружена,
     * дополнительный SQL-запрос не выполняется.
     */
    public function translationOrFallback(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?FormTranslation {
        $locale ??= app()->getLocale();
        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        $translations = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->get();

        $translation = $translations->firstWhere(
            'locale',
            $locale
        );

        if ($translation) {
            return $translation;
        }

        if ($fallbackLocale !== $locale) {
            $fallback = $translations->firstWhere(
                'locale',
                $fallbackLocale
            );

            if ($fallback) {
                return $fallback;
            }
        }

        return $translations->first();
    }

    /**
     * Получить перевод указанной локали
     * только из уже загруженной relation translations.
     *
     * Метод никогда не выполняет SQL-запрос.
     *
     * Порядок поиска:
     * 1. указанная / текущая локаль;
     * 2. fallback locale приложения;
     * 3. первый доступный перевод.
     */
    public function loadedTranslationOrFallback(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?FormTranslation {
        if (!$this->relationLoaded('translations')) {
            return null;
        }

        $locale ??= app()->getLocale();

        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $this->translations->firstWhere(
            'locale',
            $locale
        );

        if ($translation) {
            return $translation;
        }

        if ($fallbackLocale !== $locale) {
            $fallback = $this->translations->firstWhere(
                'locale',
                $fallbackLocale
            );

            if ($fallback) {
                return $fallback;
            }
        }

        return $this->translations->first();
    }

    /**
     * Получить переведённое название формы.
     */
    public function getTranslatedTitle(
        ?string $locale = null,
        ?string $fallbackLocale = null
    ): ?string {
        return $this
            ->translationOrFallback(
                $locale,
                $fallbackLocale
            )
            ?->title;
    }

    /*
    |--------------------------------------------------------------------------
    | Status helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Только активные формы.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(
            'activity',
            true
        );
    }

    /**
     * Только опубликованные формы.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_PUBLISHED
        );
    }

    /**
     * Только черновики.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_DRAFT
        );
    }

    /**
     * Только архивные формы.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_ARCHIVED
        );
    }

    /**
     * Формы, доступные в публичной части приложения.
     */
    public function scopeForPublic(Builder $query): Builder
    {
        return $query
            ->active()
            ->published();
    }

    /**
     * Поиск формы по системному коду.
     */
    public function scopeByCode(
        Builder $query,
        string $code
    ): Builder {
        return $query->where(
            'code',
            $code
        );
    }

    /**
     * Формы конкретного владельца.
     */
    public function scopeForUser(
        Builder $query,
        int $userId
    ): Builder {
        return $query->where(
            'user_id',
            $userId
        );
    }

    /**
     * Поиск по системному коду и переводимым полям.
     */
    public function scopeSearch(
        Builder $query,
        ?string $search,
        ?string $locale = null
    ): Builder {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        $locale ??= app()->getLocale();

        return $query->where(function (Builder $query) use (
            $search,
            $locale
        ) {
            $query
                ->where(
                    'code',
                    'like',
                    "%{$search}%"
                )
                ->orWhereHas(
                    'translations',
                    function (Builder $translationQuery) use (
                        $search,
                        $locale
                    ) {
                        $translationQuery
                            ->where(
                                'locale',
                                $locale
                            )
                            ->where(
                                function (Builder $query) use (
                                    $search
                                ) {
                                    $query
                                        ->where(
                                            'title',
                                            'like',
                                            "%{$search}%"
                                        )
                                        ->orWhere(
                                            'subtitle',
                                            'like',
                                            "%{$search}%"
                                        )
                                        ->orWhere(
                                            'description',
                                            'like',
                                            "%{$search}%"
                                        );
                                }
                            );
                    }
                );
        });
    }

    /**
     * Сортировка форм.
     */
    public function scopeSortByParam(
        Builder $query,
        ?string $sortBy = null,
        string $direction = 'asc',
        ?string $locale = null
    ): Builder {
        $direction = strtolower($direction) === 'desc'
            ? 'desc'
            : 'asc';

        $locale ??= app()->getLocale();

        return match ($sortBy) {
            'id' => $query->orderBy(
                'forms.id',
                $direction
            ),

            'code' => $query->orderBy(
                'forms.code',
                $direction
            ),

            'status' => $query->orderBy(
                'forms.status',
                $direction
            ),

            'activity' => $query->orderBy(
                'forms.activity',
                $direction
            ),

            'sort' => $query->orderBy(
                'forms.sort',
                $direction
            ),

            'created_at' => $query->orderBy(
                'forms.created_at',
                $direction
            ),

            'updated_at' => $query->orderBy(
                'forms.updated_at',
                $direction
            ),

            'title' => $query
                ->leftJoin(
                    'form_translations as ft_sort',
                    function ($join) use ($locale) {
                        $join
                            ->on(
                                'ft_sort.form_id',
                                '=',
                                'forms.id'
                            )
                            ->where(
                                'ft_sort.locale',
                                '=',
                                $locale
                            );
                    }
                )
                ->select('forms.*')
                ->orderBy(
                    'ft_sort.title',
                    $direction
                ),

            default => $query
                ->orderBy('forms.sort')
                ->orderBy('forms.id'),
        };
    }
}
