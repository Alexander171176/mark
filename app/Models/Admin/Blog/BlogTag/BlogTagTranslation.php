<?php

namespace App\Models\Admin\Blog\BlogTag;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogTagTranslation extends Model
{
    use HasFactory;

    protected $table = 'blog_tag_translations';

    protected $fillable = [
        'tag_id',
        'locale',
        'name',
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

    /** Основной тег */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(BlogTag::class, 'tag_id');
    }
}
