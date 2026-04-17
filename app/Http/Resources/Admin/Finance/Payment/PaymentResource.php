<?php

namespace App\Http\Resources\Admin\Finance\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Представление платежа для админ-API.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'order_id'                => $this->order_id,
            'payment_method_id'       => $this->payment_method_id,
            'user_payment_method_id'  => $this->user_payment_method_id,

            'provider'               => $this->provider,
            'provider_payment_id'    => $this->provider_payment_id,
            'idempotency_key'        => $this->idempotency_key,

            'status'   => $this->status,     // pending|processing|succeeded|failed|canceled|refunded|partially_refunded
            'currency' => $this->currency,   // ISO 4217
            'amount'   => $this->amount,

            'captured_at'     => optional($this->captured_at)?->toISOString(),
            'refunded_at'     => optional($this->refunded_at)?->toISOString(),
            'refunded_amount' => $this->refunded_amount,

            'error_code'    => $this->error_code,
            'error_message' => $this->error_message,
            'meta'          => $this->meta,

            'is_refunded'          => (bool) ($this->status === 'refunded'),
            'is_partially_refunded'=> (bool) ($this->status === 'partially_refunded'),
            'refundable_amount'    => is_null($this->refunded_amount)
                ? $this->amount
                : max(0, (float)$this->amount - (float)$this->refunded_amount),

            // опционально грузим связи
            'order' => $this->whenLoaded('order', fn () => [
                'id'      => $this->order->id,
                'number'  => $this->order->number,
                'user_id' => $this->order->user_id,
                'total'   => $this->order->total,
                'status'  => $this->order->status,
            ]),
            'payment_method' => $this->whenLoaded('paymentMethod', fn () => [
                'id'       => $this->paymentMethod->id,
                'code'     => $this->paymentMethod->code,
                'name'     => $this->paymentMethod->name,
                'provider' => $this->paymentMethod->provider,
                'type'     => $this->paymentMethod->type,
            ]),
            'user_payment_method' => $this->whenLoaded('userPaymentMethod', fn () => [
                'id'        => $this->userPaymentMethod->id,
                'provider'  => $this->userPaymentMethod->provider,
                'brand'     => $this->userPaymentMethod->brand,
                'last4'     => $this->userPaymentMethod->last4,
                'exp_month' => $this->userPaymentMethod->exp_month,
                'exp_year'  => $this->userPaymentMethod->exp_year,
            ]),

            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
            'deleted_at' => optional($this->deleted_at)?->toISOString(),
        ];
    }
}
