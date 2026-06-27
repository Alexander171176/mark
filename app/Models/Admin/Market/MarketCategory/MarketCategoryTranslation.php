<?php

namespace App\Models\Admin\Market\MarketCategory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketCategoryTranslation extends Model
{
    use HasFactory;

    protected $table = 'market_category_translations';

    protected $fillable = [
        'market_category_id',
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

    /** Категория перевода */
    public function category(): BelongsTo
    {
        return $this->belongsTo(
            MarketCategory::class,
            'market_category_id'
        );
    }
}
