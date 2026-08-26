<?php

namespace App\Models\Admin\School\SchoolOrder;

use App\Models\Admin\School\SchoolCourse\SchoolCourse;
use App\Models\Admin\School\SchoolCourseSchedule\SchoolCourseSchedule;
use App\Models\Admin\School\SchoolEnrollment\SchoolEnrollment;
use App\Models\Admin\School\SchoolInvoice\SchoolInvoice;
use App\Models\Admin\School\SchoolOrderItem\SchoolOrderItem;
use App\Models\Admin\School\SchoolPayment\SchoolPayment;
use App\Models\Admin\School\SchoolRefund\SchoolRefund;
use App\Models\Admin\School\SchoolSubscription\SchoolSubscription;
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
        return $this->belongsTo(
            SchoolCourse::class,
            'school_course_id'
        );
    }

    /** Поток курса */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(
            SchoolCourseSchedule::class,
            'school_course_schedule_id'
        );
    }

    /** Позиции заказа */
    public function orderItems(): HasMany
    {
        return $this->hasMany(
            SchoolOrderItem::class,
            'school_order_id'
        );
    }

    /** Платежи */
    public function payments(): HasMany
    {
        return $this->hasMany(
            SchoolPayment::class,
            'school_order_id'
        );
    }

    /** Возвраты */
    public function refunds(): HasMany
    {
        return $this->hasMany(
            SchoolRefund::class,
            'school_order_id'
        );
    }

    /** Инвойсы */
    public function invoices(): HasMany
    {
        return $this->hasMany(
            SchoolInvoice::class,
            'school_order_id'
        );
    }

    /** Зачисления */
    public function enrollments(): HasMany
    {
        return $this->hasMany(
            SchoolEnrollment::class,
            'school_order_id'
        );
    }

    /** Подписки */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(
            SchoolSubscription::class,
            'school_order_id'
        );
    }

    /* ======================== Scopes ======================== */

    /** Оплаченные */
    public function scopePaid(Builder $q): Builder
    {
        return $q->where(
            function (Builder $query) {
                $query
                    ->where(
                        'is_paid',
                        true
                    )
                    ->orWhere(
                        'payment_status',
                        'paid'
                    )
                    ->orWhere(
                        'payment_status',
                        'succeeded'
                    );
            }
        );
    }

    /** Открытые */
    public function scopeOpen(Builder $q): Builder
    {
        return $q->whereIn(
            'status',
            [
                'new',
                'processing',
            ]
        );
    }

    /** С итоговой суммой */
    public function scopeWithTotal(Builder $q): Builder
    {
        return $q->where(
            'total',
            '>',
            0
        );
    }

    /**
     * Поиск.
     *
     * Должен соответствовать frontend-поиску Index.
     *
     * Ищем:
     * - по самому Order;
     * - по User;
     * - по переводу Course текущей locale;
     * - по переводу Schedule текущей locale.
     */
    public function scopeSearch(
        Builder $q,
        ?string $term,
        ?string $locale = null
    ): Builder {
        $term = trim(
            (string) $term
        );

        if ($term === '') {
            return $q;
        }

        $locale = $locale
            ?: app()->getLocale();

        $words = collect(
            preg_split(
                '/[\s:#№,"\'«»(){}\[\].!?\/\\\\|;+=*&^%$@<>`~_-]+/u',
                $term
            )
        )
            ->map(
                fn ($word) =>
                trim($word)
            )
            ->filter(
                fn ($word) =>
                    mb_strlen($word) >= 2
            )
            ->values();

        if ($words->isEmpty()) {
            return $q;
        }

        return $q->where(
            function (Builder $query) use (
                $words,
                $locale
            ) {
                foreach ($words as $word) {
                    $query->where(
                        function (Builder $query) use (
                            $word,
                            $locale
                        ) {
                            $query
                                /**
                                 * Основные поля Order.
                                 */
                                ->where(
                                    'school_orders.id',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.number',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.buyer_name',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.buyer_email',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.buyer_phone',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.status',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.payment_status',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.payment_method',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.payment_provider',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.payment_reference',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.external_id',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.client_ip',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.currency',
                                    'like',
                                    "%{$word}%"
                                )
                                ->orWhere(
                                    'school_orders.total',
                                    'like',
                                    "%{$word}%"
                                )

                                /**
                                 * Пользователь.
                                 *
                                 * User не переводимый.
                                 */
                                ->orWhereHas(
                                    'user',
                                    function (Builder $qq) use ($word) {
                                        $qq
                                            ->where(
                                                'name',
                                                'like',
                                                "%{$word}%"
                                            )
                                            ->orWhere(
                                                'email',
                                                'like',
                                                "%{$word}%"
                                            );
                                    }
                                )

                                /**
                                 * Курс.
                                 *
                                 * Только перевод текущей locale.
                                 */
                                ->orWhereHas(
                                    'course.translations',
                                    function (Builder $qq) use (
                                        $word,
                                        $locale
                                    ) {
                                        $qq
                                            ->where(
                                                'locale',
                                                $locale
                                            )
                                            ->where(
                                                function (Builder $sub) use ($word) {
                                                    $sub
                                                        ->where(
                                                            'title',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'subtitle',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'short',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'description',
                                                            'like',
                                                            "%{$word}%"
                                                        );
                                                }
                                            );
                                    }
                                )

                                /**
                                 * Поток / расписание курса.
                                 *
                                 * Только перевод текущей locale.
                                 */
                                ->orWhereHas(
                                    'schedule.translations',
                                    function (Builder $qq) use (
                                        $word,
                                        $locale
                                    ) {
                                        $qq
                                            ->where(
                                                'locale',
                                                $locale
                                            )
                                            ->where(
                                                function (Builder $sub) use ($word) {
                                                    $sub
                                                        ->where(
                                                            'title',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'short',
                                                            'like',
                                                            "%{$word}%"
                                                        )
                                                        ->orWhere(
                                                            'description',
                                                            'like',
                                                            "%{$word}%"
                                                        );
                                                }
                                            );
                                    }
                                );
                        }
                    );
                }
            }
        );
    }

    /** Сортировка */
    public function scopeSortByParam(
        Builder $q,
        ?string $sort
    ): Builder {
        return match ($sort) {
            'idAsc' =>
            $q->orderBy(
                'school_orders.id',
                'asc'
            ),

            'idDesc' =>
            $q->orderBy(
                'school_orders.id',
                'desc'
            ),

            'numberAsc' =>
            $q
                ->orderBy(
                    'number',
                    'asc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'numberDesc' =>
            $q
                ->orderBy(
                    'number',
                    'desc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'createdAsc',
            'date_asc' =>
            $q
                ->orderBy(
                    'created_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'createdDesc',
            'date_desc' =>
            $q
                ->orderBy(
                    'created_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'totalAsc',
            'total_asc' =>
            $q
                ->orderBy(
                    'total',
                    'asc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'totalDesc',
            'total_desc' =>
            $q
                ->orderBy(
                    'total',
                    'desc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'paidAtAsc',
            'paid_asc' =>
            $q
                ->orderBy(
                    'paid_at',
                    'asc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'paidAtDesc',
            'paid_desc' =>
            $q
                ->orderBy(
                    'paid_at',
                    'desc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'buyerAsc' =>
            $q
                ->leftJoin(
                    'users as u_sort',
                    'u_sort.id',
                    '=',
                    'school_orders.user_id'
                )
                ->orderByRaw(
                    'COALESCE(school_orders.buyer_name, u_sort.name) asc'
                )
                ->orderByDesc(
                    'school_orders.id'
                )
                ->addSelect(
                    'school_orders.*'
                ),

            'buyerDesc' =>
            $q
                ->leftJoin(
                    'users as u_sort',
                    'u_sort.id',
                    '=',
                    'school_orders.user_id'
                )
                ->orderByRaw(
                    'COALESCE(school_orders.buyer_name, u_sort.name) desc'
                )
                ->orderByDesc(
                    'school_orders.id'
                )
                ->addSelect(
                    'school_orders.*'
                ),

            'statusAsc' =>
            $q
                ->orderBy(
                    'status',
                    'asc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'statusDesc' =>
            $q
                ->orderBy(
                    'status',
                    'desc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'paymentStatusAsc' =>
            $q
                ->orderBy(
                    'payment_status',
                    'asc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'paymentStatusDesc' =>
            $q
                ->orderBy(
                    'payment_status',
                    'desc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'paidFirst' =>
            $q
                ->orderBy(
                    'is_paid',
                    'desc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            'paidLast' =>
            $q
                ->orderBy(
                    'is_paid',
                    'asc'
                )
                ->orderByDesc(
                    'school_orders.id'
                ),

            default =>
            $q->orderByDesc(
                'school_orders.id'
            ),
        };
    }
}
