<?php

namespace App\Http\Resources\Admin\School\SchoolSubscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolSubscriptionSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'school_subscription_plan_id' => $this->school_subscription_plan_id,
            'school_order_id' => $this->school_order_id,

            'status' => $this->status,
            'currency' => $this->currency,
            'price' => (string) $this->price,

            'billing_period' => $this->billing_period,
            'interval' => (int) $this->interval,

            'is_active' => (bool) $this->is_active,
            'is_trialing' => (bool) $this->is_trialing,
            'is_expired' => (bool) $this->is_expired,

            'started_at' => optional($this->started_at)->toIso8601String(),
            'ends_at' => optional($this->ends_at)->toIso8601String(),
            'next_billing_at' => optional($this->next_billing_at)->toIso8601String(),
        ];
    }
}
