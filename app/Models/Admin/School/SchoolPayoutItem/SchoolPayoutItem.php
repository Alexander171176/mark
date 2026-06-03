<?php

namespace App\Models\Admin\School\SchoolPayoutItem;

use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use App\Models\Admin\School\SchoolPayout\SchoolPayout;
use App\Models\Admin\School\SchoolBundle\SchoolBundle;
use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolOrderItem\SchoolOrderItem;
use App\Models\Admin\School\SchoolSubscription\SchoolSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolPayoutItem extends Model
{
    use HasFactory;

    protected $table = 'school_payout_items';

    protected $fillable = [
        'school_payout_id',
        'school_order_id',
        'school_order_item_id',
        'school_course_id',
        'school_bundle_id',
        'school_subscription_id',
        'currency',
        'amount_gross',
        'fee_total',
        'tax_total',
        'amount_net',
        'earned_at',
        'title',
        'notes',
        'meta',
    ];

    protected $casts = [
        'school_payout_id' => 'integer',
        'school_order_id' => 'integer',
        'school_order_item_id' => 'integer',
        'school_course_id' => 'integer',
        'school_bundle_id' => 'integer',
        'school_subscription_id' => 'integer',
        'amount_gross' => 'decimal:2',
        'fee_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'amount_net' => 'decimal:2',
        'earned_at' => 'datetime',
        'meta' => 'array',
    ];

    /** Выплата */
    public function payout(): BelongsTo
    {
        return $this->belongsTo(SchoolPayout::class, 'school_payout_id');
    }

    /** Заказ */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SchoolOrder::class, 'school_order_id');
    }

    /** Позиция заказа */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(SchoolOrderItem::class, 'school_order_item_id');
    }

    /** Курс */
    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    /** Набор курсов */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(SchoolBundle::class, 'school_bundle_id');
    }

    /** Подписка */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(SchoolSubscription::class, 'school_subscription_id');
    }

    /** Вычисленное нетто */
    public function getComputedNetAttribute(): string
    {
        return (string) ($this->amount_gross - $this->fee_total - $this->tax_total);
    }

    /** Подпись для отображения */
    public function getDisplayLabelAttribute(): string
    {
        if ($this->title) {
            return $this->title;
        }

        if ($this->school_order_item_id) {
            return "Позиция #{$this->school_order_item_id}";
        }

        if ($this->course?->translation?->title) {
            return "Курс: {$this->course->translation->title}";
        }

        if ($this->bundle?->translation?->title) {
            return "Набор: {$this->bundle->translation->title}";
        }

        if ($this->school_subscription_id) {
            return "Подписка #{$this->school_subscription_id}";
        }

        return "Позиция выплаты #{$this->id}";
    }
}
