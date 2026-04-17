<?php

namespace App\Models\Admin\School\Bookmark;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bookmark extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bookmarks';

    protected $fillable = [
        'user_id',           // FK -> users.id
        'bookmarkable_type', // тип модели (Course, Lesson, ...)
        'bookmarkable_id',   // ID модели
        'is_favorite',       // признак "в избранном"
        'folder',            // имя пользовательской папки
        'position',          // позиция сортировки
        'note',              // заметка пользователя
        'meta',              // произвольные данные (JSON)
    ];

    protected $casts = [
        'is_favorite' => 'bool',
        'position'    => 'int',
        'meta'        => 'array',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    /* ================= Связи ================= */

    // Пользователь-владелец закладки
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Объект, на который стоит закладка (курс, урок, пост и т.д.)
    public function bookmarkable(): MorphTo
    {
        return $this->morphTo();
    }

    /* ================= Скоупы ================= */

    // Только избранные
    public function scopeFavorites($q)
    {
        return $q->where('is_favorite', true);
    }

    // По типу целевой модели
    public function scopeOfType($q, string $class)
    {
        return $q->where('bookmarkable_type', $class);
    }

    // По папке
    public function scopeInFolder($q, ?string $folder)
    {
        return $folder ? $q->where('folder', $folder) : $q->whereNull('folder');
    }

    // Для конкретного пользователя
    public function scopeForUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }
}
