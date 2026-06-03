<?php

namespace App\Models\User\Like;

use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogArticleLike extends Model
{
    use HasFactory;

    protected $table = 'blog_article_likes';

    protected $fillable = [
        'user_id',
        'article_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'article_id' => 'integer',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(BlogArticle::class, 'article_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
