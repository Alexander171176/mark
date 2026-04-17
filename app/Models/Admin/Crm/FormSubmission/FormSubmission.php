<?php

namespace App\Models\Admin\Crm\FormSubmission;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSubmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'form_submissions';

    protected $fillable = [
        'user_id',       // FK -> users.id (nullable)
        'form_key',      // ключ формы (contact/demo_request/...)
        'name',          // имя отправителя
        'email',         // email отправителя
        'phone',         // телефон
        'message',       // сообщение
        'data',          // произвольные данные формы (JSON)
        'ip',            // IP адрес
        'user_agent',    // user-agent
        'referrer',      // реферер
        'page_url',      // страница отправки
        'locale',        // локаль
        'is_spam',       // пометка спама
        'is_read',       // прочитано
        'processed_at',  // обработано
        'notes',         // заметки менеджера
        'meta',          // доп. мета (JSON)
    ];

    protected $casts = [
        'data'         => 'array',
        'meta'         => 'array',
        'is_spam'      => 'bool',
        'is_read'      => 'bool',
        'processed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /* ================= Связи ================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ================= Скоупы ================= */

    // Непрочитанные и не спам
    public function scopeInbox($q)
    {
        return $q->where('is_spam', false)->where('is_read', false);
    }

    // По ключу формы
    public function scopeForm($q, string $formKey)
    {
        return $q->where('form_key', $formKey);
    }

    // За период
    public function scopeBetween($q, $from, $to)
    {
        return $q->whereBetween('created_at', [$from, $to]);
    }
}
