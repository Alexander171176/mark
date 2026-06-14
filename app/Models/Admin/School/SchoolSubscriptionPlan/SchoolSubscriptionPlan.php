<?php

namespace App\Models\Admin\School\SchoolSubscriptionPlan;

use App\Models\Admin\Finance\Currency\Currency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SchoolSubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'school_subscription_plans';

    protected $fillable = [
        'sort',
        'activity',
        'slug',

        'published_at',
        'available_from',
        'available_until',

        'billing_period',
        'interval',
        'currency_id',
        'price',
        'trial_days',
        'auto_renew',

        'provider',
        'provider_ref',
        'provider_payload',

        'config',
    ];

    protected $casts = [
        'sort' => 'integer',
        'activity' => 'boolean',

        'published_at' => 'datetime',
        'available_from' => 'datetime',
        'available_until' => 'datetime',

        'price' => 'decimal:2',
        'interval' => 'integer',
        'currency_id' => 'integer',
        'trial_days' => 'integer',
        'auto_renew' => 'boolean',

        'provider_payload' => 'array',
        'config' => 'array',
    ];

    /* ======================== Relations ======================== */

    /** Валюта */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /** Переводы */
    public function translations(): HasMany
    {
        return $this->hasMany(SchoolSubscriptionPlanTranslation::class);
    }

    /** Текущий перевод */
    public function translation(): HasOne
    {
        return $this->hasOne(SchoolSubscriptionPlanTranslation::class, 'school_subscription_plan_id')
            ->where('locale', app()->getLocale());
    }

    /** Изображения */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolSubscriptionPlanImage::class,
            'school_subscription_plan_has_images',
            'school_subscription_plan_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('school_subscription_plan_has_images.order');
    }

    /* ======================== Scopes ======================== */

    /** Активные */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('activity', true);
    }

    /** Сортировка */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort')->orderByDesc('id');
    }

    /** Доступные по времени */
    public function scopeAvailable(Builder $q): Builder
    {
        return $q
            ->whereNotNull('published_at')
            ->where(fn ($qq) => $qq->whereNull('available_from')->orWhere('available_from', '<=', now()))
            ->where(fn ($qq) => $qq->whereNull('available_until')->orWhere('available_until', '>=', now()));
    }

    /** Для витрины */
    public function scopeForPublic(Builder $q): Builder
    {
        return $q->active()->available();
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
                        ->where('school_subscription_plans.slug', 'like', "%{$word}%")
                        ->orWhere('school_subscription_plans.billing_period', 'like', "%{$word}%")
                        ->orWhere('school_subscription_plans.provider', 'like', "%{$word}%")
                        ->orWhere('school_subscription_plans.provider_ref', 'like', "%{$word}%")
                        ->orWhere('school_subscription_plans.price', 'like', "%{$word}%")
                        ->orWhereHas('currency', function (Builder $qq) use ($word) {
                            $qq->where('code', 'like', "%{$word}%")
                                ->orWhere('name', 'like', "%{$word}%")
                                ->orWhere('symbol', 'like', "%{$word}%");
                        })
                        ->orWhereHas('translations', function (Builder $qq) use ($word, $locale) {
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
            'idAsc' => $q->orderBy('school_subscription_plans.id', 'asc'),
            'idDesc' => $q->orderBy('school_subscription_plans.id', 'desc'),

            'sortAsc' => $q->orderBy('sort', 'asc')->orderByDesc('school_subscription_plans.id'),
            'sortDesc' => $q->orderBy('sort', 'desc')->orderByDesc('school_subscription_plans.id'),

            'titleAsc' => $q
                ->leftJoin('school_subscription_plan_translations as sspt_sort', function ($join) use ($locale) {
                    $join->on('sspt_sort.school_subscription_plan_id', '=', 'school_subscription_plans.id')
                        ->where('sspt_sort.locale', '=', $locale);
                })
                ->orderBy('sspt_sort.title', 'asc')
                ->orderByDesc('school_subscription_plans.id')
                ->select('school_subscription_plans.*'),

            'titleDesc' => $q
                ->leftJoin('school_subscription_plan_translations as sspt_sort', function ($join) use ($locale) {
                    $join->on('sspt_sort.school_subscription_plan_id', '=', 'school_subscription_plans.id')
                        ->where('sspt_sort.locale', '=', $locale);
                })
                ->orderBy('sspt_sort.title', 'desc')
                ->orderByDesc('school_subscription_plans.id')
                ->select('school_subscription_plans.*'),

            'publishedAtAsc' => $q->orderBy('published_at', 'asc')->orderByDesc('school_subscription_plans.id'),
            'publishedAtDesc' => $q->orderBy('published_at', 'desc')->orderByDesc('school_subscription_plans.id'),

            'availabilityNowFirst' => $q
                ->orderByRaw("
                CASE
                    WHEN published_at IS NOT NULL
                     AND (available_from IS NULL OR available_from <= NOW())
                     AND (available_until IS NULL OR available_until >= NOW())
                    THEN 0
                    ELSE 1
                END
            ")
                ->orderByDesc('school_subscription_plans.id'),

            'availableFromAsc' => $q->orderBy('available_from', 'asc')->orderByDesc('school_subscription_plans.id'),
            'availableFromDesc' => $q->orderBy('available_from', 'desc')->orderByDesc('school_subscription_plans.id'),

            'availableUntilAsc' => $q->orderBy('available_until', 'asc')->orderByDesc('school_subscription_plans.id'),
            'availableUntilDesc' => $q->orderBy('available_until', 'desc')->orderByDesc('school_subscription_plans.id'),

            'priceAsc' => $q->orderBy('price', 'asc')->orderByDesc('school_subscription_plans.id'),
            'priceDesc' => $q->orderBy('price', 'desc')->orderByDesc('school_subscription_plans.id'),

            'trialDaysAsc' => $q->orderBy('trial_days', 'asc')->orderByDesc('school_subscription_plans.id'),
            'trialDaysDesc' => $q->orderBy('trial_days', 'desc')->orderByDesc('school_subscription_plans.id'),

            'billingPeriodAsc' => $q->orderBy('billing_period', 'asc')->orderByDesc('school_subscription_plans.id'),
            'billingPeriodDesc' => $q->orderBy('billing_period', 'desc')->orderByDesc('school_subscription_plans.id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('school_subscription_plans.id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('school_subscription_plans.id'),
            'activity' => $q->where('activity', true)->orderByDesc('school_subscription_plans.id'),
            'inactive' => $q->where('activity', false)->orderByDesc('school_subscription_plans.id'),

            'autoRenewAsc' => $q->orderBy('auto_renew', 'asc')->orderByDesc('school_subscription_plans.id'),
            'autoRenewDesc' => $q->orderBy('auto_renew', 'desc')->orderByDesc('school_subscription_plans.id'),

            'imagesAsc' => $q->withCount('images')->orderBy('images_count', 'asc')->orderByDesc('school_subscription_plans.id'),
            'imagesDesc' => $q->withCount('images')->orderBy('images_count', 'desc')->orderByDesc('school_subscription_plans.id'),

            'createdAtAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('school_subscription_plans.id'),
            'createdAtDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('school_subscription_plans.id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('school_subscription_plans.id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('school_subscription_plans.id'),

            default => $q->ordered(),
        };
    }

    /* ======================== Accessors ======================== */

    /** Главное изображение */
    public function getPrimaryImageAttribute(): ?SchoolSubscriptionPlanImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images->first();
        }

        return $this->images()->first();
    }
}
