<?php

namespace App\Models\Admin\Finance\ProviderAccount;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'provider_accounts';

    protected $fillable = [
        'provider',              // код провайдера: stripe|paypal|yookassa|...
        'title',                 // человеко-читаемое имя
        'mode',                  // test|live
        'account_id',            // внешний ID аккаунта у провайдера
        'public_key',            // публичный ключ/ID клиента
        'secret_key',            // секретный ключ (шифруется)
        'webhook_secret',        // секрет вебхуков (шифруется)
        'supported_currencies',  // массив валют
        'supported_countries',   // массив стран
        'config',                // произвольные настройки (JSON)
        'activity',              // активна ли учётка
        'is_default',            // использовать по умолчанию
        'created_by',            // FK -> users.id
        'updated_by',            // FK -> users.id
    ];

    protected $casts = [
        // Денежных чисел нет; ключи/секреты шифруем на уровне кастов
        'secret_key'         => 'encrypted',
        'webhook_secret'     => 'encrypted',
        'supported_currencies'=> 'array',
        'supported_countries' => 'array',
        'config'             => 'array',
        'activity'           => 'bool',
        'is_default'         => 'bool',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
        'deleted_at'         => 'datetime',
    ];

    /* ================= Связи ================= */

    // Кто создал / обновил (необязательные поля)
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /* ================= Скоупы ================= */

    // Активные
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    // По провайдеру и режиму
    public function scopeFor($q, string $provider, string $mode = 'live')
    {
        return $q->where(compact('provider', 'mode'));
    }

    // Учётка по умолчанию
    public function scopeDefault($q)
    {
        return $q->where('is_default', true);
    }

    /* ================= Хелперы ================= */

    public function getDisplayNameAttribute(): string
    {
        // Удобное отображение в админке/логах
        $label = $this->title ?: strtoupper($this->provider);
        return "{$label} ({$this->mode})";
    }
}
