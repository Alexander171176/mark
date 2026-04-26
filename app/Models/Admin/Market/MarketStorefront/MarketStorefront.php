<?php

namespace App\Models\Admin\Market\MarketStorefront;

use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Market\MarketCompany\MarketCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketStorefront extends Model
{
    use HasFactory;

    protected $table = 'market_storefronts';

    protected $fillable = [
        'company_id',
        'sort',
        'activity',

        'slug',
        'is_main',

        'domain',
        'subdomain',
        'primary_host',

        'default_locale',
        'default_currency_id',

        'note',
    ];

    protected $casts = [
        'sort' => 'integer',
        'activity' => 'boolean',
        'is_main' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function company(): BelongsTo
    {
        return $this->belongsTo(MarketCompany::class, 'company_id');
    }

    public function defaultCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'default_currency_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('activity', true);
    }

    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort');
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}
