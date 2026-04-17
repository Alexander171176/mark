<?php

namespace App\Models\User\Like;

use App\Models\Admin\School\Lesson\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonLike extends Model
{
    use HasFactory;

    protected $table = 'lesson_likes';

    protected $fillable = [
        'user_id',
        'lesson_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'lesson_id' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
