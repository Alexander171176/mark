<?php

namespace App\Models\Admin\Finance\ExchangeRate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExchangeRate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exchange_rates';

    protected $fillable = [
        'base_currency',   // ISO 4217 (например, USD)
        'quote_currency',  // ISO 4217 (например, EUR)
        'rate',            // 1 base = rate quote
        'provider',        // источник курсов (ECB/Fixer/ЦБР и т.п.)
        'fetched_at',      // когда получили у провайдера
        'valid_from',      // окно валидности: начало
        'valid_to',        // окно валидности: конец (nullable)
        'activity',       // использовать ли в расчётах
        'meta',            // произвольные данные (JSON)
    ];

    protected $casts = [
        'rate'       => 'decimal:8',
        'activity'  => 'bool',
        'fetched_at' => 'datetime',
        'valid_from' => 'datetime',
        'valid_to'   => 'datetime',
        'meta'       => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /* ================= Скоупы ================= */

    // Текущие активные записи для пары
    public function scopeActivePair($q, string $base, string $quote)
    {
        return $q->where('base_currency', strtoupper($base))
            ->where('quote_currency', strtoupper($quote))
            ->where('activity', true);
    }

    // Актуальные “на сейчас” (с учётом окна валидности)
    public function scopeActualNow($q)
    {
        $now = now();
        return $q->where(function ($q) use ($now) {
            $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
        })
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_to')->orWhere('valid_to', '>=', $now);
            })
            ->where('activity', true);
    }

    // Удобный геттер: курс в виде float (иногда удобно)
    public function getRateFloatAttribute(): float
    {
        return (float) $this->rate;
    }

    /* ============== Хелпер конвертации ============== */

    /**
     * Конвертация суммы из base в quote по текущему объекту курса.
     * Возвращает строку (точность денег лучше не терять).
     */
    public function convert(string|int|float $amount): string
    {
        return (string) (round((float)$amount * (float)$this->rate, 2));
    }
}
