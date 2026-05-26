<?php

namespace App\Models\Admin\School\Price;

use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\Course\SchoolCourse;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class SchoolCoursePrice extends Model
{
    use HasFactory;

    protected $table = 'school_course_prices';

    protected $fillable = [
        'school_course_id',
        'currency_id',
        'price',
        'sale_price',
        'compare_at_price',
        'starts_at',
        'ends_at',
        'activity',
        'sort',
        'meta',
    ];

    protected $casts = [
        'school_course_id' => 'integer',
        'currency_id' => 'integer',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'activity' => 'boolean',
        'sort' => 'integer',
        'meta' => 'array',
    ];

    protected $appends = [
        'effective_price',
        'has_discount',
        'discount_amount',
        'discount_percent',
    ];

    /* ======================== Relations ======================== */

    /** Курс */
    public function course(): BelongsTo
    {
        return $this->belongsTo(SchoolCourse::class, 'school_course_id');
    }

    /** Валюта */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /* ======================== Scopes ======================== */

    /** Только активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** По курсу */
    public function scopeForCourse(Builder $q, int $courseId): Builder
    {
        return $q->where('school_course_id', $courseId);
    }

    /** По валюте */
    public function scopeForCurrencyId(Builder $q, int $currencyId): Builder
    {
        return $q->where('currency_id', $currencyId);
    }

    /** По коду валюты */
    public function scopeForCurrencyCode(Builder $q, string $code): Builder
    {
        $code = strtoupper(trim($code));

        return $q->whereHas('currency', fn (Builder $cq) => $cq->where('code', $code));
    }

    /** Действующие по дате */
    public function scopeCurrent(Builder $q, CarbonInterface|string|null $at = null): Builder
    {
        $at = $at instanceof CarbonInterface
            ? $at
            : ($at ? Carbon::parse($at) : now());

        return $q
            ->where(fn (Builder $qq) => $qq->whereNull('starts_at')->orWhere('starts_at', '<=', $at))
            ->where(fn (Builder $qq) => $qq->whereNull('ends_at')->orWhere('ends_at', '>=', $at));
    }

    /** Активные и действующие */
    public function scopeActual(Builder $q, CarbonInterface|string|null $at = null): Builder
    {
        return $q->active()->current($at);
    }

    /** Сортировка */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    /* ======================== Accessors ======================== */

    /** Итоговая цена */
    public function getEffectivePriceAttribute(): string
    {
        $sale = $this->sale_price;

        if ($sale !== null && bccomp((string) $sale, '0', 2) === 1) {
            return (string) $sale;
        }

        return (string) $this->price;
    }

    /** Есть ли скидка */
    public function getHasDiscountAttribute(): bool
    {
        if ($this->compare_at_price === null) {
            return false;
        }

        return bccomp((string) $this->compare_at_price, (string) $this->effective_price, 2) === 1;
    }

    /** Сумма скидки */
    public function getDiscountAmountAttribute(): ?string
    {
        if (!$this->has_discount) {
            return null;
        }

        return bcsub((string) $this->compare_at_price, (string) $this->effective_price, 2);
    }

    /** Процент скидки */
    public function getDiscountPercentAttribute(): ?float
    {
        if (!$this->has_discount) {
            return null;
        }

        $compare = (string) $this->compare_at_price;

        if (bccomp($compare, '0', 2) !== 1) {
            return null;
        }

        $diff = bcsub($compare, (string) $this->effective_price, 6);
        $ratio = bcdiv($diff, $compare, 6);
        $percent = bcmul($ratio, '100', 4);

        return round((float) $percent, 2);
    }
}
