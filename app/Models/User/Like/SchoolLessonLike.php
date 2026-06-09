<?php

namespace App\Models\User\Like;

use App\Models\Admin\School\SchoolLesson\SchoolLesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolLessonLike extends Model
{
    use HasFactory;

    protected $table = 'school_lesson_likes';

    protected $fillable = [
        'user_id',
        'school_lesson_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'school_lesson_id' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(SchoolLesson::class, 'school_lesson_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
