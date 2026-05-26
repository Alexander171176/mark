<?php

namespace App\Models\Admin\School\Order;

use App\Models\Admin\School\Course\SchoolCourse;
use App\Models\Admin\School\CourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\Enrollment\SchoolEnrollment;
use App\Models\Admin\School\Invoice\SchoolInvoice;
use App\Models\Admin\School\Payment\SchoolPayment;
use App\Models\Admin\School\Refund\SchoolRefund;
use App\Models\Admin\School\Subscription\SchoolSubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolOrder extends Model
{
    use HasFactory;

    protected $table = 'school_orders';

    protected $fillable = [
        'user_id',
        'school_course_id',
        'school_course_schedule_id',
        'number',

        'buyer_name',
        'buyer_email',
        'buyer_phone',

        'billing_company',
        'billing_tax_id',
        'billing_address',

        'is_paid',
        'paid_at',
        'payment_method_id',
        'payment_method',
        'payment_provider',
        'payment_reference',
        'confirmation_code',
        'confirmation_status',
        'failure_reason',

        'currency',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',

        'status',
        'payment_status',

        'items',
        'meta',

        'user_comment',
        'manager_comment',

        'external_id',
        'exported_at',

        'client_ip',
        'user_agent',
        'public_hash',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'school_course_id' => 'integer',
        'school_course_schedule_id' => 'integer',
        'payment_method_id' => 'integer',

        'is_paid' => 'boolean',

        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',

        'items' => 'array',
        'meta' => 'array',

        'paid_at' => 'datetime',
        'exported_at' => 'datetime',
    ];

    /* ======================== Relations ======================== */

    /** Пользователь */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Курс */
    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    /** Поток курса */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(SchoolCourseSchedule::class, 'school_course_schedule_id');
    }

    /** Позиции заказа */
    public function orderItems(): HasMany
    {
        return $this->hasMany(SchoolOrderItem::class, 'school_order_id');
    }

    /** Платежи */
    public function payments(): HasMany
    {
        return $this->hasMany(SchoolPayment::class, 'school_order_id');
    }

    /** Возвраты */
    public function refunds(): HasMany
    {
        return $this->hasMany(SchoolRefund::class, 'school_order_id');
    }

    /** Инвойсы */
    public function invoices(): HasMany
    {
        return $this->hasMany(SchoolInvoice::class, 'school_order_id');
    }

    /** Зачисления */
    public function enrollments(): HasMany
    {
        return $this->hasMany(SchoolEnrollment::class, 'school_order_id');
    }

    /** Подписки */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(SchoolSubscription::class, 'school_order_id');
    }

    /* ======================== Scopes ======================== */

    /** Оплаченные */
    public function scopePaid(Builder $q): Builder
    {
        return $q->where(function (Builder $query) {
            $query->where('is_paid', true)
                ->orWhere('payment_status', 'paid')
                ->orWhere('payment_status', 'succeeded');
        });
    }

    /** Открытые */
    public function scopeOpen(Builder $q): Builder
    {
        return $q->whereIn('status', ['new', 'processing']);
    }

    /** С итоговой суммой */
    public function scopeWithTotal(Builder $q): Builder
    {
        return $q->where('total', '>', 0);
    }

    /** Поиск */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!$term) {
            return $q;
        }

        return $q->where(function (Builder $query) use ($term) {
            $query->where('number', 'like', "%{$term}%")
                ->orWhere('buyer_name', 'like', "%{$term}%")
                ->orWhere('buyer_email', 'like', "%{$term}%")
                ->orWhere('buyer_phone', 'like', "%{$term}%")
                ->orWhere('payment_reference', 'like', "%{$term}%")
                ->orWhere('external_id', 'like', "%{$term}%");
        });
    }

    /** Сортировка */
    public function scopeSortByParam(Builder $q, ?string $sort): Builder
    {
        return match ($sort) {
            'date_asc'   => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'date_desc'  => $q->orderBy('created_at', 'desc')->orderByDesc('id'),
            'total_asc'  => $q->orderBy('total', 'asc')->orderByDesc('id'),
            'total_desc' => $q->orderBy('total', 'desc')->orderByDesc('id'),
            'paid_asc'   => $q->orderBy('paid_at', 'asc')->orderByDesc('id'),
            'paid_desc'  => $q->orderBy('paid_at', 'desc')->orderByDesc('id'),
            default      => $q->orderByDesc('id'),
        };
    }
}
