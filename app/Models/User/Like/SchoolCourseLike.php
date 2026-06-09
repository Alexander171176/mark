<?php

namespace App\Models\User\Like;

use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolCourseLike extends Model
{
    use HasFactory;

    protected $table = 'school_course_likes';

    protected $fillable = [
        'user_id',
        'school_course_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'school_course_id' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
