<?php

namespace App\Models\Admin\School\LearningCategory;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LearningCategoryImage extends BaseImage
{
    use HasFactory;

    protected $table = 'learning_category_images';

    /** Категории, к которым принадлежит это изображение. */
    public function categories(): BelongsToMany
    {
        // Параметры связи верны
        return $this->belongsToMany(
            LearningCategory::class,
            'learning_category_has_images',
            'image_id',
            'learning_category_id');
    }
}
