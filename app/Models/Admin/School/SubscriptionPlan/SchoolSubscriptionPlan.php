<?php

namespace App\Models\Admin\School\SubscriptionPlan;

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
