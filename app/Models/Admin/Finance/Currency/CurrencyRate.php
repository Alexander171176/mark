<?php

namespace App\Models\Admin\Finance\Currency;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrencyRate extends Model
{
    use HasFactory;

    protected $table = 'currency_rates';

    protected $fillable = [
        'base_currency_id',
        'quote_currency_id',
        'rate',
        'provider',
        'is_manual',
        'fetched_at',
    ];

    protected $casts = [
        'base_currency_id'  => 'int',
        'quote_currency_id' => 'int',

        // контракт “история”: rate хранится как decimal в БД
        'rate'              => 'decimal:8',

        'is_manual'         => 'bool',
        'fetched_at'        => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    /* ===================== Relations ===================== */

    public function base(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'quote_currency_id');
    }

    /* ===================== Scopes ===================== */

    public function scopeForPair(Builder $q, int $baseId, int $quoteId): Builder
    {
        return $q->where('base_currency_id', $baseId)
            ->where('quote_currency_id', $quoteId);
    }

    /**
     * “Latest” по истории: сначала fetched_at, затем id для устойчивости.
     */
    public function scopeLatestFirst(Builder $q): Builder
    {
        return $q->orderByDesc('fetched_at')->orderByDesc('id');
    }

    /**
     * Последняя запись для пары (оставляем query-builder, чтобы можно было ->first()).
     */
    public function scopeLatestForPair(Builder $q, int $baseId, int $quoteId): Builder
    {
        return $q->forPair($baseId, $quoteId)->latestFirst();
    }

    /**
     * Удобно для выборки последних курсов по базе:
     * CurrencyRate::latestByBase($baseId)->get()->unique('quote_currency_id')
     */
    public function scopeLatestByBase(Builder $q, int $baseId): Builder
    {
        return $q->where('base_currency_id', $baseId)->latestFirst();
    }

    /* ===================== Accessors ===================== */

    public function getPairAttribute(): string
    {
        $base  = $this->relationLoaded('base') ? ($this->base?->code ?? 'BASE') : (string) $this->base_currency_id;
        $quote = $this->relationLoaded('quote') ? ($this->quote?->code ?? 'QUOTE') : (string) $this->quote_currency_id;

        return "{$base}/{$quote}";
    }
}
