<?php

namespace App\Models\Admin\Form\FormSubmissionStatusHistory;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmissionStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'form_submission_status_histories';

    /*
    |--------------------------------------------------------------------------
    | Mass assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'form_submission_id',
        'user_id',

        'from_status',
        'to_status',

        'source',
        'comment',
        'metadata',

        'changed_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'form_submission_id' => 'integer',
        'user_id' => 'integer',

        'metadata' => 'array',

        'changed_at' => 'datetime',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /**
     * Заявка, которой принадлежит запись истории.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(
            FormSubmission::class,
            'form_submission_id'
        );
    }

    /**
     * Пользователь, выполнивший изменение статуса.
     *
     * Для системных изменений
     * связь может вернуть null.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Является ли запись первоначальным
     * созданием заявки.
     */
    public function isInitial(): bool
    {
        return $this->from_status === null;
    }

    /**
     * Выполнено ли изменение пользователем.
     */
    public function isUserAction(): bool
    {
        return $this->user_id !== null;
    }

    /**
     * Получить отдельное значение metadata.
     *
     * Поддерживается dot notation.
     */
    public function getMetadata(
        string $key,
        mixed $default = null
    ): mixed {
        return data_get(
            $this->metadata ?? [],
            $key,
            $default
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * История конкретной заявки.
     */
    public function scopeForSubmission(
        Builder $query,
        int $submissionId
    ): Builder {
        return $query->where(
            'form_submission_status_histories.form_submission_id',
            $submissionId
        );
    }

    /**
     * Переходы в определённый статус.
     */
    public function scopeToStatus(
        Builder $query,
        string $status
    ): Builder {
        return $query->where(
            'form_submission_status_histories.to_status',
            $status
        );
    }

    /**
     * Изменения из определённого статуса.
     */
    public function scopeFromStatus(
        Builder $query,
        string $status
    ): Builder {
        return $query->where(
            'form_submission_status_histories.from_status',
            $status
        );
    }

    /**
     * Изменения из определённого источника.
     */
    public function scopeFromSource(
        Builder $query,
        string $source
    ): Builder {
        return $query->where(
            'form_submission_status_histories.source',
            $source
        );
    }

    /**
     * Основной порядок истории:
     * от старых событий к новым.
     */
    public function scopeOrdered(
        Builder $query
    ): Builder {
        return $query
            ->orderBy(
                'form_submission_status_histories.changed_at',
                'asc'
            )
            ->orderBy(
                'form_submission_status_histories.id',
                'asc'
            );
    }
}
