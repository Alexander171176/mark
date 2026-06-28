<?php

namespace App\Models\Admin\Market\MarketBrand;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketBrandTranslation extends Model
{
    use HasFactory;

    protected $table = 'market_brand_translations';

    protected $fillable = [
        'market_brand_id',
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

    /** Бренд перевода */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(MarketBrand::class, 'market_brand_id');
    }
}
