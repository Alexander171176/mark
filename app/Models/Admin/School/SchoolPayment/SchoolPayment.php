<?php

namespace App\Models\Admin\School\SchoolPayment;

use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use App\Models\Admin\School\SchoolPaymentMethod\SchoolPaymentMethod;
use App\Models\Admin\School\SchoolRefund\SchoolRefund;
use App\Models\Admin\School\SchoolUserPaymentMethod\SchoolUserPaymentMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolPayment extends Model
{
    use HasFactory;

    protected $table = 'school_payments';

    protected $fillable = [
        'school_order_id',
        'school_payment_method_id',
        'school_user_payment_method_id',
        'provider',
        'provider_payment_id',
        'idempotency_key',
        'status',
        'currency',
        'amount',
        'captured_at',
        'refunded_at',
        'refunded_amount',
        'error_code',
        'error_message',
        'meta',
    ];

    protected $casts = [
        'school_order_id' => 'integer',
        'school_payment_method_id' => 'integer',
        'school_user_payment_method_id' => 'integer',
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'captured_at' => 'datetime',
        'refunded_at' => 'datetime',
        'meta' => 'array',
    ];

    /** Заказ */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SchoolOrder::class, 'school_order_id');
    }

    /** Способ оплаты */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(SchoolPaymentMethod::class, 'school_payment_method_id');
    }

    /** Сохранённый способ оплаты */
    public function userPaymentMethod(): BelongsTo
    {
        return $this->belongsTo(SchoolUserPaymentMethod::class, 'school_user_payment_method_id');
    }

    /** Возвраты */
    public function refunds(): HasMany
    {
        return $this->hasMany(SchoolRefund::class, 'school_payment_id');
    }

    /** Успешные */
    public function scopeSucceeded(Builder $q): Builder
    {
        return $q->where('status', 'succeeded');
    }

    /** Ожидающие */
    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    /** Неудачные */
    public function scopeFailed(Builder $q): Builder
    {
        return $q->where('status', 'failed');
    }

    /** Возвращённые */
    public function scopeRefunded(Builder $q): Builder
    {
        return $q->whereIn('status', ['refunded', 'partially_refunded']);
    }
}
