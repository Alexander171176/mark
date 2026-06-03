<?php

namespace App\Models\Admin\School\SchoolFaqCategory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolFaqCategoryTranslation extends Model
{
    use HasFactory;

    protected $table = 'school_faq_category_translations';

    protected $fillable = [
        'school_faq_category_id',
        'locale',
        'title',
        'description',
    ];

    /** Категория FAQ */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SchoolFaqCategory::class, 'school_faq_category_id');
    }
}
