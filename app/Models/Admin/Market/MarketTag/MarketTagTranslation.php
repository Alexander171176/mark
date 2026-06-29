<?php

namespace App\Models\Admin\Market\MarketTag;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketTagTranslation extends Model
{
    use HasFactory;

    protected $table = 'market_tag_translations';

    protected $fillable = [
        'market_tag_id',
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

    /** Тег перевода */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(
            MarketTag::class,
            'market_tag_id'
        );
    }
}
