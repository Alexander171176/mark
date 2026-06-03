<?php

namespace App\Models\Admin\School\SchoolQaThread;

use App\Models\Admin\School\SchoolQaMessage\SchoolQaMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SchoolQaThread extends Model
{
    use HasFactory;

    protected $table = 'school_qa_threads';

    protected $fillable = [
        'user_id',
        'threadable_type',
        'threadable_id',
        'title',
        'body',
        'status',
        'is_locked',
        'is_pinned',
        'replies_count',
        'last_reply_at',
        'last_activity_at',
        'meta',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'threadable_id' => 'integer',
        'is_locked' => 'boolean',
        'is_pinned' => 'boolean',
        'replies_count' => 'integer',
        'last_reply_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'meta' => 'array',
    ];

    /** Автор */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Целевая сущность */
    public function threadable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Сообщения */
    public function messages(): HasMany
    {
        return $this->hasMany(SchoolQaMessage::class, 'thread_id');
    }

    /** Открытые */
    public function scopeOpen(Builder $q): Builder
    {
        return $q
            ->where('status', 'open')
            ->where('is_locked', false);
    }

    /** Закреплённые */
    public function scopePinned(Builder $q): Builder
    {
        return $q->where('is_pinned', true);
    }

    /** Недавние */
    public function scopeRecent(Builder $q): Builder
    {
        return $q->orderByDesc('last_activity_at')->orderByDesc('id');
    }

    /** Закрыть тему */
    public function close(): void
    {
        $this->status = 'closed';
        $this->is_locked = true;
        $this->save();
    }

    /** Открыть тему */
    public function reopen(): void
    {
        $this->status = 'open';
        $this->is_locked = false;
        $this->save();
    }

    /** Закрепить */
    public function pin(): void
    {
        $this->is_pinned = true;
        $this->save();
    }

    /** Открепить */
    public function unpin(): void
    {
        $this->is_pinned = false;
        $this->save();
    }
}
