<?php

namespace App\Models\Admin\Finance\CoursePrice;

use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\Course\Course;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Цена курса (мультивалютность/периоды/акции)
 *
 * @property int $id
 * @property int $course_id
 * @property int $currency_id
 * @property string $price
 * @property string|null $sale_price
 * @property string|null $compare_at_price
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property bool $activity
 * @property int $sort
 * @property array|null $meta
 *
 * @property-read string $effective_price
 * @property-read bool $has_discount
 * @property-read string|null $discount_amount
 * @property-read float|null $discount_percent
 */
class CoursePrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_prices';

    protected $fillable = [
        'course_id',
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
        'course_id'        => 'int',
        'currency_id'      => 'int',

        'price'            => 'decimal:2',
        'sale_price'       => 'decimal:2',
        'compare_at_price' => 'decimal:2',

        'starts_at'        => 'datetime',
        'ends_at'          => 'datetime',

        'activity'         => 'bool',
        'sort'             => 'int',
        'meta'             => 'array',

        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    /**
     * Чтобы computed-поля стабильно появлялись при ->toArray()
     * (ресурс ты и так отдаёшь, но это делает поведение единым).
     */
    protected $appends = [
        'effective_price',
        'has_discount',
        'discount_amount',
        'discount_percent',
    ];

    /* ======================== Relations ======================== */

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /* ======================== Scopes ======================== */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    public function scopeForCourse(Builder $q, int $courseId): Builder
    {
        return $q->where('course_id', $courseId);
    }

    public function scopeForCurrencyId(Builder $q, int $currencyId): Builder
    {
        return $q->where('currency_id', $currencyId);
    }

    public function scopeForCurrencyCode(Builder $q, string $code): Builder
    {
        $code = strtoupper(trim($code));

        return $q->whereHas('currency', fn (Builder $cq) => $cq->where('code', $code));
    }

    /**
     * Окно действия цены по датам.
     * starts_at: null или <= $at
     * ends_at:   null или >= $at
     */
    public function scopeCurrent(Builder $q, CarbonInterface|string|null $at = null): Builder
    {
        $at = $at instanceof CarbonInterface
            ? $at
            : ($at ? Carbon::parse($at) : now());

        return $q
            ->where(fn (Builder $qq) => $qq->whereNull('starts_at')->orWhere('starts_at', '<=', $at))
            ->where(fn (Builder $qq) => $qq->whereNull('ends_at')->orWhere('ends_at', '>=', $at));
    }

    /** Активные + в окне действия */
    public function scopeActual(Builder $q, CarbonInterface|string|null $at = null): Builder
    {
        return $q->active()->current($at);
    }

    /** sort ↑ затем id ↑ (как было у тебя) */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderBy('id');
    }

    /* ======================== Accessors ======================== */

    /**
     * Эффективная цена:
     * если sale_price задана и > 0, используем её, иначе price.
     */
    public function getEffectivePriceAttribute(): string
    {
        $sale = $this->sale_price; // string|null (из casts decimal:2)

        if ($sale !== null && bccomp((string) $sale, '0', 2) === 1) {
            return (string) $sale;
        }

        return (string) $this->price;
    }

    /**
     * Скидка есть, если compare_at_price > effective_price.
     */
    public function getHasDiscountAttribute(): bool
    {
        if ($this->compare_at_price === null) {
            return false;
        }

        return bccomp((string) $this->compare_at_price, (string) $this->effective_price, 2) === 1;
    }

    /**
     * Сумма скидки = compare_at_price - effective_price.
     */
    public function getDiscountAmountAttribute(): ?string
    {
        if (!$this->has_discount) {
            return null;
        }

        return bcsub((string) $this->compare_at_price, (string) $this->effective_price, 2);
    }

    /**
     * Процент скидки = (compare - effective) / compare * 100
     */
    public function getDiscountPercentAttribute(): ?float
    {
        if (!$this->has_discount) {
            return null;
        }

        $compare = (string) $this->compare_at_price;

        if (bccomp($compare, '0', 2) !== 1) {
            return null;
        }

        $diff    = bcsub($compare, (string) $this->effective_price, 6);
        $ratio   = bcdiv($diff, $compare, 6);
        $percent = bcmul($ratio, '100', 4);

        return round((float) $percent, 2);
    }
}
