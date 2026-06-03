<?php

namespace App\Models\Admin\School\SchoolOrderItem;

use App\Models\Admin\School\SchoolOrder\SchoolOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SchoolOrderItem extends Model
{
    use HasFactory;

    protected $table = 'school_order_items';

    protected $fillable = [
        'school_order_id',

        'purchasable_type',
        'purchasable_id',

        'title',
        'sku',
        'unit_name',

        'currency',
        'quantity',
        'unit_price',
        'discount',
        'total',

        'attributes',
        'meta',
    ];

    protected $casts = [
        'school_order_id' => 'integer',
        'purchasable_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'attributes' => 'array',
        'meta' => 'array',
    ];

    /* ======================== Relations ======================== */

    /** Заказ */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SchoolOrder::class, 'school_order_id');
    }

    /** Покупаемая сущность */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    /* ======================== Logic ======================== */

    /** Автопересчёт суммы */
    protected static function booted(): void
    {
        static::saving(function (SchoolOrderItem $item) {
            if (is_null($item->total)) {
                $gross = (float) $item->unit_price * (int) $item->quantity;
                $item->total = max(0, $gross - (float) $item->discount);
            }
        });
    }

    /* ======================== Accessors ======================== */

    /** Тип позиции */
    public function getTypeAttribute(): ?string
    {
        return $this->purchasable_type;
    }
}
