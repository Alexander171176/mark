<?php

namespace App\Models\Admin\Finance\Order;

use App\Models\Admin\Finance\OrderItem\OrderItem;
use App\Models\Admin\Finance\Payment\Payment;
use App\Models\Admin\Finance\Refund\Refund;
use App\Models\Admin\School\Enrollment\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    /**
     * Разрешённые к массовому заполнению поля.
     * Структура соответствует универсальной миграции orders.
     */
    protected $fillable = [
        // Модуль 1 — кто оформил
        'user_id',
        'number',

        // Модуль 2 — покупатель (физ/юр)
        'buyer_name',
        'buyer_email',
        'buyer_phone',
        'billing_company',
        'billing_tax_id',
        'billing_address',

        // Модуль 3 — адрес и доставка
        'shipping_address',
        'shipping_address_parts',
        'delivery_method_id',
        'delivery_cost',
        'delivery_options',
        'delivery_interval',
        'warehouse',
        'delivery_date',

        // Модуль 4 — оплата
        'is_paid',
        'paid_at',
        'payment_method_id',
        'payment_method',
        'payment_provider',
        'payment_reference',
        'confirmation_code',
        'confirmation_status',
        'failure_reason',

        // Модуль 5 — мультивалютность
        'currency',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'total_shop_currency',
        'delivery_shop_currency',

        // Модуль 6 — статусы
        'status',
        'payment_status',

        // Модуль 7 — контент заказа
        'items',
        'meta',

        // Модуль 8 — комментарии
        'user_comment',
        'manager_comment',

        // Модуль 9 — интеграции
        'external_id',
        'exported_at',

        // Модуль 10 — тех.данные
        'client_ip',
        'user_agent',
        'public_hash',
    ];

    protected $casts = [
        // Деньги
        'subtotal'              => 'decimal:2',
        'discount_total'        => 'decimal:2',
        'tax_total'             => 'decimal:2',
        'total'                 => 'decimal:2',
        'delivery_cost'         => 'decimal:2',
        'total_shop_currency'   => 'decimal:2',
        'delivery_shop_currency'=> 'decimal:2',

        // Флаги
        'is_paid'               => 'bool',

        // JSON
        'shipping_address_parts'=> 'array',
        'delivery_options'      => 'array',
        'items'                 => 'array',
        'meta'                  => 'array',

        // Даты
        'paid_at'               => 'datetime',
        'delivery_date'         => 'datetime',
        'exported_at'           => 'datetime',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    /* ================= Связи ================= */

    // Пользователь, оформивший заказ (может быть null для гостя)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Позиции заказа (курсы, товары, услуги, подписки, комплекты и т.п.)
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Платежи по заказу (ретраи, доплаты и т.п.)
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Возвраты по заказу
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    // Зачисления (для онлайн-школы), если order_id хранится в enrollments
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /* ================= Скоупы ================= */

    // Оплаченные (учитываем и флаг, и payment_status)
    public function scopePaid($q)
    {
        return $q->where(function ($q) {
            $q->where('is_paid', true)
                ->orWhere('payment_status', 'paid');
        });
    }

    // Открытые (в работе, ожидают обработки/оплаты)
    public function scopeOpen($q)
    {
        return $q->whereIn('status', ['new', 'processing']);
    }

    // Только с ненулевым итогом (для отчётов)
    public function scopeWithTotal($q)
    {
        return $q->where('total', '>', 0);
    }
}
