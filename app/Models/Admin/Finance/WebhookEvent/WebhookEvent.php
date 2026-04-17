<?php

namespace App\Models\Admin\Finance\WebhookEvent;

use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\Finance\Payment\Payment;
use App\Models\Admin\Finance\Subscription\Subscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebhookEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'webhook_events';

    protected $fillable = [
        'provider',          // источник (Stripe/PayPal/…)
        'event_type',        // тип события ('invoice.payment_succeeded')
        'external_id',       // id события у провайдера
        'idempotency_key',   // ключ идемпотентности
        'signature',         // подпись провайдера
        'order_id',          // FK -> orders.id (nullable)
        'payment_id',        // FK -> payments.id (nullable)
        'subscription_id',   // FK -> subscriptions.id (nullable)
        'payload',           // сырое тело (JSON)
        'headers',           // сырые заголовки (JSON)
        'status',            // received|processing|processed|failed|skipped
        'attempts',          // количество попыток обработки
        'error_message',     // последняя ошибка
        'delivered_at',      // когда пришёл вебхук
        'processed_at',      // когда обработан
    ];

    protected $casts = [
        'payload'      => 'array',
        'headers'      => 'array',
        'attempts'     => 'int',
        'delivered_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /* =============== Связи =============== */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /* =============== Скоупы =============== */

    public function scopeProvider($q, string $provider)
    {
        return $q->where('provider', $provider);
    }

    public function scopePending($q)
    {
        return $q->whereIn('status', ['received', 'processing']);
    }

    public function scopeFailed($q)
    {
        return $q->where('status', 'failed');
    }

    /* =============== Хелперы =============== */

    public function markProcessed(): void
    {
        $this->status = 'processed';
        $this->processed_at = now();
        $this->error_message = null;
        $this->save();
    }

    public function markFailed(string $message): void
    {
        $this->status = 'failed';
        $this->error_message = $message;
        $this->attempts = ($this->attempts ?? 0) + 1;
        $this->save();
    }
}
