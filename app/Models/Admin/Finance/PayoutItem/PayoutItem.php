<?php

namespace App\Models\Admin\Finance\PayoutItem;

use App\Models\Admin\Finance\Order\Order;
use App\Models\Admin\Finance\OrderItem\OrderItem;
use App\Models\Admin\Finance\Payout\Payout;
use App\Models\Admin\Finance\Subscription\Subscription;
use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\Course\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutItem extends Model
{
    use HasFactory;

    protected $table = 'payout_items';

    protected $fillable = [
        'payout_id',        // FK -> payouts.id
        'order_id',         // FK -> orders.id (nullable)
        'order_item_id',    // FK -> order_items.id (nullable)
        'course_id',        // FK -> courses.id (nullable)
        'bundle_id',        // FK -> bundles.id (nullable)
        'subscription_id',  // FK -> subscriptions.id (nullable)
        'currency',         // ISO 4217
        'amount_gross',     // начислено брутто
        'fee_total',        // комиссии
        'tax_total',        // налоги/удержания
        'amount_net',       // к выплате по позиции
        'earned_at',        // когда доход был заработан
        'title',            // подпись позиции
        'notes',            // заметки
        'meta',             // произвольные данные (JSON)
    ];

    protected $casts = [
        'amount_gross' => 'decimal:2',
        'fee_total'    => 'decimal:2',
        'tax_total'    => 'decimal:2',
        'amount_net'   => 'decimal:2',
        'earned_at'    => 'datetime',
        'meta'         => 'array',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /* =============== Связи =============== */

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /* =============== Хелперы =============== */

    // На всякий случай — пересчёт нетто из брутто/удержаний
    public function getComputedNetAttribute(): string
    {
        return (string) ($this->amount_gross - $this->fee_total - $this->tax_total);
    }

    // Короткая подпись для таблиц/отчётов
    public function getDisplayLabelAttribute(): string
    {
        if ($this->title) return $this->title;
        if ($this->order_item_id) return "Позиция #{$this->order_item_id}";
        if ($this->course?->title) return "Курс: {$this->course->title}";
        if ($this->bundle?->title) return "Бандл: {$this->bundle->title}";
        if ($this->subscription_id) return "Подписка #{$this->subscription_id}";
        return "Позиция выплаты #{$this->id}";
    }
}
