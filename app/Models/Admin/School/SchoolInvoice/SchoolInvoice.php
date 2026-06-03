<?php

namespace App\Models\Admin\School\SchoolInvoice;

use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolInvoice extends Model
{
    use HasFactory;

    protected $table = 'school_invoices';

    protected $fillable = [
        'school_order_id',
        'number',
        'status',
        'currency',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'issued_at',
        'due_at',
        'paid_at',
        'bill_to_name',
        'bill_to_tax_id',
        'bill_to_email',
        'bill_to_address1',
        'bill_to_address2',
        'bill_to_city',
        'bill_to_region',
        'bill_to_postcode',
        'bill_to_country',
        'notes',
        'meta',
    ];

    protected $casts = [
        'school_order_id' => 'integer',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
        'meta' => 'array',
    ];

    /** Заказ */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SchoolOrder::class, 'school_order_id');
    }

    /** Просроченные */
    public function scopeOverdue(Builder $q): Builder
    {
        return $q
            ->whereNull('paid_at')
            ->where('status', 'issued')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now());
    }

    /** Ожидают оплаты */
    public function scopePendingPayment(Builder $q): Builder
    {
        return $q
            ->where('status', 'issued')
            ->whereNull('paid_at');
    }

    /** Оплаченные */
    public function scopePaid(Builder $q): Builder
    {
        return $q->where(function (Builder $query) {
            $query->where('status', 'paid')
                ->orWhereNotNull('paid_at');
        });
    }

    /** Оплачен ли инвойс */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid' || !is_null($this->paid_at);
    }
}
