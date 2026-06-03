<?php

namespace App\Models\Admin\School\SchoolPayout;

use App\Models\Admin\School\SchoolPayoutItem\SchoolPayoutItem;
use App\Models\Admin\School\SchoolProviderAccount\SchoolProviderAccount;
use App\Models\Admin\School\SchoolInstructorProfile\SchoolInstructorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolPayout extends Model
{
    use HasFactory;

    protected $table = 'school_payouts';

    protected $fillable = [
        'school_instructor_profile_id',
        'school_provider_account_id',
        'number',
        'period_start',
        'period_end',
        'currency',
        'amount_gross',
        'fee_total',
        'tax_total',
        'amount_net',
        'status',
        'method',
        'paid_at',
        'notes',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'school_instructor_profile_id' => 'integer',
        'school_provider_account_id' => 'integer',
        'period_start' => 'date',
        'period_end' => 'date',
        'amount_gross' => 'decimal:2',
        'fee_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'amount_net' => 'decimal:2',
        'paid_at' => 'datetime',
        'meta' => 'array',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /** Преподаватель */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(SchoolInstructorProfile::class, 'school_instructor_profile_id');
    }

    /** Аккаунт провайдера */
    public function providerAccount(): BelongsTo
    {
        return $this->belongsTo(SchoolProviderAccount::class, 'school_provider_account_id');
    }

    /** Позиции выплаты */
    public function items(): HasMany
    {
        return $this->hasMany(SchoolPayoutItem::class, 'school_payout_id');
    }

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

    /** Оплаченные */
    public function scopePaid(Builder $q): Builder
    {
        return $q->where('status', 'paid');
    }

    /** Ожидающие */
    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', 'pending');
    }

    /** В обработке */
    public function scopeProcessing(Builder $q): Builder
    {
        return $q->where('status', 'processing');
    }

    /** За период */
    public function scopeForPeriod(Builder $q, $from, $to): Builder
    {
        return $q
            ->where(function (Builder $query) use ($from) {
                $query->whereNull('period_start')
                    ->orWhere('period_start', '>=', $from);
            })
            ->where(function (Builder $query) use ($to) {
                $query->whereNull('period_end')
                    ->orWhere('period_end', '<=', $to);
            });
    }

    /** Вычисленное нетто */
    public function getComputedNetAttribute(): string
    {
        return (string) ($this->amount_gross - $this->fee_total - $this->tax_total);
    }

    /** Название для отображения */
    public function getDisplayNameAttribute(): string
    {
        $who = $this->instructor?->translation?->title
            ?? $this->instructor?->user?->name
            ?? 'Инструктор';

        return "{$this->number} • {$who}";
    }
}
