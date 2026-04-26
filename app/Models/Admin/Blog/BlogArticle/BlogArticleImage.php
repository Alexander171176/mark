<?php

namespace App\Models\Admin\Blog\BlogArticle;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogArticleImage extends BaseImage
{
    use HasFactory;

    protected $table = 'blog_article_images';

    /**
     * Статьи, в которых используется это изображение
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogArticle::class,
            'blog_article_has_images',
            'image_id',
            'article_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
