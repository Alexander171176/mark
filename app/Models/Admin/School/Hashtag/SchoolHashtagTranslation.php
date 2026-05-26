<?php

namespace App\Models\Admin\School\Hashtag;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolHashtagTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_hashtag_translations';

    protected $fillable = [
        'school_hashtag_id',
        'locale',
        'name',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Связь с хештегом */
    public function hashtag(): BelongsTo
    {
        return $this->belongsTo(SchoolHashtag::class, 'school_hashtag_id');
    }
}
