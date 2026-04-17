<?php

namespace App\Models\Admin\Market\MarketCompany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MarketCompany extends Model
{
    use HasFactory;

    protected $table = 'market_companies';

    protected $fillable = [
        'owner_user_id',
        'sort',
        'activity',

        'name',
        'brand_name',
        'legal_name',

        'slug',
        'external_id',

        'company_type',
        'tax_regime',
        'bin_iin',

        'email',
        'phone',

        'messenger_type',
        'messenger_contact',

        'country',
        'city',
        'legal_address',
        'actual_address',
    ];

    protected $casts = [
        'activity' => 'boolean',
        'sort' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('activity', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort');
    }
}
