<?php

namespace App\Models\Admin\School\Quiz;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolQuizImage extends BaseImage
{
    use HasFactory;

    protected $table = 'school_quiz_images';

    /** Квизы, в которых используется изображение */
    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolQuiz::class,
            'school_quiz_has_images',
            'image_id',
            'school_quiz_id'
        )
            ->withPivot('order')
            ->orderBy('school_quiz_has_images.order', 'asc');
    }
}
