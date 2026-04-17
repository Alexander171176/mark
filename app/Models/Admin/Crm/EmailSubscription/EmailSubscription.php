<?php

namespace App\Models\Admin\Crm\EmailSubscription;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'email_subscriptions';

    protected $fillable = [
        'user_id',
        'email',
        'list',
        'status',            // pending|subscribed|unsubscribed|bounced|complained
        'confirm_token',
        'confirmed_at',
        'unsubscribed_at',
        'unsub_reason',
        'source',
        'locale',
        'ip',
        'user_agent',
        'provider',
        'provider_subscriber_id',
        'last_event',
        'tags',
        'meta',
    ];

    protected $casts = [
        'confirmed_at'    => 'datetime',
        'unsubscribed_at' => 'datetime',
        'tags'            => 'array',
        'meta'            => 'array',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    /* ============ Связи ============ */

    // Связанный пользователь (может отсутствовать для “гостевых” подписок)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ============ Скоупы ============ */

    // Только активные подписчики (подтверждённые и не отписанные)
    public function scopeSubscribed($q)
    {
        return $q->where('status', 'subscribed');
    }

    // По конкретному списку/каналу
    public function scopeForList($q, string $list)
    {
        return $q->where('list', $list);
    }

    // Требуют подтверждения
    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }
}
