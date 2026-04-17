<?php

namespace App\Models\Admin\Constructor\Cms;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blog_posts';

    protected $fillable = [
        'author_id',        // FK -> users.id
        'title',            // заголовок
        'slug',             // ЧПУ
        'excerpt',          // краткое описание
        'content',          // контент
        'status',           // draft|published|archived
        'activity',        // активна ли запись
        'published_at',     // дата публикации
        'cover_image_url',  // обложка (если без медиабиблиотеки)
        'reading_time',     // время чтения (мин)
        'meta',             // произвольные данные (JSON)
        'locale',           // локаль
    ];

    protected $casts = [
        'activity'    => 'bool',
        'published_at' => 'datetime',
        'reading_time' => 'int',
        'meta'         => 'array',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /* ============== Связи ============== */

    // Автор записи
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /* ============== Скоупы ============== */

    // Опубликованные и активные
    public function scopePublished($q)
    {
        return $q->where('status', 'published')
            ->where('activity', true)
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeByLocale($q, ?string $locale)
    {
        return $locale ? $q->where('locale', $locale) : $q;
    }

    /* ============== Хелперы ============== */

    public function getUrlAttribute(): string
    {
        // Простой URL. Если есть префикс /blog или локализация в URL — поменяйте под роутинг.
        return '/blog/'.$this->slug;
    }
}
