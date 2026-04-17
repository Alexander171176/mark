<?php

namespace App\Models\Admin\Finance\Currency;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies';

    protected $fillable = [
        'sort',
        'code',
        'name',
        'symbol',
        'precision',
        'symbol_first',
        'thousands_sep',
        'decimal_sep',
        'activity',
        'is_default',
        'set_default_at',
    ];

    protected $casts = [
        'sort'           => 'int',
        'precision'      => 'int',
        'symbol_first'   => 'bool',
        'activity'       => 'bool',
        'is_default'     => 'bool',
        'set_default_at' => 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /* ===================== Relations ===================== */

    public function baseRates(): HasMany
    {
        return $this->hasMany(CurrencyRate::class, 'base_currency_id');
    }

    public function quoteRates(): HasMany
    {
        return $this->hasMany(CurrencyRate::class, 'quote_currency_id');
    }

    /**
     * Алиас под вложенные роуты/биндинг (как у тебя было).
     */
    public function rates(): HasMany
    {
        return $this->baseRates();
    }

    /* ===================== Scopes ===================== */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    public function scopeDefault(Builder $q): Builder
    {
        return $q->where('is_default', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderBy('code');
    }

    /* ===================== Normalization ===================== */

    /**
     * Храним всегда ISO-4217 uppercase, 3 символа.
     */
    public function setCodeAttribute($value): void
    {
        $code = strtoupper(trim((string) $value));
        $code = substr($code, 0, 3);
        $this->attributes['code'] = $code;
    }

    /* ===================== Helpers ===================== */

    public function formatAmount(float|int $amount): string
    {
        $formatted = number_format(
            (float) $amount,
            (int) $this->precision,
            (string) $this->decimal_sep,
            (string) $this->thousands_sep
        );

        $symbol = trim((string) ($this->symbol ?? ''));

        if ($symbol === '') {
            return $formatted;
        }

        return $this->symbol_first
            ? "{$symbol}{$formatted}"
            : "{$formatted} {$symbol}";
    }
}
