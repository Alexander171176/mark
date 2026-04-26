<?php

namespace App\Models\Admin\Blog\BlogArticle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogArticleTranslation extends Model
{
    use HasFactory;

    protected $table = 'blog_article_translations';

    protected $fillable = [
        'article_id',
        'locale',
        'title',
        'subtitle',
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

    public function article(): BelongsTo
    {
        return $this->belongsTo(BlogArticle::class, 'article_id');
    }
}
