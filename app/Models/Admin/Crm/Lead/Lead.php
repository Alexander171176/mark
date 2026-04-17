<?php

namespace App\Models\Admin\Crm\Lead;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leads';

    protected $fillable = [
        // Контакты
        'name', 'email', 'phone',
        // Сообщение и источник
        'message', 'source', 'page_url', 'referrer',
        // UTM
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
        // Тех.инфо
        'ip', 'user_agent', 'consent',
        // Статус/обработка
        'status', 'processed_at', 'manager_id',
        // Примечания/метаданные
        'notes', 'meta',
    ];

    protected $casts = [
        'consent'      => 'bool',
        'processed_at' => 'datetime',
        'meta'         => 'array',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /* ========= Связи ========= */

    // Назначенный менеджер (пользователь системы)
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /* ========= Скоупы ========= */

    // Только новые/необработанные лиды
    public function scopeNew($q)
    {
        return $q->where('status', 'new');
    }

    // Лиды по UTM-кампании
    public function scopeCampaign($q, string $campaign)
    {
        return $q->where('utm_campaign', $campaign);
    }

    // Лиды, по которым уже была работа
    public function scopeProcessed($q)
    {
        return $q->whereNotNull('processed_at');
    }
}
