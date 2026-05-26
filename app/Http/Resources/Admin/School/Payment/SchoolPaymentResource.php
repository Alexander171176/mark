<?php

namespace App\Http\Resources\Admin\School\Payment;

use App\Http\Resources\Admin\School\Order\SchoolOrderSharedResource;
use App\Http\Resources\Admin\School\PaymentMethod\SchoolPaymentMethodResource;
use App\Http\Resources\Admin\School\UserPaymentMethod\SchoolUserPaymentMethodResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolPaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $refundedAmount = $this->refunded_amount !== null ? (float) $this->refunded_amount : 0;
        $amount = (float) $this->amount;

        return [
            'id' => $this->id,

            'school_order_id' => $this->school_order_id,
            'school_payment_method_id' => $this->school_payment_method_id,
            'school_user_payment_method_id' => $this->school_user_payment_method_id,

            'provider' => $this->provider,
            'provider_payment_id' => $this->provider_payment_id,
            'idempotency_key' => $this->idempotency_key,

            'status' => $this->status,
            'currency' => $this->currency,
            'amount' => (string) $this->amount,

            'captured_at' => optional($this->captured_at)->toIso8601String(),
            'refunded_at' => optional($this->refunded_at)->toIso8601String(),
            'refunded_amount' => $this->refunded_amount !== null ? (string) $this->refunded_amount : null,

            'error_code' => $this->error_code,
            'error_message' => $this->error_message,
            'meta' => $this->meta,

            'is_refunded' => $this->status === 'refunded',
            'is_partially_refunded' => $this->status === 'partially_refunded',
            'refundable_amount' => (string) max(0, $amount - $refundedAmount),

            'order' => new SchoolOrderSharedResource(
                $this->whenLoaded('order')
            ),

            'payment_method' => new SchoolPaymentMethodResource(
                $this->whenLoaded('paymentMethod')
            ),

            'user_payment_method' => new SchoolUserPaymentMethodResource(
                $this->whenLoaded('userPaymentMethod')
            ),

            'refunds' => $this->whenLoaded('refunds', fn () => $this->refunds->map(fn ($refund) => [
                'id' => $refund->id,
                'school_order_id' => $refund->school_order_id,
                'school_payment_id' => $refund->school_payment_id,
                'provider' => $refund->provider,
                'provider_refund_id' => $refund->provider_refund_id,
                'status' => $refund->status,
                'currency' => $refund->currency,
                'amount' => (string) $refund->amount,
                'reason' => $refund->reason,
                'requested_at' => optional($refund->requested_at)->toIso8601String(),
                'processed_at' => optional($refund->processed_at)->toIso8601String(),
            ])),

            'refunds_count' => $this->when(
                isset($this->refunds_count),
                fn () => (int) $this->refunds_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
