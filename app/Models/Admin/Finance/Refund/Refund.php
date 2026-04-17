<?php

namespace App\Models\Admin\Finance\Refund;

use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\Finance\Payment\Payment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'refunds';

    protected $fillable = [
        'order_id',              // FK -> orders.id
        'payment_id',            // FK -> payments.id (опционально)
        'provider',              // платёжный провайдер
        'provider_refund_id',    // id возврата у провайдера
        'status',                // requested|processing|succeeded|failed|canceled
        'currency',              // ISO 4217
        'amount',                // сумма возврата
        'reason',                // причина
        'notes',                 // заметки
        'meta',                  // произвольные данные (JSON)
        'requested_at',          // когда инициирован
        'processed_at',          // когда завершён
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'meta'         => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /* ================= Связи ================= */

    // Заказ
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Платёж (если возврат привязан к конкретному платежу)
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /* ================= Скоупы ================= */

    public function scopeSucceeded($q) { return $q->where('status', 'succeeded'); }
    public function scopeProcessing($q){ return $q->where('status', 'processing'); }
    public function scopeFailed($q)    { return $q->where('status', 'failed'); }
}
