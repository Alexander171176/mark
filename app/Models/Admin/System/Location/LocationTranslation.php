<?php

namespace App\Models\Admin\System\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationTranslation extends Model
{
    use HasFactory;

    /**
     * Массово заполняемые поля.
     */
    protected $fillable = [
        'location_id',
        'locale',
        'title',
        'title_in',
        'title_from',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /**
     * Локация.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(
            Location::class
        );
    }
}
