<?php

namespace App\Http\Resources\Admin\School\Subscription;

use App\Http\Resources\Admin\School\Order\SchoolOrderSharedResource;
use App\Http\Resources\Admin\School\SubscriptionPlan\SchoolSubscriptionPlanSharedResource;
use App\Http\Resources\Admin\School\UserPaymentMethod\SchoolUserPaymentMethodResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolSubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $now = now();

        return [
            'id' => $this->id,

            'user_id' => $this->user_id,
            'school_subscription_plan_id' => $this->school_subscription_plan_id,
            'school_order_id' => $this->school_order_id,
            'school_user_payment_method_id' => $this->school_user_payment_method_id,

            'currency' => $this->currency,
            'price' => (string) $this->price,
            'billing_period' => $this->billing_period,
            'interval' => (int) $this->interval,

            'trial_days' => (int) $this->trial_days,
            'trial_ends_at' => optional($this->trial_ends_at)->toIso8601String(),

            'current_period_start' => optional($this->current_period_start)->toIso8601String(),
            'current_period_end' => optional($this->current_period_end)->toIso8601String(),

            'started_at' => optional($this->started_at)->toIso8601String(),
            'ends_at' => optional($this->ends_at)->toIso8601String(),
            'cancelled_at' => optional($this->cancelled_at)->toIso8601String(),
            'cancel_at_period_end' => (bool) $this->cancel_at_period_end,

            'status' => $this->status,

            'provider' => $this->provider,
            'provider_subscription_id' => $this->provider_subscription_id,
            'last_paid_at' => optional($this->last_paid_at)->toIso8601String(),
            'next_billing_at' => optional($this->next_billing_at)->toIso8601String(),
            'renewal_attempts' => (int) $this->renewal_attempts,

            'features' => $this->features,
            'limits' => $this->limits,
            'meta' => $this->meta,

            'is_active' => (bool) $this->is_active,
            'is_trialing' => (bool) $this->is_trialing,
            'is_expired' => (bool) $this->is_expired,
            'days_left' => $this->ends_at ? $now->diffInDays($this->ends_at, false) : null,
            'seconds_to_renewal' => $this->next_billing_at ? $now->diffInSeconds($this->next_billing_at, false) : null,

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),

            'plan' => new SchoolSubscriptionPlanSharedResource(
                $this->whenLoaded('plan')
            ),

            'order' => new SchoolOrderSharedResource(
                $this->whenLoaded('order')
            ),

            'user_payment_method' => new SchoolUserPaymentMethodResource(
                $this->whenLoaded('userPaymentMethod')
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
