<?php

namespace App\Models\Admin\Finance\Invoice;

use App\Models\Admin\Finance\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoices';

    protected $fillable = [
        'order_id',          // FK -> orders.id
        'number',            // номер инвойса (уникальный)
        'status',            // draft|issued|paid|void|refunded
        'currency',          // валюта ISO 4217
        'subtotal',          // сумма позиций
        'discount_total',    // скидки
        'tax_total',         // налоги
        'total',             // итог к оплате
        'issued_at',         // выставлен
        'due_at',            // срок оплаты
        'paid_at',           // оплачен
        // реквизиты плательщика (снимок)
        'bill_to_name',
        'bill_to_tax_id',
        'bill_to_email',
        'bill_to_address1',
        'bill_to_address2',
        'bill_to_city',
        'bill_to_region',
        'bill_to_postcode',
        'bill_to_country',
        'notes',             // примечания
        'meta',              // произвольный JSON
    ];

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total'      => 'decimal:2',
        'total'          => 'decimal:2',
        'issued_at'      => 'datetime',
        'due_at'         => 'datetime',
        'paid_at'        => 'datetime',
        'meta'           => 'array',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    /* ================= Связи ================= */

    // Заказ, к которому относится инвойс
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /* ================= Скоупы/хелперы ================= */

    // Просроченные к оплате
    public function scopeOverdue($q)
    {
        return $q->whereNull('paid_at')
            ->where('status', 'issued')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now());
    }

    // К оплате (выставлен и не оплачен)
    public function scopePendingPayment($q)
    {
        return $q->where('status', 'issued')->whereNull('paid_at');
    }

    // Удобный флаг
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid' || !is_null($this->paid_at);
    }
}
