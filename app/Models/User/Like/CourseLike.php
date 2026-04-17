<?php

namespace App\Models\User\Like;

use App\Models\Admin\School\Course\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseLike extends Model
{
    use HasFactory;

    protected $table = 'course_likes';

    protected $fillable = [
        'user_id',
        'course_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'course_id' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
