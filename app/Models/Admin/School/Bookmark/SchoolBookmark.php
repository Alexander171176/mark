<?php

namespace App\Models\Admin\School\Bookmark;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SchoolBookmark extends Model
{
    use HasFactory;

    protected $table = 'school_bookmarks';

    protected $fillable = [
        'user_id',
        'bookmarkable_type',
        'bookmarkable_id',
        'is_favorite',
        'folder',
        'position',
        'note',
        'meta',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'bookmarkable_id' => 'integer',
        'is_favorite' => 'boolean',
        'position' => 'integer',
        'meta' => 'array',
    ];

    /** Пользователь */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Объект закладки */
    public function bookmarkable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Избранные */
    public function scopeFavorites(Builder $q): Builder
    {
        return $q->where('is_favorite', true);
    }

    /** По типу объекта */
    public function scopeOfType(Builder $q, string $class): Builder
    {
        return $q->where('bookmarkable_type', $class);
    }

    /** По папке */
    public function scopeInFolder(Builder $q, ?string $folder): Builder
    {
        return $folder
            ? $q->where('folder', $folder)
            : $q->whereNull('folder');
    }

    /** Для пользователя */
    public function scopeForUser(Builder $q, int $userId): Builder
    {
        return $q->where('user_id', $userId);
    }

    /** Сортировка */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('position')->orderByDesc('id');
    }
}
