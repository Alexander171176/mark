<?php

namespace App\Models\Admin\Finance\SubscriptionPlan;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriptionPlanImage extends BaseImage
{
    use HasFactory;

    protected $table = 'subscription_plan_images';

    /** Тарифные планы, в которых используется это изображение */
    public function subscriptionPlans(): BelongsToMany
    {
        return $this->belongsToMany(
            SubscriptionPlan::class,
            'subscription_plan_has_images',
            'image_id',    // FK на текущую модель (subscription_plan_images.id)
            'subscription_plan_id')   // FK на subscription_plans.id
        ->withPivot('order')
            ->orderBy('subscription_plan_has_images.order', 'asc');
    }
}
