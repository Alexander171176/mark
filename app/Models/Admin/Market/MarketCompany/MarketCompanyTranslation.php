<?php

namespace App\Models\Admin\Market\MarketCompany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketCompanyTranslation extends Model
{
    use HasFactory;

    protected $table = 'market_company_translations';

    protected $fillable = [
        'market_company_id',
        'locale',
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(MarketCompany::class, 'market_company_id');
    }
}
