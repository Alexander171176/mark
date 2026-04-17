<?php

namespace App\Models\Admin\Finance\PaymentMethod;

use App\Models\Admin\Finance\Payment\Payment;
use App\Models\Admin\Finance\UserPaymentMethod\UserPaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_methods';

    protected $fillable = [
        'code',               // системный код
        'name',               // человеко-читаемое имя
        'provider',           // имя провайдера/шлюза
        'type',               // card|bank_transfer|ewallet|cash|invoice|other

        'supports_refund',    // поддержка возвратов
        'supports_recurring', // поддержка подписок/рекуррентных платежей

        'activity',           // включён/выключен
        'sort',               // порядок отображения
        'meta',               // произвольные настройки (JSON)
    ];

    protected $casts = [
        'supports_refund'    => 'bool',
        'supports_recurring' => 'bool',
        'activity'           => 'bool',
        'sort'               => 'int',
        'meta'               => 'array',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
        'deleted_at'         => 'datetime',
    ];

    /* ============ Связи ============ */

    // Привязки пользователей к этому способу (их сохранённые методы оплаты)
    public function userPaymentMethods(): HasMany
    {
        return $this->hasMany(UserPaymentMethod::class);
    }

    // Платежи, в которых использован этот способ (если вы храните FK на справочник)
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /* ============ Скоупы ============ */

    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    public function scopeByCode($q, string $code)
    {
        return $q->where('code', $code);
    }
}
