<?php

namespace App\Models\Admin\School\QaMessage;

use App\Models\Admin\School\QaThread\QaThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QaMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'qa_messages';

    protected $fillable = [
        'thread_id',     // FK -> qa_threads.id
        'user_id',       // FK -> users.id
        'parent_id',     // FK -> qa_messages.id (ответ на сообщение)
        'body',          // текст сообщения
        'is_private',    // приватность
        'is_pinned',     // закреплено
        'replies_count', // быстрый счётчик
        'edited_at',     // время последнего редактирования
        'meta',          // произвольные данные (JSON)
    ];

    protected $casts = [
        'is_private'    => 'bool',
        'is_pinned'     => 'bool',
        'replies_count' => 'int',
        'edited_at'     => 'datetime',
        'meta'          => 'array',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    /* ================= Связи ================= */

    // Тема, к которой относится сообщение
    public function thread(): BelongsTo
    {
        return $this->belongsTo(QaThread::class, 'thread_id');
    }

    // Автор
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Родительское сообщение
    public function parent(): BelongsTo
    {
        return $this->belongsTo(QaMessage::class, 'parent_id');
    }

    // Ответы на это сообщение
    public function replies(): HasMany
    {
        return $this->hasMany(QaMessage::class, 'parent_id');
    }

    /* ================= Скоупы ================= */

    public function scopePublic($q)
    {
        return $q->where('is_private', false);
    }

    public function scopePinned($q)
    {
        return $q->where('is_pinned', true);
    }

    public function scopeForThread($q, int $threadId)
    {
        return $q->where('thread_id', $threadId);
    }

    /* ================= Хелперы ================= */

    // Пометить как отредактированное
    public function markEdited(): void
    {
        $this->edited_at = now();
        $this->save();
    }
}
