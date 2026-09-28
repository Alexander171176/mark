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

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Все переводы формы.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(
            FormTranslation::class,
            'form_id'
        );
    }

    /**
     * Перевод для текущей локали приложения.
     *
     * Relation оставляем для публичных
     * и внешних сценариев.
     *
     * Admin Index использует translations
     * с заранее ограниченным набором локалей.
     */
    public function translation(): HasOne
    {
        return $this->hasOne(
            FormTranslation::class,
            'form_id'
        )->where(
            'locale',
            app()->getLocale()
        );
    }

    /**
     * Все поля формы.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(
            FormField::class,
            'form_id'
        )
            ->orderBy(
                'sort',
                'asc'
            )
            ->orderBy(
                'id',
                'asc'
            );
    }

    /**
     * Только активные поля формы.
     */
    public function activeFields(): HasMany
    {
        return $this->hasMany(
            FormField::class,
            'form_id'
        )
            ->where(
                'activity',
                true
            )
            ->orderBy(
                'sort',
                'asc'
            )
            ->orderBy(
                'id',
                'asc'
            );
    }

    /**
     * Заявки, отправленные через форму.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(
            FormSubmission::class,
            'form_id'
        );
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

        $translations = $this->relationLoaded(
            'translations'
        )
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
        if (
            !$this->relationLoaded(
                'translations'
            )
        ) {
            return null;
        }

        $locale ??= app()->getLocale();

        $fallbackLocale ??= config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $this->translations
            ->firstWhere(
                'locale',
                $locale
            );

        if ($translation) {
            return $translation;
        }

        if ($fallbackLocale !== $locale) {
            $fallback = $this->translations
                ->firstWhere(
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
        return $this->status ===
            self::STATUS_DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status ===
            self::STATUS_PUBLISHED;
    }

    public function isArchived(): bool
    {
        return $this->status ===
            self::STATUS_ARCHIVED;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Только активные формы.
     */
    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'forms.activity',
            true
        );
    }

    /**
     * Только опубликованные формы.
     */
    public function scopePublished(
        Builder $query
    ): Builder {
        return $query->where(
            'forms.status',
            self::STATUS_PUBLISHED
        );
    }

    /**
     * Только черновики.
     */
    public function scopeDraft(
        Builder $query
    ): Builder {
        return $query->where(
            'forms.status',
            self::STATUS_DRAFT
        );
    }

    /**
     * Только архивные формы.
     */
    public function scopeArchived(
        Builder $query
    ): Builder {
        return $query->where(
            'forms.status',
            self::STATUS_ARCHIVED
        );
    }

    /**
     * Формы, доступные
     * в публичной части приложения.
     */
    public function scopeForPublic(
        Builder $query
    ): Builder {
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
            'forms.code',
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
            'forms.user_id',
            $userId
        );
    }

    /**
     * Сортировка по умолчанию.
     */
    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderBy(
                'forms.sort',
                'asc'
            )
            ->orderByDesc(
                'forms.id'
            );
    }

    /**
     * Сортировка и фильтрация
     * по параметру списка.
     *
     * Контракт совпадает с остальными
     * административными сущностями:
     *
     * idAsc / idDesc
     * sortAsc / sortDesc
     * titleAsc / titleDesc
     * codeAsc / codeDesc
     * activityAsc / activityDesc
     * activity / inactive
     * statusAsc / statusDesc
     * statusDraft / statusPublished / statusArchived
     * fieldsAsc / fieldsDesc
     * submissionsAsc / submissionsDesc
     * ownerNameAsc / ownerNameDesc
     * ownerEmailAsc / ownerEmailDesc
     * createdAtAsc / createdAtDesc
     * dateAsc / dateDesc
     * updatedAtAsc / updatedAtDesc
     */
    public function scopeSortByParam(
        Builder $query,
        ?string $sort,
        ?string $locale = null
    ): Builder {
        $locale = $locale
            ?: app()->getLocale();

        return match ($sort) {
            /*
            |--------------------------------------------------------------------------
            | ID
            |--------------------------------------------------------------------------
            */

            'idAsc' => $query->orderBy(
                'forms.id',
                'asc'
            ),

            'idDesc' => $query->orderBy(
                'forms.id',
                'desc'
            ),

            /*
            |--------------------------------------------------------------------------
            | Sort
            |--------------------------------------------------------------------------
            */

            'sortAsc' => $query
                ->orderBy(
                    'forms.sort',
                    'asc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'sortDesc' => $query
                ->orderBy(
                    'forms.sort',
                    'desc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Название
            |--------------------------------------------------------------------------
            */

            'titleAsc' => $query
                ->leftJoin(
                    'form_translations as sort_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'forms.id',
                            '=',
                            'sort_translations.form_id'
                        )->where(
                            'sort_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'forms.*'
                )
                ->orderBy(
                    'sort_translations.title',
                    'asc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'titleDesc' => $query
                ->leftJoin(
                    'form_translations as sort_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'forms.id',
                            '=',
                            'sort_translations.form_id'
                        )->where(
                            'sort_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'forms.*'
                )
                ->orderBy(
                    'sort_translations.title',
                    'desc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Системный код
            |--------------------------------------------------------------------------
            */

            'codeAsc' => $query
                ->orderBy(
                    'forms.code',
                    'asc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'codeDesc' => $query
                ->orderBy(
                    'forms.code',
                    'desc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Активность
            |--------------------------------------------------------------------------
            */

            'activityAsc' => $query
                ->orderBy(
                    'forms.activity',
                    'asc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'activityDesc' => $query
                ->orderBy(
                    'forms.activity',
                    'desc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'activity' => $query
                ->where(
                    'forms.activity',
                    true
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'inactive' => $query
                ->where(
                    'forms.activity',
                    false
                )
                ->orderByDesc(
                    'forms.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Статус
            |--------------------------------------------------------------------------
            */

            'statusAsc' => $query
                ->orderBy(
                    'forms.status',
                    'asc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'statusDesc' => $query
                ->orderBy(
                    'forms.status',
                    'desc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'statusDraft' => $query
                ->where(
                    'forms.status',
                    self::STATUS_DRAFT
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'statusPublished' => $query
                ->where(
                    'forms.status',
                    self::STATUS_PUBLISHED
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'statusArchived' => $query
                ->where(
                    'forms.status',
                    self::STATUS_ARCHIVED
                )
                ->orderByDesc(
                    'forms.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Количество полей
            |--------------------------------------------------------------------------
            */

            'fieldsAsc' => $query
                ->withCount('fields')
                ->orderBy(
                    'fields_count',
                    'asc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'fieldsDesc' => $query
                ->withCount('fields')
                ->orderBy(
                    'fields_count',
                    'desc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Количество заявок
            |--------------------------------------------------------------------------
            */

            'submissionsAsc' => $query
                ->withCount('submissions')
                ->orderBy(
                    'submissions_count',
                    'asc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'submissionsDesc' => $query
                ->withCount('submissions')
                ->orderBy(
                    'submissions_count',
                    'desc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Владелец
            |--------------------------------------------------------------------------
            */

            'ownerNameAsc' => $query
                ->leftJoin(
                    'users as sort_users',
                    'forms.user_id',
                    '=',
                    'sort_users.id'
                )
                ->addSelect(
                    'forms.*'
                )
                ->orderBy(
                    'sort_users.name',
                    'asc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'ownerNameDesc' => $query
                ->leftJoin(
                    'users as sort_users',
                    'forms.user_id',
                    '=',
                    'sort_users.id'
                )
                ->addSelect(
                    'forms.*'
                )
                ->orderBy(
                    'sort_users.name',
                    'desc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'ownerEmailAsc' => $query
                ->leftJoin(
                    'users as sort_users',
                    'forms.user_id',
                    '=',
                    'sort_users.id'
                )
                ->addSelect(
                    'forms.*'
                )
                ->orderBy(
                    'sort_users.email',
                    'asc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'ownerEmailDesc' => $query
                ->leftJoin(
                    'users as sort_users',
                    'forms.user_id',
                    '=',
                    'sort_users.id'
                )
                ->addSelect(
                    'forms.*'
                )
                ->orderBy(
                    'sort_users.email',
                    'desc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Дата создания
            |--------------------------------------------------------------------------
            */

            'createdAtAsc',
            'dateAsc' => $query
                ->orderBy(
                    'forms.created_at',
                    'asc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'createdAtDesc',
            'dateDesc' => $query
                ->orderBy(
                    'forms.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Дата обновления
            |--------------------------------------------------------------------------
            */

            'updatedAtAsc' => $query
                ->orderBy(
                    'forms.updated_at',
                    'asc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            'updatedAtDesc' => $query
                ->orderBy(
                    'forms.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'forms.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Сортировка по умолчанию
            |--------------------------------------------------------------------------
            */

            default => $query->ordered(),
        };
    }

    /**
     * Поиск для Admin Index.
     *
     * Семантика должна совпадать
     * с локальным поиском Index.vue.
     */
    public function scopeSearch(
        Builder $query,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim(
            (string) $term
        );

        if ($term === '') {
            return $query;
        }

        $locale = $locale
            ?: app()->getLocale();

        return $query->where(
            function (Builder $q) use (
                $term,
                $locale
            ) {
                $q->where(
                    'forms.code',
                    'like',
                    "%{$term}%"
                )
                    ->orWhere(
                        'forms.status',
                        'like',
                        "%{$term}%"
                    )
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
                                        Builder $searchQuery
                                    ) use ($term) {
                                        $searchQuery
                                            ->where(
                                                'title',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'subtitle',
                                                'like',
                                                "%{$term}%"
                                            )
                                            ->orWhere(
                                                'description',
                                                'like',
                                                "%{$term}%"
                                            );
                                    }
                                );
                        }
                    )
                    ->orWhereHas(
                        'user',
                        function (
                            Builder $userQuery
                        ) use ($term) {
                            $userQuery
                                ->where(
                                    'name',
                                    'like',
                                    "%{$term}%"
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    "%{$term}%"
                                );
                        }
                    );
            }
        );
    }
}
