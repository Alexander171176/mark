<?php

namespace App\Models\User\Like;

use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogVideoLike extends Model
{
    use HasFactory;

    protected $table = 'blog_video_likes';

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
        return $this->belongsTo(BlogVideo::class, 'video_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
