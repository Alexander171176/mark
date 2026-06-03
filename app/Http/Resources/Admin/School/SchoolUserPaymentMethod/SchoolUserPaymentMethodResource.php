<?php

namespace App\Http\Resources\Admin\School\SchoolUserPaymentMethod;

use App\Http\Resources\Admin\School\SchoolPaymentMethod\SchoolPaymentMethodResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolUserPaymentMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'user_id' => $this->user_id,
            'school_payment_method_id' => $this->school_payment_method_id,

            'provider' => $this->provider,
            'provider_customer_id' => $this->provider_customer_id,
            'provider_payment_method_id' => $this->provider_payment_method_id,

            'brand' => $this->brand,
            'last4' => $this->last4,
            'exp_month' => $this->exp_month,
            'exp_year' => $this->exp_year,
            'country' => $this->country,

            'billing_name' => $this->billing_name,
            'billing_email' => $this->billing_email,
            'billing_phone' => $this->billing_phone,
            'billing_address' => $this->billing_address,

            'is_default' => (bool) $this->is_default,
            'activity' => (bool) $this->activity,
            'meta' => $this->meta,

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),

            'payment_method' => new SchoolPaymentMethodResource(
                $this->whenLoaded('paymentMethod')
            ),

            'payments_count' => $this->when(
                isset($this->payments_count),
                fn () => (int) $this->payments_count
            ),

            'subscriptions_count' => $this->when(
                isset($this->subscriptions_count),
                fn () => (int) $this->subscriptions_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
