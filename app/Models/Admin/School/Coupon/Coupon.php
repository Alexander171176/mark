<?php

namespace App\Models\Admin\School\Coupon;

use App\Models\Admin\School\Bundle\Bundle;
use App\Models\Admin\School\Course\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'coupons';

    protected $fillable = [
        'code',               // Промокод
        'name',               // Название
        'description',        // Описание для админки
        'type',               // percent|fixed|free
        'value',              // % или сумма
        'currency',           // Валюта (для fixed)
        'min_order_total',    // Мин. сумма заказа
        'max_uses',           // Общий лимит
        'max_uses_per_user',  // Лимит на пользователя
        'used_count',         // Счётчик использований
        'applies_to',         // any|courses|bundles
        'starts_at',          // Начало действия
        'ends_at',            // Окончание действия
        'activity',           // Флаг активности
        'stackable',          // Можно ли комбинировать
        'meta',               // JSON
    ];

    protected $casts = [
        'value'            => 'decimal:2',
        'min_order_total'  => 'decimal:2',
        'activity'         => 'bool',
        'stackable'        => 'bool',
        'starts_at'        => 'datetime',
        'ends_at'          => 'datetime',
        'meta'             => 'array',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    /* ============== Связи ============== */

    // Курсы, к которым привязан купон
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'coupon_has_course');
    }

    // Бандлы, к которым привязан купон
    public function bundles(): BelongsToMany
    {
        return $this->belongsToMany(Bundle::class, 'coupon_has_bundle');
    }

    /* ============== Скоупы/хелперы ============== */

    // Текущие (по времени и активности)
    public function scopeCurrent($q)
    {
        $now = now();
        return $q->where('activity', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }

    // Проверка актуальности купона на текущий момент (без бизнес-логики корзины)
    public function getIsCurrentlyValidAttribute(): bool
    {
        $now = now();
        $inWindow = (is_null($this->starts_at) || $this->starts_at->lte($now))
            && (is_null($this->ends_at)   || $this->ends_at->gte($now));
        return $this->activity && $inWindow;
    }
}
