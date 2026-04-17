<?php

namespace App\Models\Admin\Blog\Rubric;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RubricImage extends BaseImage
{
    use HasFactory;

    protected $table = 'rubric_images';

    /** Рубрики, в которых используется это изображение */
    public function rubrics(): BelongsToMany
    {
        return $this->belongsToMany(
            Rubric::class,
            'rubric_has_images',
            'image_id',   // FK на rubric_images.id
            'rubric_id'   // FK на rubrics.id
        )
            ->withPivot('order')
            ->orderBy('pivot_order'); // сортировка внутри рубрики (rubric_has_images.order)
    }
}
