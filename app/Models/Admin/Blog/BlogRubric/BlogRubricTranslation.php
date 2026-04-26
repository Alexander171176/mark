<?php

namespace App\Models\Admin\Blog\BlogRubric;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogRubricTranslation extends Model
{
    use HasFactory;

    protected $table = 'blog_rubric_translations';

    protected $fillable = [
        'rubric_id',
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

    /** Основная рубрика */
    public function rubric(): BelongsTo
    {
        return $this->belongsTo(BlogRubric::class, 'rubric_id');
    }
}
