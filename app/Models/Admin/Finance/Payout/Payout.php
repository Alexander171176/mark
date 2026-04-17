<?php

namespace App\Models\Admin\Finance\Payout;

use App\Models\Admin\Finance\PayoutItem\PayoutItem;
use App\Models\Admin\Finance\ProviderAccount\ProviderAccount;
use App\Models\Admin\School\InstructorProfile\InstructorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payout extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payouts';

    protected $fillable = [
        'instructor_profile_id', // FK -> instructor_profiles.id
        'provider_account_id',   // FK -> provider_accounts.id (nullable)
        'number',                // номер выплаты
        'period_start',          // начало периода
        'period_end',            // конец периода
        'currency',              // ISO 4217
        'amount_gross',          // начислено брутто
        'fee_total',             // комиссии
        'tax_total',             // налоги/удержания
        'amount_net',            // к выплате (нетто)
        'status',                // pending|processing|paid|failed|cancelled
        'method',                // способ выплаты
        'paid_at',               // когда выплачено
        'notes',                 // заметки
        'meta',                  // произвольные данные (JSON)
        'created_by',            // FK -> users.id
        'updated_by',            // FK -> users.id
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'amount_gross' => 'decimal:2',
        'fee_total'    => 'decimal:2',
        'tax_total'    => 'decimal:2',
        'amount_net'   => 'decimal:2',
        'meta'         => 'array',
        'paid_at'      => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /* =============== Связи =============== */

    // Получатель выплаты
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class, 'instructor_profile_id');
    }

    // Аккаунт платёжного провайдера (если использовался)
    public function providerAccount(): BelongsTo
    {
        return $this->belongsTo(ProviderAccount::class, 'provider_account_id');
    }

    // Позиции/расшифровка выплаты (например, по заказам/курсам)
    public function items(): HasMany
    {
        return $this->hasMany(PayoutItem::class);
    }

    // Аудит
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /* =============== Скоупы =============== */

    public function scopePaid($q)       { return $q->where('status', 'paid'); }
    public function scopePending($q)    { return $q->where('status', 'pending'); }
    public function scopeProcessing($q) { return $q->where('status', 'processing'); }

    public function scopeForPeriod($q, $from, $to)
    {
        return $q->where(function ($w) use ($from, $to) {
            $w->whereNull('period_start')->orWhere('period_start', '>=', $from);
        })->where(function ($w) use ($to) {
            $w->whereNull('period_end')->orWhere('period_end', '<=', $to);
        });
    }

    /* =============== Хелперы =============== */

    // Пересчёт нетто по полям (если нужно получить вычисляемое значение)
    public function getComputedNetAttribute(): string
    {
        return (string) ($this->amount_gross - $this->fee_total - $this->tax_total);
    }

    public function getDisplayNameAttribute(): string
    {
        $who = $this->instructor?->public_name ?? 'Инструктор';
        return "{$this->number} • {$who}";
    }
}
