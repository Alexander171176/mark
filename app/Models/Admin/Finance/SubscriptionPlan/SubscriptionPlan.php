<?php

namespace App\Models\Admin\Finance\SubscriptionPlan;

use App\Models\Admin\Finance\Currency\Currency;
use App\Models\Admin\Finance\Subscription\Subscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subscription_plans';

    /**
     * Массово заполняемые поля (под новую структуру таблицы).
     */
    protected $fillable = [
        // управление
        'sort',
        'activity',

        // витрина / локаль
        'locale',
        'title',
        'slug',
        'subtitle',
        'short',
        'description',

        // SEO
        'meta_title',
        'meta_keywords',
        'meta_desc',

        // публикация / доступность
        'published_at',
        'available_from',
        'available_until',

        // биллинг
        'billing_period',
        'interval',
        'currency_id',
        'price',
        'trial_days',
        'auto_renew',

        // провайдер оплаты
        'provider',
        'provider_ref',
        'provider_payload',

        // конфиг (features/limits/meta и прочее)
        'config',
    ];

    /**
     * Приведение типов (casts).
     */
    protected $casts = [
        'sort'              => 'int',
        'activity'          => 'bool',

        'published_at'      => 'datetime',
        'available_from'    => 'datetime',
        'available_until'   => 'datetime',

        'price'             => 'decimal:2',
        'interval'          => 'int',
        'currency_id'       => 'int',
        'trial_days'        => 'int',
        'auto_renew'        => 'bool',

        'provider_payload'  => 'array',
        'config'            => 'array',

        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    /* ===================== Связи ===================== */

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * Подписки пользователей на этот план.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'subscription_plan_id');
    }

    /** Изображения тарифного плана (через pivot subscription_plan_has_images) */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(
            SubscriptionPlanImage::class,
            'subscription_plan_has_images',
            'subscription_plan_id',
            'image_id'
        )
            ->withPivot('order')
            ->orderBy('subscription_plan_has_images.order', 'asc')
            ->orderBy('subscription_plan_has_images.image_id', 'desc'); // стабилизация
    }

    /* ===================== Скоупы ===================== */

    /**
     * Только активные планы (доступны к покупке).
     */
    public function scopeActive($q)
    {
        return $q->where('activity', true);
    }

    /**
     * Планы заданной локали (ru/kk/en...).
     */
    public function scopeLocale($q, string $locale)
    {
        return $q->where('locale', $locale);
    }

    /**
     * Планы заданного периода (например, все monthly / yearly).
     */
    public function scopePeriod($q, string $period, ?int $interval = null)
    {
        $q->where('billing_period', $period);

        if (!is_null($interval)) {
            $q->where('interval', $interval);
        }

        return $q;
    }

    /**
     * Планы, опубликованные и находящиеся в окне доступности на витрине.
     * Важно: это “витринная доступность”, а не “activity”.
     */
    public function scopePublicAvailable($q)
    {
        return $q
            ->whereNotNull('published_at')
            ->where(function ($qq) {
                $qq->whereNull('available_from')
                    ->orWhere('available_from', '<=', now());
            })
            ->where(function ($qq) {
                $qq->whereNull('available_until')
                    ->orWhere('available_until', '>=', now());
            });
    }

    /**
     * Комбинированный скоуп для витрины: активный + доступный по окну публикации.
     */
    public function scopeForShowcase($q)
    {
        return $q->active()->publicAvailable();
    }

    /** Главное изображение (с наименьшим order) */
    public function getPrimaryImageAttribute(): ?SubscriptionPlanImage
    {
        if ($this->relationLoaded('images')) {
            return $this->images->first();
        }

        return $this->images()->first(); // orderBy уже есть в relation
    }
}
