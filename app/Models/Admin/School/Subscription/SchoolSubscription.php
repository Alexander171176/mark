<?php

namespace App\Models\Admin\School\Subscription;

use App\Models\Admin\School\Order\SchoolOrder;
use App\Models\Admin\School\SubscriptionPlan\SchoolSubscriptionPlan;
use App\Models\Admin\School\UserPaymentMethod\SchoolUserPaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolSubscription extends Model
{
    use HasFactory;

    protected $table = 'school_subscriptions';

    protected $fillable = [
        'user_id',
        'school_subscription_plan_id',
        'school_order_id',
        'school_user_payment_method_id',

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
        'user_id' => 'integer',
        'school_subscription_plan_id' => 'integer',
        'school_order_id' => 'integer',
        'school_user_payment_method_id' => 'integer',

        'price' => 'decimal:2',
        'interval' => 'integer',
        'trial_days' => 'integer',
        'cancel_at_period_end' => 'boolean',
        'renewal_attempts' => 'integer',

        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_paid_at' => 'datetime',
        'next_billing_at' => 'datetime',

        'features' => 'array',
        'limits' => 'array',
        'meta' => 'array',
    ];

    /* ======================== Relations ======================== */

    /** Пользователь */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Тарифный план */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SchoolSubscriptionPlan::class, 'school_subscription_plan_id');
    }

    /** Заказ */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SchoolOrder::class, 'school_order_id');
    }

    /** Способ оплаты пользователя */
    public function userPaymentMethod(): BelongsTo
    {
        return $this->belongsTo(SchoolUserPaymentMethod::class, 'school_user_payment_method_id');
    }

    /* ======================== Scopes ======================== */

    /** Активные */
    public function scopeActive(Builder $q, bool $includeTrial = true): Builder
    {
        return $q->whereIn('status', $includeTrial ? ['active', 'trialing'] : ['active']);
    }

    /** Готовые к списанию */
    public function scopeDueForBilling(Builder $q): Builder
    {
        return $q
            ->whereNotNull('next_billing_at')
            ->where('next_billing_at', '<=', now())
            ->whereIn('status', ['active', 'past_due', 'incomplete']);
    }

    /** Отмена в конце периода */
    public function scopeCancelAtPeriodEnd(Builder $q): Builder
    {
        return $q
            ->where('cancel_at_period_end', true)
            ->whereNull('ends_at')
            ->whereNotNull('current_period_end');
    }

    /** По провайдеру */
    public function scopeByProvider(Builder $q, string $provider): Builder
    {
        return $q->where('provider', $provider);
    }

    /** По статусу */
    public function scopeStatus(Builder $q, string $status): Builder
    {
        return $q->where('status', $status);
    }

    /* ======================== Accessors ======================== */

    /** Активна ли подписка */
    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, ['active', 'trialing'], true);
    }

    /** На триале ли подписка */
    public function getIsTrialingAttribute(): bool
    {
        return $this->status === 'trialing'
            && $this->trial_ends_at
            && $this->trial_ends_at->isFuture();
    }

    /** Истекла ли подписка */
    public function getIsExpiredAttribute(): bool
    {
        return $this->ends_at !== null && $this->ends_at->isPast();
    }
}
