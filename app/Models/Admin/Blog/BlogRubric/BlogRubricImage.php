<?php

namespace App\Models\Admin\Blog\BlogRubric;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogRubricImage extends BaseImage
{
    use HasFactory;

    protected $table = 'blog_rubric_images';

    /**
     * Рубрики, в которых используется это изображение
     */
    public function rubrics(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogRubric::class,
            'blog_rubric_has_images',
            'image_id',
            'rubric_id'
        )
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
