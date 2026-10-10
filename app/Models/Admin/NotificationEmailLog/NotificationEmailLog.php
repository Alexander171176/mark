<?php

namespace App\Models\Admin\NotificationEmailLog;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class NotificationEmailLog extends Model
{
    use HasFactory;

    protected $table = 'notification_email_logs';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_RETRYING = 'retrying';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';
    public const STATUS_SKIPPED = 'skipped';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PROCESSING,
        self::STATUS_RETRYING,
        self::STATUS_SENT,
        self::STATUS_FAILED,
        self::STATUS_SKIPPED,
    ];

    public const SORTS = [
        'idAsc', 'idDesc',
        'createdAtAsc', 'createdAtDesc',
        'recipientAsc', 'recipientDesc',
        'statusAsc', 'statusDesc',
        'attemptsAsc', 'attemptsDesc',
    ];

    protected $fillable = [
        'uuid', 'event', 'recipient', 'subject', 'status', 'attempts',
        'queue', 'mailer', 'error_type', 'error_message',
        'queued_at', 'processing_at', 'sent_at', 'failed_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'queued_at' => 'datetime',
        'processing_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Структурные фильтры журнала
    |--------------------------------------------------------------------------
    */

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status !== null && $status !== ''
            ? $query->where('notification_email_logs.status', $status)
            : $query;
    }

    public function scopeEvent(Builder $query, ?string $event): Builder
    {
        return $event !== null && $event !== ''
            ? $query->where('notification_email_logs.event', $event)
            : $query;
    }

    /** Поиск подстроки в поле получателя. */
    public function scopeRecipient(Builder $query, ?string $recipient): Builder
    {
        if ($recipient === null || trim($recipient) === '') {
            return $query;
        }

        return $query->where(
            'notification_email_logs.recipient',
            'like',
            '%' . self::escapeLike(trim($recipient)) . '%'
        );
    }

    /** Поиск подстроки UUID. */
    public function scopeUuidContains(Builder $query, ?string $uuid): Builder
    {
        if ($uuid === null || trim($uuid) === '') {
            return $query;
        }

        return $query->where(
            'notification_email_logs.uuid',
            'like',
            '%' . self::escapeLike(trim($uuid)) . '%'
        );
    }

    /** Точное количество попыток, включая ноль. */
    public function scopeAttempts(Builder $query, int|string|null $attempts): Builder
    {
        if ($attempts === null || $attempts === '') {
            return $query;
        }

        return $query->where('notification_email_logs.attempts', (int) $attempts);
    }

    /** Начало периода включительно. */
    public function scopeDateFrom(Builder $query, ?string $date): Builder
    {
        if (!$date) {
            return $query;
        }

        return $query->where(
            'notification_email_logs.created_at',
            '>=',
            Carbon::createFromFormat('!Y-m-d', $date)->startOfDay()
        );
    }

    /** Конец периода включительно, без SQL DATE(created_at). */
    public function scopeDateTo(Builder $query, ?string $date): Builder
    {
        if (!$date) {
            return $query;
        }

        return $query->where(
            'notification_email_logs.created_at',
            '<',
            Carbon::createFromFormat('!Y-m-d', $date)->addDay()->startOfDay()
        );
    }

    /** Применение всех структурных фильтров из административной панели. */
    public function scopeIndexFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->status($filters['status'] ?? null)
            ->event($filters['event'] ?? null)
            ->recipient($filters['recipient'] ?? null)
            ->uuidContains($filters['uuid'] ?? null)
            ->attempts($filters['attempts'] ?? null)
            ->dateFrom($filters['date_from'] ?? null)
            ->dateTo($filters['date_to'] ?? null);
    }

    /*
    |--------------------------------------------------------------------------
    | Поиск и сортировка
    |--------------------------------------------------------------------------
    */

    /** Общий поиск по получателю, событию, теме письма и UUID. */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $pattern = '%' . self::escapeLike($term) . '%';

        return $query->where(function (Builder $q) use ($pattern) {
            $q->where('notification_email_logs.recipient', 'like', $pattern)
                ->orWhere('notification_email_logs.event', 'like', $pattern)
                ->orWhere('notification_email_logs.subject', 'like', $pattern)
                ->orWhere('notification_email_logs.uuid', 'like', $pattern);
        });
    }

    public function scopeSortByParam(Builder $query, ?string $sort): Builder
    {
        $column = match ($sort) {
            'createdAtAsc', 'createdAtDesc' => 'created_at',
            'recipientAsc', 'recipientDesc' => 'recipient',
            'statusAsc', 'statusDesc' => 'status',
            'attemptsAsc', 'attemptsDesc' => 'attempts',
            default => 'id',
        };

        $direction = str_ends_with((string) $sort, 'Asc') ? 'asc' : 'desc';

        $query->orderBy('notification_email_logs.' . $column, $direction);

        // Стабильный порядок при одинаковых значениях.
        if ($column !== 'id') {
            $query->orderByDesc('notification_email_logs.id');
        }

        return $query;
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->status(self::STATUS_FAILED);
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->status(self::STATUS_SENT);
    }

    /** Экранирование специальных символов шаблона SQL LIKE. */
    private static function escapeLike(string $value): string
    {
        return addcslashes($value, '%_\\');
    }
}
