<?php

namespace App\Models\Admin\Finance\UserPaymentMethod;

use App\Models\Admin\Finance\PaymentMethod\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_payment_methods';

    protected $fillable = [
        'user_id',                     // FK -> users.id
        'payment_method_id',           // FK -> payment_methods.id (справочник)
        'provider',                    // имя провайдера/шлюза
        'provider_customer_id',        // id клиента в провайдере
        'provider_payment_method_id',  // id сохранённого метода/токена

        'brand', 'last4', 'exp_month', 'exp_year', 'country', // карточные данные
        'billing_name', 'billing_email', 'billing_phone',
        'billing_address',             // JSON адрес
        'is_default',                  // по умолчанию для пользователя
        'activity',                   // включён/выключен
        'meta',                        // произвольные данные (JSON)
    ];

    protected $casts = [
        'exp_month'       => 'int',
        'exp_year'        => 'int',
        'is_default'      => 'bool',
        'activity'       => 'bool',
        'billing_address' => 'array',
        'meta'            => 'array',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    /* ========= Связи ========= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /* ========= Скоупы ========= */

    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    public function scopeDefaultForUser($q, int $userId)
    {
        return $q->where('user_id', $userId)->where('is_default', true);
    }
}
