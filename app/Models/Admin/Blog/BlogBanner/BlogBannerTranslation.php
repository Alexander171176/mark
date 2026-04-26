<?php

namespace App\Models\Admin\Blog\BlogBanner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogBannerTranslation extends Model
{
    use HasFactory;

    protected $table = 'blog_banner_translations';

    protected $fillable = [
        'banner_id',
        'locale',
        'title',
        'link',
        'short',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /** Основной баннер */
    public function banner(): BelongsTo
    {
        return $this->belongsTo(BlogBanner::class, 'banner_id');
    }
}
