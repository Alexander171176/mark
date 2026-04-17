<?php

namespace App\Models\Admin\School\Quiz;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QuizImage extends BaseImage
{
    use HasFactory;

    protected $table = 'quiz_images';

    /** Квизы, в которых используется это изображение */
    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(
            Quiz::class,
            'quiz_has_images',
            'image_id',   // FK на текущую модель (quiz_images.id)
            'quiz_id'     // FK на quizzes.id
        )
            ->withPivot('order')
            ->orderBy('quiz_has_images.order', 'asc');
    }
}
