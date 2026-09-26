<?php

namespace App\Models\Admin\Form\FormSubmission;

use App\Models\Admin\Form\Form\Form;
use App\Models\Admin\Form\FormSubmissionFile\FormSubmissionFile;
use App\Models\Admin\Form\FormSubmissionValue\FormSubmissionValue;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

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

    protected $casts = [
        'form_id' => 'integer',
        'user_id' => 'integer',
        'assigned_user_id' => 'integer',

        'context' => 'array',

        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'submitted_at' => 'datetime',
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
        return $this->belongsTo(Form::class);
    }

    /**
     * Авторизованный пользователь,
     * отправивший форму.
     *
     * Для гостевой заявки будет null.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Пользователь админки/CRM,
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
     */
    public function values(): HasMany
    {
        return $this->hasMany(
            FormSubmissionValue::class
        );
    }

    /**
     * Файлы, приложенные к заявке.
     */
    public function files(): HasMany
    {
        return $this->hasMany(
            FormSubmissionFile::class
        )
            ->orderBy('field_name')
            ->orderBy('sort')
            ->orderBy('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Status helpers
    |--------------------------------------------------------------------------
    */

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isSpam(): bool
    {
        return $this->status === self::STATUS_SPAM;
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
     * product.id
     * category.id
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
        if ($this->relationLoaded('values')) {
            /** @var FormSubmissionValue|null $value */
            $value = $this->values->firstWhere(
                'field_name',
                $fieldName
            );

            return $value;
        }

        /** @var FormSubmissionValue|null $value */
        $value = $this->values()
            ->where('field_name', $fieldName)
            ->first();

        return $value;
    }

    /**
     * Получить файлы поля по его системному имени.
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
        if ($this->relationLoaded('files')) {
            return $this->files
                ->where('field_name', $fieldName)
                ->values();
        }

        /** @var Collection<int, FormSubmissionFile> $files */
        $files = $this->files()
            ->where('field_name', $fieldName)
            ->get();

        return $files;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes: statuses
    |--------------------------------------------------------------------------
    */

    public function scopeNew(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_NEW
        );
    }

    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_PROCESSING
        );
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_COMPLETED
        );
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where(
            'status',
            self::STATUS_CANCELLED
        );
    }

    public function scopeSpam(Builder $query): Builder
    {
        return $query->where(
            'status',
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
            'form_id',
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
            fn (Builder $formQuery) => $formQuery
                ->where('code', $code)
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
            'source',
            $source
        );
    }

    /**
     * Заявки авторизованного пользователя.
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
     * Заявки, назначенные сотруднику.
     */
    public function scopeAssignedTo(
        Builder $query,
        int $userId
    ): Builder {
        return $query->where(
            'assigned_user_id',
            $userId
        );
    }

    /**
     * Неназначенные заявки.
     */
    public function scopeUnassigned(
        Builder $query
    ): Builder {
        return $query->whereNull(
            'assigned_user_id'
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
            'locale',
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
                fn (Builder $query) => $query
                    ->where(
                        'submitted_at',
                        '>=',
                        $from
                    )
            )
            ->when(
                $to,
                fn (Builder $query) => $query
                    ->where(
                        'submitted_at',
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
            'utm_source',
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
            'utm_campaign',
            $campaign
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes: search
    |--------------------------------------------------------------------------
    */

    /**
     * Поиск заявки.
     *
     * Ищем:
     * - ID заявки
     * - source
     * - page_url
     * - session_id
     * - UTM
     * - системные имена полей
     * - сохранённые значения полей
     * - отображаемые значения
     */
    public function scopeSearch(
        Builder $query,
        ?string $search
    ): Builder {
        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(
            function (Builder $query) use ($search) {
                if (ctype_digit($search)) {
                    $query->orWhere(
                        'form_submissions.id',
                        (int) $search
                    );
                }

                $query
                    ->orWhere(
                        'form_submissions.source',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'form_submissions.page_url',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'form_submissions.session_id',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'form_submissions.utm_source',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'form_submissions.utm_campaign',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'values',
                        function (Builder $valueQuery) use (
                            $search
                        ) {
                            $valueQuery
                                ->where(
                                    'field_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'value',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'display_value',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes: sorting
    |--------------------------------------------------------------------------
    */

    /**
     * Сортировка заявок.
     */
    public function scopeSortByParam(
        Builder $query,
        ?string $sortBy = null,
        string $direction = 'desc'
    ): Builder {
        $direction = strtolower($direction) === 'asc'
            ? 'asc'
            : 'desc';

        return match ($sortBy) {
            'id' => $query->orderBy(
                'form_submissions.id',
                $direction
            ),

            'status' => $query->orderBy(
                'form_submissions.status',
                $direction
            ),

            'source' => $query->orderBy(
                'form_submissions.source',
                $direction
            ),

            'processed_at' => $query->orderBy(
                'form_submissions.processed_at',
                $direction
            ),

            'completed_at' => $query->orderBy(
                'form_submissions.completed_at',
                $direction
            ),

            'created_at' => $query->orderBy(
                'form_submissions.created_at',
                $direction
            ),

            'submitted_at' => $query->orderBy(
                'form_submissions.submitted_at',
                $direction
            ),

            default => $query
                ->orderByDesc(
                    'form_submissions.submitted_at'
                )
                ->orderByDesc(
                    'form_submissions.id'
                ),
        };
    }
}
