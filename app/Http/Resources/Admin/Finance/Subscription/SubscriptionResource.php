<?php

namespace App\Http\Resources\Admin\Finance\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Представление подписки для админ-API.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        $now = now();

        $isTrialing = $this->status === 'trialing'
            || ($this->trial_ends_at && $this->trial_ends_at->gt($now) && $this->status === 'active');

        $activityNow = in_array($this->status, ['active','trialing'], true)
            && (!$this->ends_at || $this->ends_at->gt($now));

        return [
            'id'                     => $this->id,
            'user_id'                => $this->user_id,
            'subscription_plan_id'   => $this->subscription_plan_id,
            'order_id'               => $this->order_id,
            'user_payment_method_id' => $this->user_payment_method_id,

            'currency'        => $this->currency,
            'price'           => $this->price,
            'billing_period'  => $this->billing_period,  // day|week|month|year
            'interval'        => $this->interval,

            'trial_days'   => $this->trial_days,
            'trial_ends_at'=> optional($this->trial_ends_at)?->toISOString(),

            'current_period_start' => optional($this->current_period_start)?->toISOString(),
            'current_period_end'   => optional($this->current_period_end)?->toISOString(),

            'started_at'    => optional($this->started_at)?->toISOString(),
            'ends_at'       => optional($this->ends_at)?->toISOString(),
            'cancelled_at'  => optional($this->cancelled_at)?->toISOString(),
            'cancel_at_period_end' => (bool) $this->cancel_at_period_end,

            'status' => $this->status, // trialing|active|past_due|paused|cancelled|expired|incomplete|pending

            'provider'                 => $this->provider,
            'provider_subscription_id' => $this->provider_subscription_id,
            'last_paid_at'             => optional($this->last_paid_at)?->toISOString(),
            'next_billing_at'          => optional($this->next_billing_at)?->toISOString(),
            'renewal_attempts'         => $this->renewal_attempts,

            'features' => $this->features,
            'limits'   => $this->limits,
            'meta'     => $this->meta,

            // Удобные вычисляемые поля
            'is_trialing'        => $isTrialing,
            'activity_now'      => $activityNow,
            'days_left'          => $this->ends_at ? $now->diffInDays($this->ends_at, false) : null,
            'seconds_to_renewal' => $this->next_billing_at ? $now->diffInSeconds($this->next_billing_at, false) : null,

            // Короткие сведения о плане/методе оплаты (когда загружены)
            'plan' => $this->whenLoaded('plan', fn () => [
                'id'            => $this->plan->id,
                'name'          => $this->plan->name,
                'slug'          => $this->plan->slug,
                'billing_period'=> $this->plan->billing_period,
                'interval'      => $this->plan->interval,
                'price'         => $this->plan->price,
                'currency'      => $this->plan->currency,
            ]),
            'user_payment_method' => $this->whenLoaded('userPaymentMethod', fn () => [
                'id'       => $this->userPaymentMethod->id,
                'provider' => $this->userPaymentMethod->provider,
                'brand'    => $this->userPaymentMethod->brand,
                'last4'    => $this->userPaymentMethod->last4,
                'exp_month'=> $this->userPaymentMethod->exp_month,
                'exp_year' => $this->userPaymentMethod->exp_year,
            ]),

            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
            'deleted_at' => optional($this->deleted_at)?->toISOString(),
        ];
    }
}
