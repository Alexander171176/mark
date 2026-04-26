<?php

namespace App\Models\Admin\Blog\BlogVideo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogVideoTranslation extends Model
{
    use HasFactory;

    protected $table = 'blog_video_translations';

    protected $fillable = [
        'video_id',
        'locale',
        'title',
        'short',
        'description',
        'pseudonym',
        'meta_title',
        'meta_keywords',
        'meta_desc',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** Основное видео */
    public function video(): BelongsTo
    {
        return $this->belongsTo(BlogVideo::class, 'video_id');
    }
}
