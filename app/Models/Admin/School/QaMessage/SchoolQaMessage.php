<?php

namespace App\Models\Admin\School\QaMessage;

use App\Models\Admin\School\QaThread\SchoolQaThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolQaMessage extends Model
{
    use HasFactory;

    protected $table = 'school_qa_messages';

    protected $fillable = [
        'thread_id',
        'user_id',
        'parent_id',
        'body',
        'is_private',
        'is_pinned',
        'replies_count',
        'edited_at',
        'meta',
    ];

    protected $casts = [
        'thread_id' => 'integer',
        'user_id' => 'integer',
        'parent_id' => 'integer',
        'is_private' => 'boolean',
        'is_pinned' => 'boolean',
        'replies_count' => 'integer',
        'edited_at' => 'datetime',
        'meta' => 'array',
    ];

    /** Тема */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(SchoolQaThread::class, 'thread_id');
    }

    /** Автор */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Родительское сообщение */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SchoolQaMessage::class, 'parent_id');
    }

    /** Ответы */
    public function replies(): HasMany
    {
        return $this->hasMany(SchoolQaMessage::class, 'parent_id');
    }

    /** Публичные */
    public function scopePublic(Builder $q): Builder
    {
        return $q->where('is_private', false);
    }

    /** Закреплённые */
    public function scopePinned(Builder $q): Builder
    {
        return $q->where('is_pinned', true);
    }

    /** По теме */
    public function scopeForThread(Builder $q, int $threadId): Builder
    {
        return $q->where('thread_id', $threadId);
    }

    /** Пометить отредактированным */
    public function markEdited(): void
    {
        $this->edited_at = now();
        $this->save();
    }
}
