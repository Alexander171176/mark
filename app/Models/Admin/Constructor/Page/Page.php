<?php

namespace App\Models\Admin\Constructor\Page;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pages';

    protected $fillable = [
        'parent_id',            // FK -> pages.id (родитель)
        'author_id',            // FK -> users.id (автор)
        'title',                // заголовок
        'slug',                 // ЧПУ
        'excerpt',              // краткое описание
        'content',              // основной контент
        'status',               // draft|published|archived
        'activity',            // активна ли страница
        'published_at',         // дата публикации
        'template',             // шаблон вывода
        'layout',               // макет
        'locale',               // локаль (ru, en, ...)
        'sort',             // сортировка среди siblings
        'meta',                 // произвольные данные (JSON)
    ];

    protected $casts = [
        'activity'     => 'bool',
        'sort'         => 'int',
        'meta'         => 'array',
        'published_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /* ================= Связи ================= */

    // Родительская страница
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    // Дочерние страницы
    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('sort');
    }

    // Автор страницы
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /* ================= Скоупы ================= */

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

    /* ================= Хелперы ================= */

    public function getUrlAttribute(): string
    {
        // Простейший вариант URL. Если есть дерево/локаль/префиксы —
        // скорректируйте под свой роутинг.
        return '/'.$this->slug;
    }
}
