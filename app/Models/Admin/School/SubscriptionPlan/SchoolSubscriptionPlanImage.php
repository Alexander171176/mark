<?php

namespace App\Models\Admin\School\SubscriptionPlan;

use App\Models\Admin\Image\BaseImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolSubscriptionPlanImage extends BaseImage
{
    use HasFactory;

    protected $table = 'school_subscription_plan_images';

    /** Тарифы */
    public function subscriptionPlans(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolSubscriptionPlan::class,
            'school_subscription_plan_has_images',
            'image_id',
            'school_subscription_plan_id'
        )
            ->withPivot('order')
            ->orderBy('school_subscription_plan_has_images.order');
    }
}
