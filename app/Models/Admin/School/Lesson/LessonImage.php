<?php

namespace App\Models\Admin\School\Lesson;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LessonImage extends BaseImage
{
    use HasFactory;

    protected $table = 'lesson_images';

    /** Уроки, в которых используется это изображение */
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(
            Lesson::class,
            'lesson_has_images',
            'image_id',   // FK на текущую модель (lesson_images.id)
            'lesson_id'   // FK на lessons.id
        )
            ->withPivot('order')
            ->orderBy('lesson_has_images.order', 'asc');
    }
}
