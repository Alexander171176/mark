<?php

namespace App\Models\Admin\School\SchoolTrack;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolTrackTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_track_translations';

    protected $fillable = [
        'school_track_id',
        'locale',
        'name',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Трек */
    public function track(): BelongsTo
    {
        return $this->belongsTo(SchoolTrack::class, 'school_track_id');
    }
}
