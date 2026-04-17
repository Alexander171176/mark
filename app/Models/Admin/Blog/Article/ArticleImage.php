<?php

namespace App\Models\Admin\Blog\Article;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ArticleImage extends BaseImage
{
    use HasFactory;

    protected $table = 'article_images';

    /** Статьи, в которых используется это изображение */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            Article::class,
            'article_has_images',
            'image_id',   // FK на article_images.id
            'article_id'  // FK на articles.id
        )
            ->withPivot('order')
            ->orderBy('pivot_order');
    }
}
