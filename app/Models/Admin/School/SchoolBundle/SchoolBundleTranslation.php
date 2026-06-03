<?php

namespace App\Models\Admin\School\SchoolBundle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolBundleTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_bundle_translations';

    protected $fillable = [
        'school_bundle_id',
        'locale',
        'title',
        'subtitle',
        'short',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    /** Набор курсов */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(SchoolBundle::class, 'school_bundle_id');
    }
}
