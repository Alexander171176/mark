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

    /** Сортировка по параметру */
    public function scopeSortByParam(Builder $q, ?string $sort, ?string $locale = null): Builder
    {
        $locale = $locale ?: app()->getLocale();

        return match ($sort) {
            'idAsc' => $q->orderBy('id', 'asc'),
            'idDesc' => $q->orderBy('id', 'desc'),

            'sortAsc' => $q->orderBy('sort', 'asc')->orderByDesc('id'),
            'sortDesc' => $q->orderBy('sort', 'desc')->orderByDesc('id'),

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

            'priceAsc' => $q->orderBy('price', 'asc')->orderByDesc('id'),
            'priceDesc' => $q->orderBy('price', 'desc')->orderByDesc('id'),

            'trialDaysAsc' => $q->orderBy('trial_days', 'asc')->orderByDesc('id'),
            'trialDaysDesc' => $q->orderBy('trial_days', 'desc')->orderByDesc('id'),

            'publishedAtAsc' => $q->orderBy('published_at', 'asc')->orderByDesc('id'),
            'publishedAtDesc' => $q->orderBy('published_at', 'desc')->orderByDesc('id'),

            'availableFromAsc' => $q->orderBy('available_from', 'asc')->orderByDesc('id'),
            'availableFromDesc' => $q->orderBy('available_from', 'desc')->orderByDesc('id'),

            'availableUntilAsc' => $q->orderBy('available_until', 'asc')->orderByDesc('id'),
            'availableUntilDesc' => $q->orderBy('available_until', 'desc')->orderByDesc('id'),

            'activityAsc' => $q->orderBy('activity', 'asc')->orderByDesc('id'),
            'activityDesc' => $q->orderBy('activity', 'desc')->orderByDesc('id'),
            'activity' => $q->where('activity', true)->orderByDesc('id'),
            'inactive' => $q->where('activity', false)->orderByDesc('id'),

            'autoRenewAsc' => $q->orderBy('auto_renew', 'asc')->orderByDesc('id'),
            'autoRenewDesc' => $q->orderBy('auto_renew', 'desc')->orderByDesc('id'),

            'imagesAsc' => $q->withCount('images')->orderBy('images_count', 'asc')->orderByDesc('id'),
            'imagesDesc' => $q->withCount('images')->orderBy('images_count', 'desc')->orderByDesc('id'),

            'createdAtAsc' => $q->orderBy('created_at', 'asc')->orderByDesc('id'),
            'createdAtDesc' => $q->orderBy('created_at', 'desc')->orderByDesc('id'),

            'updatedAtAsc' => $q->orderBy('updated_at', 'asc')->orderByDesc('id'),
            'updatedAtDesc' => $q->orderBy('updated_at', 'desc')->orderByDesc('id'),

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
