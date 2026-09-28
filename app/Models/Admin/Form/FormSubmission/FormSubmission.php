<?php

namespace App\Models\Admin\Form\FormSubmission;

use App\Models\Admin\Form\Form\Form;
use App\Models\Admin\Form\FormSubmissionFile\FormSubmissionFile;
use App\Models\Admin\Form\FormSubmissionValue\FormSubmissionValue;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSubmission extends Model
{
    use HasFactory;

    protected $table = 'form_submissions';

    /*
    |--------------------------------------------------------------------------
    | Statuses
    |--------------------------------------------------------------------------
    */

    public const STATUS_NEW = 'new';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_SPAM = 'spam';

    /*
    |--------------------------------------------------------------------------
    | Mass assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'form_id',
        'user_id',

        'status',

        'source',
        'page_url',
        'locale',
        'context',

        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',

        'ip',
        'user_agent',
        'session_id',

        'assigned_user_id',
        'processed_at',
        'completed_at',

        'submitted_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'form_id' => 'integer',
        'user_id' => 'integer',
        'assigned_user_id' => 'integer',

        'context' => 'array',

        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'submitted_at' => 'datetime',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Форма, через которую была отправлена заявка.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(
            Form::class,
            'form_id'
        );
    }

    /**
     * Авторизованный пользователь,
     * отправивший форму.
     *
     * Для гостевой заявки будет null.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Пользователь админки / CRM,
     * которому назначена заявка.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_user_id'
        );
    }

    /**
     * Значения полей заявки.
     *
     * Значения являются snapshot-данными
     * и сохраняют содержимое формы
     * на момент отправки.
     */
    public function values(): HasMany
    {
        return $this->hasMany(
            FormSubmissionValue::class,
            'form_submission_id'
        );
    }

    /**
     * Файлы, приложенные к заявке.
     */
    public function files(): HasMany
    {
        return $this->hasMany(
            FormSubmissionFile::class,
            'form_submission_id'
        )
            ->orderBy(
                'field_name',
                'asc'
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

    /*
    |--------------------------------------------------------------------------
    | Status helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Новая заявка.
     */
    public function isNew(): bool
    {
        return $this->status
            === self::STATUS_NEW;
    }

    /**
     * Заявка находится в обработке.
     */
    public function isProcessing(): bool
    {
        return $this->status
            === self::STATUS_PROCESSING;
    }

    /**
     * Заявка завершена.
     */
    public function isCompleted(): bool
    {
        return $this->status
            === self::STATUS_COMPLETED;
    }

    /**
     * Заявка отменена.
     */
    public function isCancelled(): bool
    {
        return $this->status
            === self::STATUS_CANCELLED;
    }

    /**
     * Заявка отмечена как spam.
     */
    public function isSpam(): bool
    {
        return $this->status
            === self::STATUS_SPAM;
    }

    /**
     * Назначена ли заявка сотруднику.
     */
    public function isAssigned(): bool
    {
        return $this->assigned_user_id !== null;
    }

    /**
     * Является ли заявка гостевой.
     */
    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    /*
    |--------------------------------------------------------------------------
    | Context helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить значение из контекста заявки.
     *
     * Поддерживает dot notation:
     *
     * product.id
     * category.id
     * order.id
     */
    public function getContext(
        string $key,
        mixed $default = null
    ): mixed {
        return data_get(
            $this->context ?? [],
            $key,
            $default
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Value helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Получить сохранённое значение поля
     * по его системному имени.
     *
     * Метод использует snapshot field_name,
     * поэтому продолжит работать даже после
     * удаления исходного FormField.
     */
    public function valueByFieldName(
        string $fieldName
    ): ?FormSubmissionValue {
        if (
            $this->relationLoaded(
                'values'
            )
        ) {
            /** @var FormSubmissionValue|null $value */
            $value = $this->values
                ->firstWhere(
                    'field_name',
                    $fieldName
                );

            return $value;
        }

        /** @var FormSubmissionValue|null $value */
        $value = $this->values()
            ->where(
                'field_name',
                $fieldName
            )
            ->first();

        return $value;
    }

    /**
     * Получить файлы поля
     * по его системному имени.
     *
     * Метод использует snapshot field_name,
     * поэтому продолжит работать даже после
     * удаления исходного FormField.
     *
     * @return Collection<int, FormSubmissionFile>
     */
    public function filesByFieldName(
        string $fieldName
    ): Collection {
        if (
            $this->relationLoaded(
                'files'
            )
        ) {
            return $this->files
                ->where(
                    'field_name',
                    $fieldName
                )
                ->values();
        }

        /** @var Collection<int, FormSubmissionFile> $files */
        $files = $this->files()
            ->where(
                'field_name',
                $fieldName
            )
            ->get();

        return $files;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes: statuses
    |--------------------------------------------------------------------------
    */

    /**
     * Только новые заявки.
     */
    public function scopeNew(
        Builder $query
    ): Builder {
        return $query->where(
            'form_submissions.status',
            self::STATUS_NEW
        );
    }

    /**
     * Только заявки в обработке.
     */
    public function scopeProcessing(
        Builder $query
    ): Builder {
        return $query->where(
            'form_submissions.status',
            self::STATUS_PROCESSING
        );
    }

    /**
     * Только завершённые заявки.
     */
    public function scopeCompleted(
        Builder $query
    ): Builder {
        return $query->where(
            'form_submissions.status',
            self::STATUS_COMPLETED
        );
    }

    /**
     * Только отменённые заявки.
     */
    public function scopeCancelled(
        Builder $query
    ): Builder {
        return $query->where(
            'form_submissions.status',
            self::STATUS_CANCELLED
        );
    }

    /**
     * Только spam-заявки.
     */
    public function scopeSpam(
        Builder $query
    ): Builder {
        return $query->where(
            'form_submissions.status',
            self::STATUS_SPAM
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes: filters
    |--------------------------------------------------------------------------
    */

    /**
     * Заявки конкретной формы.
     */
    public function scopeForForm(
        Builder $query,
        int $formId
    ): Builder {
        return $query->where(
            'form_submissions.form_id',
            $formId
        );
    }

    /**
     * Заявки формы по её системному коду.
     */
    public function scopeForFormCode(
        Builder $query,
        string $code
    ): Builder {
        return $query->whereHas(
            'form',
            fn (Builder $formQuery) =>
            $formQuery->where(
                'forms.code',
                $code
            )
        );
    }

    /**
     * Заявки из конкретного источника.
     */
    public function scopeFromSource(
        Builder $query,
        string $source
    ): Builder {
        return $query->where(
            'form_submissions.source',
            $source
        );
    }

    /**
     * Заявки авторизованного отправителя.
     */
    public function scopeForUser(
        Builder $query,
        int $userId
    ): Builder {
        return $query->where(
            'form_submissions.user_id',
            $userId
        );
    }

    /**
     * Заявки, назначенные сотруднику.
     */
    public function scopeAssignedTo(
        Builder $query,
        int $userId
    ): Builder {
        return $query->where(
            'form_submissions.assigned_user_id',
            $userId
        );
    }

    /**
     * Все назначенные заявки.
     */
    public function scopeAssigned(
        Builder $query
    ): Builder {
        return $query->whereNotNull(
            'form_submissions.assigned_user_id'
        );
    }

    /**
     * Неназначенные заявки.
     */
    public function scopeUnassigned(
        Builder $query
    ): Builder {
        return $query->whereNull(
            'form_submissions.assigned_user_id'
        );
    }

    /**
     * Заявки указанной локали.
     */
    public function scopeLocale(
        Builder $query,
        string $locale
    ): Builder {
        return $query->where(
            'form_submissions.locale',
            $locale
        );
    }

    /**
     * Заявки за период отправки.
     */
    public function scopeSubmittedBetween(
        Builder $query,
        mixed $from = null,
        mixed $to = null
    ): Builder {
        return $query
            ->when(
                $from,
                fn (Builder $builder) =>
                $builder->where(
                    'form_submissions.submitted_at',
                    '>=',
                    $from
                )
            )
            ->when(
                $to,
                fn (Builder $builder) =>
                $builder->where(
                    'form_submissions.submitted_at',
                    '<=',
                    $to
                )
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes: marketing
    |--------------------------------------------------------------------------
    */

    /**
     * Заявки из UTM-источника.
     */
    public function scopeUtmSource(
        Builder $query,
        string $source
    ): Builder {
        return $query->where(
            'form_submissions.utm_source',
            $source
        );
    }

    /**
     * Заявки определённой UTM-кампании.
     */
    public function scopeUtmCampaign(
        Builder $query,
        string $campaign
    ): Builder {
        return $query->where(
            'form_submissions.utm_campaign',
            $campaign
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes: ordering
    |--------------------------------------------------------------------------
    */

    /**
     * Сортировка заявок по умолчанию.
     *
     * Сначала самые свежие отправленные заявки.
     */
    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderByDesc(
                'form_submissions.submitted_at'
            )
            ->orderByDesc(
                'form_submissions.id'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes: sorting
    |--------------------------------------------------------------------------
    */

    /**
     * Сортировка и простая фильтрация
     * по единому параметру Admin Index.
     *
     * Контракт:
     *
     * idAsc / idDesc
     *
     * submittedAtAsc / submittedAtDesc
     * createdAtAsc / createdAtDesc
     * dateAsc / dateDesc
     * updatedAtAsc / updatedAtDesc
     *
     * statusAsc / statusDesc
     * statusNew
     * statusProcessing
     * statusCompleted
     * statusCancelled
     * statusSpam
     *
     * sourceAsc / sourceDesc
     * localeAsc / localeDesc
     *
     * formIdAsc / formIdDesc
     * formTitleAsc / formTitleDesc
     *
     * userNameAsc / userNameDesc
     * assignedUserNameAsc / assignedUserNameDesc
     *
     * assigned / unassigned
     *
     * valuesAsc / valuesDesc
     * filesAsc / filesDesc
     *
     * processedAtAsc / processedAtDesc
     * completedAtAsc / completedAtDesc
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

            'idAsc' => $query
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'idDesc' => $query
                ->orderBy(
                    'form_submissions.id',
                    'desc'
                ),

            /*
            |--------------------------------------------------------------------------
            | Время отправки
            |--------------------------------------------------------------------------
            */

            'submittedAtAsc' => $query
                ->orderBy(
                    'form_submissions.submitted_at',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'submittedAtDesc' => $query
                ->orderBy(
                    'form_submissions.submitted_at',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Статус
            |--------------------------------------------------------------------------
            */

            'statusAsc' => $query
                ->orderBy(
                    'form_submissions.status',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'statusDesc' => $query
                ->orderBy(
                    'form_submissions.status',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            'statusNew' => $query
                ->new()
                ->ordered(),

            'statusProcessing' => $query
                ->processing()
                ->ordered(),

            'statusCompleted' => $query
                ->completed()
                ->ordered(),

            'statusCancelled' => $query
                ->cancelled()
                ->ordered(),

            'statusSpam' => $query
                ->spam()
                ->ordered(),

            /*
            |--------------------------------------------------------------------------
            | Источник
            |--------------------------------------------------------------------------
            */

            'sourceAsc' => $query
                ->orderBy(
                    'form_submissions.source',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'sourceDesc' => $query
                ->orderBy(
                    'form_submissions.source',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Локаль заявки
            |--------------------------------------------------------------------------
            */

            'localeAsc' => $query
                ->orderBy(
                    'form_submissions.locale',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'localeDesc' => $query
                ->orderBy(
                    'form_submissions.locale',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Родительская форма: ID
            |--------------------------------------------------------------------------
            */

            'formIdAsc' => $query
                ->orderBy(
                    'form_submissions.form_id',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'formIdDesc' => $query
                ->orderBy(
                    'form_submissions.form_id',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Родительская форма: название
            |--------------------------------------------------------------------------
            */

            'formTitleAsc' => $query
                ->leftJoin(
                    'forms as sort_forms',
                    'form_submissions.form_id',
                    '=',
                    'sort_forms.id'
                )
                ->leftJoin(
                    'form_translations as sort_form_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'sort_forms.id',
                            '=',
                            'sort_form_translations.form_id'
                        )->where(
                            'sort_form_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'form_submissions.*'
                )
                ->orderBy(
                    'sort_form_translations.title',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'formTitleDesc' => $query
                ->leftJoin(
                    'forms as sort_forms',
                    'form_submissions.form_id',
                    '=',
                    'sort_forms.id'
                )
                ->leftJoin(
                    'form_translations as sort_form_translations',
                    function ($join) use ($locale) {
                        $join->on(
                            'sort_forms.id',
                            '=',
                            'sort_form_translations.form_id'
                        )->where(
                            'sort_form_translations.locale',
                            '=',
                            $locale
                        );
                    }
                )
                ->addSelect(
                    'form_submissions.*'
                )
                ->orderBy(
                    'sort_form_translations.title',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Отправитель
            |--------------------------------------------------------------------------
            */

            'userNameAsc' => $query
                ->leftJoin(
                    'users as sort_users',
                    'form_submissions.user_id',
                    '=',
                    'sort_users.id'
                )
                ->addSelect(
                    'form_submissions.*'
                )
                ->orderBy(
                    'sort_users.name',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'userNameDesc' => $query
                ->leftJoin(
                    'users as sort_users',
                    'form_submissions.user_id',
                    '=',
                    'sort_users.id'
                )
                ->addSelect(
                    'form_submissions.*'
                )
                ->orderBy(
                    'sort_users.name',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Ответственный сотрудник
            |--------------------------------------------------------------------------
            */

            'assignedUserNameAsc' => $query
                ->leftJoin(
                    'users as sort_assigned_users',
                    'form_submissions.assigned_user_id',
                    '=',
                    'sort_assigned_users.id'
                )
                ->addSelect(
                    'form_submissions.*'
                )
                ->orderBy(
                    'sort_assigned_users.name',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'assignedUserNameDesc' => $query
                ->leftJoin(
                    'users as sort_assigned_users',
                    'form_submissions.assigned_user_id',
                    '=',
                    'sort_assigned_users.id'
                )
                ->addSelect(
                    'form_submissions.*'
                )
                ->orderBy(
                    'sort_assigned_users.name',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Назначение
            |--------------------------------------------------------------------------
            */

            'assigned' => $query
                ->assigned()
                ->ordered(),

            'unassigned' => $query
                ->unassigned()
                ->ordered(),

            /*
            |--------------------------------------------------------------------------
            | Количество значений
            |--------------------------------------------------------------------------
            |
            | indexQuery() уже загружает values_count.
            |
            */

            'valuesAsc' => $query
                ->orderBy(
                    'values_count',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'valuesDesc' => $query
                ->orderBy(
                    'values_count',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Количество файлов
            |--------------------------------------------------------------------------
            |
            | indexQuery() уже загружает files_count.
            |
            */

            'filesAsc' => $query
                ->orderBy(
                    'files_count',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'filesDesc' => $query
                ->orderBy(
                    'files_count',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Начало обработки
            |--------------------------------------------------------------------------
            */

            'processedAtAsc' => $query
                ->orderBy(
                    'form_submissions.processed_at',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'processedAtDesc' => $query
                ->orderBy(
                    'form_submissions.processed_at',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Завершение
            |--------------------------------------------------------------------------
            */

            'completedAtAsc' => $query
                ->orderBy(
                    'form_submissions.completed_at',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'completedAtDesc' => $query
                ->orderBy(
                    'form_submissions.completed_at',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Дата создания
            |--------------------------------------------------------------------------
            */

            'createdAtAsc',
            'dateAsc' => $query
                ->orderBy(
                    'form_submissions.created_at',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'createdAtDesc',
            'dateDesc' => $query
                ->orderBy(
                    'form_submissions.created_at',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Дата обновления
            |--------------------------------------------------------------------------
            */

            'updatedAtAsc' => $query
                ->orderBy(
                    'form_submissions.updated_at',
                    'asc'
                )
                ->orderBy(
                    'form_submissions.id',
                    'asc'
                ),

            'updatedAtDesc' => $query
                ->orderBy(
                    'form_submissions.updated_at',
                    'desc'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),

            /*
            |--------------------------------------------------------------------------
            | Сортировка по умолчанию
            |--------------------------------------------------------------------------
            */

            default => $query->ordered(),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes: search
    |--------------------------------------------------------------------------
    */

    /**
     * Поиск для Admin Index.
     *
     * Поиск выполняется:
     *
     * - по ID заявки;
     * - по source;
     * - по page_url;
     * - по session_id;
     * - по IP;
     * - по UTM;
     * - по коду формы;
     * - по названию формы текущей локали;
     * - по имени / email отправителя;
     * - по имени / email ответственного;
     * - по snapshot field_name;
     * - по snapshot value;
     * - по snapshot display_value.
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
                /*
                |--------------------------------------------------------------------------
                | ID
                |--------------------------------------------------------------------------
                */

                if (ctype_digit($term)) {
                    $q->orWhere(
                        'form_submissions.id',
                        (int) $term
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Собственные поля заявки
                |--------------------------------------------------------------------------
                */

                $q->orWhere(
                    'form_submissions.source',
                    'like',
                    "%{$term}%"
                )
                    ->orWhere(
                        'form_submissions.page_url',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'form_submissions.session_id',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'form_submissions.ip',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'form_submissions.locale',
                        'like',
                        "%{$term}%"
                    )

                    /*
                    |--------------------------------------------------------------------------
                    | UTM
                    |--------------------------------------------------------------------------
                    */

                    ->orWhere(
                        'form_submissions.utm_source',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'form_submissions.utm_medium',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'form_submissions.utm_campaign',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'form_submissions.utm_content',
                        'like',
                        "%{$term}%"
                    )
                    ->orWhere(
                        'form_submissions.utm_term',
                        'like',
                        "%{$term}%"
                    )

                    /*
                    |--------------------------------------------------------------------------
                    | Родительская форма
                    |--------------------------------------------------------------------------
                    */

                    ->orWhereHas(
                        'form',
                        function (
                            Builder $formQuery
                        ) use (
                            $term,
                            $locale
                        ) {
                            $formQuery
                                ->where(
                                    'forms.code',
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
                                                        );
                                                }
                                            );
                                    }
                                );
                        }
                    )

                    /*
                    |--------------------------------------------------------------------------
                    | Авторизованный отправитель
                    |--------------------------------------------------------------------------
                    */

                    ->orWhereHas(
                        'user',
                        function (
                            Builder $userQuery
                        ) use ($term) {
                            $userQuery->where(
                                function (
                                    Builder $searchQuery
                                ) use ($term) {
                                    $searchQuery
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
                    )

                    /*
                    |--------------------------------------------------------------------------
                    | Ответственный сотрудник
                    |--------------------------------------------------------------------------
                    */

                    ->orWhereHas(
                        'assignedUser',
                        function (
                            Builder $userQuery
                        ) use ($term) {
                            $userQuery->where(
                                function (
                                    Builder $searchQuery
                                ) use ($term) {
                                    $searchQuery
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
                    )

                    /*
                    |--------------------------------------------------------------------------
                    | Snapshot-значения полей
                    |--------------------------------------------------------------------------
                    */

                    ->orWhereHas(
                        'values',
                        function (
                            Builder $valueQuery
                        ) use ($term) {
                            $valueQuery
                                ->where(
                                    'field_name',
                                    'like',
                                    "%{$term}%"
                                )
                                ->orWhere(
                                    'field_label',
                                    'like',
                                    "%{$term}%"
                                )
                                ->orWhere(
                                    'value',
                                    'like',
                                    "%{$term}%"
                                )
                                ->orWhere(
                                    'display_value',
                                    'like',
                                    "%{$term}%"
                                );
                        }
                    );
            }
        );
    }
}
