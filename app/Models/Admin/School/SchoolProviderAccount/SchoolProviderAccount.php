<?php

namespace App\Models\Admin\School\SchoolProviderAccount;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolProviderAccount extends Model
{
    use HasFactory;

    protected $table = 'school_provider_accounts';

    protected $fillable = [
        'provider',
        'title',
        'mode',
        'account_id',
        'public_key',
        'secret_key',
        'webhook_secret',
        'supported_currencies',
        'supported_countries',
        'config',
        'activity',
        'is_default',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'secret_key' => 'encrypted',
        'webhook_secret' => 'encrypted',
        'supported_currencies' => 'array',
        'supported_countries' => 'array',
        'config' => 'array',
        'activity' => 'boolean',
        'is_default' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /** Кто создал */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Кто обновил */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /** Активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** По провайдеру и режиму */
    public function scopeFor(Builder $q, string $provider, string $mode = 'live'): Builder
    {
        return $q
            ->where('provider', $provider)
            ->where('mode', $mode);
    }

    /** По умолчанию */
    public function scopeDefault(Builder $q): Builder
    {
        return $q->where('is_default', true);
    }

    /** Название для отображения */
    public function getDisplayNameAttribute(): string
    {
        $label = $this->title ?: strtoupper($this->provider);

        return "{$label} ({$this->mode})";
    }
}
