<?php

namespace App\Models\Admin\School\SchoolPaymentMethod;

use App\Models\Admin\School\SchoolPayment\SchoolPayment;
use App\Models\Admin\School\SchoolUserPaymentMethod\SchoolUserPaymentMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolPaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'school_payment_methods';

    protected $fillable = [
        'code',
        'name',
        'provider',
        'type',
        'supports_refund',
        'supports_recurring',
        'activity',
        'sort',
        'meta',
    ];

    protected $casts = [
        'supports_refund' => 'boolean',
        'supports_recurring' => 'boolean',
        'activity' => 'boolean',
        'sort' => 'integer',
        'meta' => 'array',
    ];

    /** Сохранённые методы пользователей */
    public function userPaymentMethods(): HasMany
    {
        return $this->hasMany(SchoolUserPaymentMethod::class, 'school_payment_method_id');
    }

    /** Платежи */
    public function payments(): HasMany
    {
        return $this->hasMany(SchoolPayment::class, 'school_payment_method_id');
    }

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** По коду */
    public function scopeByCode(Builder $q, string $code): Builder
    {
        return $q->where('code', $code);
    }

    /** Сортировка */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }
}
