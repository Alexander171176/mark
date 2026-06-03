<?php

namespace App\Models\Admin\School\SchoolRefund;

use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use App\Models\Admin\School\SchoolPayment\SchoolPayment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolRefund extends Model
{
    use HasFactory;

    protected $table = 'school_refunds';

    protected $fillable = [
        'school_order_id',
        'school_payment_id',
        'provider',
        'provider_refund_id',
        'status',
        'currency',
        'amount',
        'reason',
        'notes',
        'meta',
        'requested_at',
        'processed_at',
    ];

    protected $casts = [
        'school_order_id' => 'integer',
        'school_payment_id' => 'integer',
        'amount' => 'decimal:2',
        'meta' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

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

    /* ======================== Scopes ======================== */

    /** Успешные */
    public function scopeSucceeded(Builder $q): Builder
    {
        return $q->where('status', 'succeeded');
    }

    /** В обработке */
    public function scopeProcessing(Builder $q): Builder
    {
        return $q->where('status', 'processing');
    }

    /** Ошибочные */
    public function scopeFailed(Builder $q): Builder
    {
        return $q->where('status', 'failed');
    }

    /** Запрошенные */
    public function scopeRequested(Builder $q): Builder
    {
        return $q->where('status', 'requested');
    }

    /** Отменённые */
    public function scopeCanceled(Builder $q): Builder
    {
        return $q->where('status', 'canceled');
    }
}
