<?php

namespace App\Models\User\Like;

use App\Models\Admin\Blog\Video\Video;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoLike extends Model
{
    use HasFactory;

    protected $table = 'video_likes';

    protected $fillable = [
        'user_id',
        'video_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'video_id' => 'integer',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
