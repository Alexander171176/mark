<?php

namespace App\Models\Admin\Finance\OrderItem;

use App\Models\Admin\Finance\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    /**
     * Поля под новую универсальную миграцию order_items.
     */
    protected $fillable = [
        'order_id',

        // Полиморфная связь
        'purchasable_type',
        'purchasable_id',

        // Снапшот данных позиции
        'title',
        'sku',
        'unit_name',

        // Цены и количество
        'currency',
        'quantity',
        'unit_price',
        'discount',
        'total',

        // Доп. атрибуты и мета
        'attributes',
        'meta',
    ];

    protected $casts = [
        'quantity'   => 'int',
        'unit_price' => 'decimal:2',
        'discount'   => 'decimal:2',
        'total'      => 'decimal:2',
        'attributes' => 'array',
        'meta'       => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ================= Связи ================= */

    // Заказ, к которому относится позиция
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Универсальная покупаемая сущность:
    // Course, Bundle, Product, SubscriptionPlan, Service и т.п.
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    /* ================= Логика/хелперы ================= */

    // Автопересчёт total, если не задан явно
    protected static function booted(): void
    {
        static::saving(function (OrderItem $item) {
            if (is_null($item->total)) {
                $gross = (float) $item->unit_price * (int) $item->quantity;
                $net   = max(0, $gross - (float) $item->discount);
                $item->total = $net;
            }
        });
    }

    // Удобный «виртуальный» доступ к типу позиции (course, product и т.п.)
    public function getTypeAttribute(): ?string
    {
        // Можно использовать короткие алиасы через morphMap, но пока даём FQCN
        return $this->purchasable_type;
    }
}
