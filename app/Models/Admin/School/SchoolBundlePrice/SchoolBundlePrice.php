<?php

namespace App\Models\Admin\School\SchoolBundlePrice;

use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\School\SchoolBundle\SchoolBundle;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class SchoolBundlePrice extends Model
{
    use HasFactory;

    protected $table = 'school_bundle_prices';

    protected $fillable = [
        'school_bundle_id',
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
        'school_bundle_id' => 'integer',
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

    /** Набор курсов */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(SchoolBundle::class, 'school_bundle_id');
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

    /** По набору */
    public function scopeForBundle(Builder $q, int $bundleId): Builder
    {
        return $q->where('school_bundle_id', $bundleId);
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

    /** Поиск */
    public function scopeSearch(Builder $q, ?string $term, ?string $locale = null): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $q;
        }

        $locale = $locale ?: app()->getLocale();

        $words = collect(preg_split('/[\s:#№,"\'«»(){}\[\].!?\/\\\\|;+=*&^%$@<>`~_-]+/u', $term))
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => mb_strlen($word) >= 2)
            ->values();

        if ($words->isEmpty()) {
            return $q;
        }

        return $q->where(function (Builder $query) use ($words, $locale) {
            foreach ($words as $word) {
                $query->where(function (Builder $query) use ($word, $locale) {
                    $query
                        ->where('school_bundle_prices.price', 'like', "%{$word}%")
                        ->orWhere('school_bundle_prices.sale_price', 'like', "%{$word}%")
                        ->orWhere('school_bundle_prices.compare_at_price', 'like', "%{$word}%")
                        ->orWhereHas('currency', function (Builder $qq) use ($word) {
                            $qq->where('code', 'like', "%{$word}%")
                                ->orWhere('name', 'like', "%{$word}%")
                                ->orWhere('symbol', 'like', "%{$word}%");
                        })
                        ->orWhereHas('bundle.translations', function (Builder $qq) use ($word, $locale) {
                            $qq->where('locale', $locale)
                                ->where(function (Builder $sub) use ($word) {
                                    $sub->where('title', 'like', "%{$word}%")
                                        ->orWhere('subtitle', 'like', "%{$word}%")
                                        ->orWhere('short', 'like', "%{$word}%")
                                        ->orWhere('description', 'like', "%{$word}%");
                                });
                        });
                });
            }
        });
    }

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $q->orderBy('school_bundle_prices.id', 'asc'),
            'idDesc' => $q->orderBy('school_bundle_prices.id', 'desc'),

            'sortAsc' => $q->orderBy('sort', 'asc')->orderByDesc('school_bundle_prices.id'),
            'sortDesc' => $q->orderBy('sort', 'desc')->orderByDesc('school_bundle_prices.id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('school_bundle_prices.id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('school_bundle_prices.id'),
            'activity' => $q->where('activity', true)->orderByDesc('school_bundle_prices.id'),
            'inactive' => $q->where('activity', false)->orderByDesc('school_bundle_prices.id'),

            'bundleTitleAsc' => $q
                ->leftJoin('school_bundle_translations as sbt_sort', function ($join) use ($locale) {
                    $join->on('sbt_sort.school_bundle_id', '=', 'school_bundle_prices.school_bundle_id')
                        ->where('sbt_sort.locale', '=', $locale);
                })
                ->orderBy('sbt_sort.title', 'asc')
                ->orderByDesc('school_bundle_prices.id')
                ->addSelect('school_bundle_prices.*'),

            'bundleTitleDesc' => $q
                ->leftJoin('school_bundle_translations as sbt_sort', function ($join) use ($locale) {
                    $join->on('sbt_sort.school_bundle_id', '=', 'school_bundle_prices.school_bundle_id')
                        ->where('sbt_sort.locale', '=', $locale);
                })
                ->orderBy('sbt_sort.title', 'desc')
                ->orderByDesc('school_bundle_prices.id')
                ->addSelect('school_bundle_prices.*'),

            'currencyCodeAsc' => $q
                ->leftJoin('currencies as c_sort', 'c_sort.id', '=', 'school_bundle_prices.currency_id')
                ->orderBy('c_sort.code', 'asc')
                ->orderByDesc('school_bundle_prices.id')
                ->addSelect('school_bundle_prices.*'),

            'currencyCodeDesc' => $q
                ->leftJoin('currencies as c_sort', 'c_sort.id', '=', 'school_bundle_prices.currency_id')
                ->orderBy('c_sort.code', 'desc')
                ->orderByDesc('school_bundle_prices.id')
                ->addSelect('school_bundle_prices.*'),

            'effectivePriceAsc' => $q
                ->orderByRaw('COALESCE(NULLIF(sale_price, 0), price) asc')
                ->orderByDesc('school_bundle_prices.id'),

            'effectivePriceDesc' => $q
                ->orderByRaw('COALESCE(NULLIF(sale_price, 0), price) desc')
                ->orderByDesc('school_bundle_prices.id'),

            'priceAsc' => $q->orderBy('price', 'asc')->orderByDesc('school_bundle_prices.id'),
            'priceDesc' => $q->orderBy('price', 'desc')->orderByDesc('school_bundle_prices.id'),

            'salePriceAsc' => $q->orderBy('sale_price', 'asc')->orderByDesc('school_bundle_prices.id'),
            'salePriceDesc' => $q->orderBy('sale_price', 'desc')->orderByDesc('school_bundle_prices.id'),

            'compareAtPriceAsc' => $q->orderBy('compare_at_price', 'asc')->orderByDesc('school_bundle_prices.id'),
            'compareAtPriceDesc' => $q->orderBy('compare_at_price', 'desc')->orderByDesc('school_bundle_prices.id'),

            'discountPercentAsc' => $q
                ->orderByRaw("
                CASE
                    WHEN compare_at_price IS NOT NULL
                     AND compare_at_price > 0
                     AND compare_at_price > COALESCE(NULLIF(sale_price, 0), price)
                    THEN ((compare_at_price - COALESCE(NULLIF(sale_price, 0), price)) / compare_at_price) * 100
                    ELSE 0
                END asc
            ")
                ->orderByDesc('school_bundle_prices.id'),

            'discountPercentDesc' => $q
                ->orderByRaw("
                CASE
                    WHEN compare_at_price IS NOT NULL
                     AND compare_at_price > 0
                     AND compare_at_price > COALESCE(NULLIF(sale_price, 0), price)
                    THEN ((compare_at_price - COALESCE(NULLIF(sale_price, 0), price)) / compare_at_price) * 100
                    ELSE 0
                END desc
            ")
                ->orderByDesc('school_bundle_prices.id'),

            'startsAtAsc' => $q->orderBy('starts_at', 'asc')->orderByDesc('school_bundle_prices.id'),
            'startsAtDesc' => $q->orderBy('starts_at', 'desc')->orderByDesc('school_bundle_prices.id'),

            'endsAtAsc' => $q->orderBy('ends_at', 'asc')->orderByDesc('school_bundle_prices.id'),
            'endsAtDesc' => $q->orderBy('ends_at', 'desc')->orderByDesc('school_bundle_prices.id'),

            'createdAtAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('school_bundle_prices.id'),
            'createdAtDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('school_bundle_prices.id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('school_bundle_prices.id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('school_bundle_prices.id'),

            default => $q->ordered(),
        };
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
