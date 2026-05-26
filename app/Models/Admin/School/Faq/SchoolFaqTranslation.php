<?php

namespace App\Models\Admin\School\Faq;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolFaqTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_faq_translations';

    protected $fillable = [
        'school_faq_id',
        'locale',
        'question',
        'answer',
        'meta_title',
        'meta_description',
    ];

    /** FAQ */
    public function faq(): BelongsTo
    {
        return $this->belongsTo(SchoolFaq::class, 'school_faq_id');
    }
}
