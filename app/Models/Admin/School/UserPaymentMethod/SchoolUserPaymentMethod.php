<?php

namespace App\Models\Admin\School\UserPaymentMethod;

use App\Models\Admin\School\PaymentMethod\SchoolPaymentMethod;
use App\Models\Admin\School\Payment\SchoolPayment;
use App\Models\Admin\School\Subscription\SchoolSubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolUserPaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'school_user_payment_methods';

    protected $fillable = [
        'user_id',
        'school_payment_method_id',
        'provider',
        'provider_customer_id',
        'provider_payment_method_id',
        'brand',
        'last4',
        'exp_month',
        'exp_year',
        'country',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'is_default',
        'activity',
        'meta',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'school_payment_method_id' => 'integer',
        'exp_month' => 'integer',
        'exp_year' => 'integer',
        'is_default' => 'boolean',
        'activity' => 'boolean',
        'billing_address' => 'array',
        'meta' => 'array',
    ];

    /** Пользователь */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Способ оплаты */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(SchoolPaymentMethod::class, 'school_payment_method_id');
    }

    /** Платежи */
    public function payments(): HasMany
    {
        return $this->hasMany(SchoolPayment::class, 'school_user_payment_method_id');
    }

    /** Подписки */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(SchoolSubscription::class, 'school_user_payment_method_id');
    }

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Метод по умолчанию пользователя */
    public function scopeDefaultForUser(Builder $q, int $userId): Builder
    {
        return $q
            ->where('user_id', $userId)
            ->where('is_default', true);
    }
}
