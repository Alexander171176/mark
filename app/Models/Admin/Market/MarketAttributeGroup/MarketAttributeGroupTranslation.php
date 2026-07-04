<?php

namespace App\Models\Admin\Market\MarketAttributeGroup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketAttributeGroupTranslation extends Model
{
    use HasFactory;

    protected $table = 'market_attribute_group_translations';

    protected $fillable = [
        'market_attribute_group_id',
        'locale',
        'title',
        'subtitle',
        'short',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** Группа характеристик перевода */
    public function group(): BelongsTo
    {
        return $this->belongsTo(
            MarketAttributeGroup::class,
            'market_attribute_group_id'
        );
    }
}
