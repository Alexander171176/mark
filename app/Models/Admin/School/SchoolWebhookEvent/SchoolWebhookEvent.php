<?php

namespace App\Models\Admin\School\SchoolWebhookEvent;

use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use App\Models\Admin\School\SchoolPayment\SchoolPayment;
use App\Models\Admin\School\SchoolSubscription\SchoolSubscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolWebhookEvent extends Model
{
    use HasFactory;

    protected $table = 'school_webhook_events';

    protected $fillable = [
        'provider',
        'event_type',
        'external_id',
        'idempotency_key',
        'signature',
        'school_order_id',
        'school_payment_id',
        'school_subscription_id',
        'payload',
        'headers',
        'status',
        'attempts',
        'error_message',
        'delivered_at',
        'processed_at',
    ];

    protected $casts = [
        'school_order_id' => 'integer',
        'school_payment_id' => 'integer',
        'school_subscription_id' => 'integer',
        'payload' => 'array',
        'headers' => 'array',
        'attempts' => 'integer',
        'delivered_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /** Заказ */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SchoolOrder::class, 'school_order_id');
    }

    /** Платёж */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(SchoolPayment::class, 'school_payment_id');
    }

    /** Подписка */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(SchoolSubscription::class, 'school_subscription_id');
    }

    /** По провайдеру */
    public function scopeProvider(Builder $q, string $provider): Builder
    {
        return $q->where('provider', $provider);
    }

    /** Ожидающие обработки */
    public function scopePending(Builder $q): Builder
    {
        return $q->whereIn('status', ['received', 'processing']);
    }

    /** Ошибочные */
    public function scopeFailed(Builder $q): Builder
    {
        return $q->where('status', 'failed');
    }

    /** Обработанные */
    public function scopeProcessed(Builder $q): Builder
    {
        return $q->where('status', 'processed');
    }

    /** Пометить обработанным */
    public function markProcessed(): void
    {
        $this->status = 'processed';
        $this->processed_at = now();
        $this->error_message = null;
        $this->save();
    }

    /** Пометить ошибочным */
    public function markFailed(string $message): void
    {
        $this->status = 'failed';
        $this->error_message = $message;
        $this->attempts = ($this->attempts ?? 0) + 1;
        $this->save();
    }
}
