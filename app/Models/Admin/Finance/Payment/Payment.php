<?php

namespace App\Models\Admin\Finance\Payment;

use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\Finance\PaymentMethod\PaymentMethod;
use App\Models\Admin\Finance\Refund\Refund;
use App\Models\Admin\Finance\UserPaymentMethod\UserPaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'order_id',                 // FK -> orders.id
        'payment_method_id',        // FK -> payment_methods.id
        'user_payment_method_id',   // FK -> user_payment_methods.id (если использовался сохранённый метод)
        'provider',                 // платёжный провайдер
        'provider_payment_id',      // id платежа у провайдера
        'idempotency_key',          // идемпотентность
        'status',                   // pending|processing|succeeded|failed|...
        'currency',                 // ISO 4217
        'amount',                   // сумма платежа
        'captured_at',              // когда подтверждён/захвачен
        'refunded_at',              // когда возвращён
        'refunded_amount',          // сумма возврата (частичный)
        'error_code',               // код ошибки провайдера
        'error_message',            // текст ошибки
        'meta',                     // произвольные данные (JSON)
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'meta'            => 'array',
        'captured_at'     => 'datetime',
        'refunded_at'     => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    /* ================= Связи ================= */

    // Заказ, к которому относится платёж
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Тип способа оплаты (справочник)
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Сохранённый способ пользователя (если применялся)
    public function userPaymentMethod(): BelongsTo
    {
        return $this->belongsTo(UserPaymentMethod::class);
    }

    // Связанные возвраты (если у вас есть отдельная сущность Refund)
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    /* ================= Скоупы ================= */

    public function scopeSucceeded($q) { return $q->where('status', 'succeeded'); }
    public function scopePending($q)   { return $q->where('status', 'pending'); }
    public function scopeFailed($q)    { return $q->where('status', 'failed'); }
}
