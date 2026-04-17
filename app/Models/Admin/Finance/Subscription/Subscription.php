<?php

namespace App\Models\Admin\Finance\Subscription;

use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\Finance\SubscriptionPlan\SubscriptionPlan;
use App\Models\Admin\Finance\UserPaymentMethod\UserPaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'order_id',
        'user_payment_method_id',

        'currency',
        'price',
        'billing_period',
        'interval',
        'trial_days',
        'trial_ends_at',

        'current_period_start',
        'current_period_end',
        'started_at',
        'ends_at',
        'cancelled_at',
        'cancel_at_period_end',

        'status',

        'provider',
        'provider_subscription_id',
        'last_paid_at',
        'next_billing_at',
        'renewal_attempts',

        'features',
        'limits',
        'meta',
    ];

    protected $casts = [
        'price'                => 'decimal:2',
        'interval'             => 'int',
        'trial_days'           => 'int',
        'cancel_at_period_end' => 'bool',
        'renewal_attempts'     => 'int',

        'trial_ends_at'        => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end'   => 'datetime',
        'started_at'           => 'datetime',
        'ends_at'              => 'datetime',
        'cancelled_at'         => 'datetime',
        'last_paid_at'         => 'datetime',
        'next_billing_at'      => 'datetime',

        'features'             => 'array',
        'limits'               => 'array',
        'meta'                 => 'array',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
        'deleted_at'           => 'datetime',
    ];

    /* ===== Связи ===== */

    // Владелец
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Тарифный план
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    // Исходный заказ (если был)
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Привязанный способ оплаты пользователя
    public function userPaymentMethod(): BelongsTo
    {
        return $this->belongsTo(UserPaymentMethod::class);
    }

    /* ===== Скоупы ===== */

    public function scopeActive($q, bool $includeTrial = true)
    {
        return $q->whereIn('status', $includeTrial ? ['active','trialing'] : ['active']);
    }

    public function scopeDueForBilling($q)
    {
        return $q->whereNotNull('next_billing_at')
            ->where('next_billing_at', '<=', now())
            ->whereIn('status', ['active', 'past_due', 'incomplete']);
    }

    public function scopeCancelAtPeriodEnd($q)
    {
        return $q->where('cancel_at_period_end', true)
            ->whereNull('ends_at')
            ->whereNotNull('current_period_end');
    }
}
