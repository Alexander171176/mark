<?php

namespace App\Models\Admin\School\QaThread;

use App\Models\Admin\School\QaMessage\QaMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QaThread extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'qa_threads';

    protected $fillable = [
        'user_id',           // автор темы
        'threadable_type',   // morph type целевой сущности
        'threadable_id',     // morph id целевой сущности
        'title',             // заголовок
        'body',              // стартовый текст
        'status',            // open|closed|archived
        'is_locked',         // запрет новых сообщений
        'is_pinned',         // закреплено
        'replies_count',     // кол-во ответов
        'last_reply_at',     // время последнего ответа
        'last_activity_at',  // последняя активность
        'meta',              // произвольные данные (JSON)
    ];

    protected $casts = [
        'is_locked'        => 'bool',
        'is_pinned'        => 'bool',
        'replies_count'    => 'int',
        'last_reply_at'    => 'datetime',
        'last_activity_at' => 'datetime',
        'meta'             => 'array',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    /* ================= Связи ================= */

    // Автор
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // К какой сущности относится тема
    public function threadable(): MorphTo
    {
        return $this->morphTo();
    }

    // Сообщения внутри темы (будем делать модель QaMessage)
    public function messages(): HasMany
    {
        return $this->hasMany(QaMessage::class, 'thread_id');
    }

    /* ================= Скоупы ================= */

    public function scopeOpen($q)
    {
        return $q->where('status', 'open')->where('is_locked', false);
    }

    public function scopePinned($q)
    {
        return $q->where('is_pinned', true);
    }

    public function scopeRecent($q)
    {
        return $q->orderByDesc('last_activity_at')->orderByDesc('id');
    }

    /* ================= Хелперы ================= */

    public function close(): void
    {
        $this->status = 'closed';
        $this->is_locked = true;
        $this->save();
    }

    public function reopen(): void
    {
        $this->status = 'open';
        $this->is_locked = false;
        $this->save();
    }

    public function pin(): void
    {
        $this->is_pinned = true;
        $this->save();
    }

    public function unpin(): void
    {
        $this->is_pinned = false;
        $this->save();
    }
}
