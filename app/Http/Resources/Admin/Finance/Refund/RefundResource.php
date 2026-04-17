<?php

namespace App\Http\Resources\Admin\Finance\Refund;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundResource extends JsonResource
{
    /**
     * Представление возврата для админ-API.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        $finalStatuses = ['succeeded','failed','canceled'];

        return [
            'id'                  => $this->id,
            'order_id'            => $this->order_id,
            'payment_id'          => $this->payment_id,

            'provider'            => $this->provider,
            'provider_refund_id'  => $this->provider_refund_id,

            'status'              => $this->status, // requested|processing|succeeded|failed|canceled
            'currency'            => $this->currency,
            'amount'              => $this->amount,

            'reason'              => $this->reason,
            'notes'               => $this->notes,
            'meta'                => $this->meta,

            'requested_at'        => optional($this->requested_at)?->toISOString(),
            'processed_at'        => optional($this->processed_at)?->toISOString(),
            'created_at'          => optional($this->created_at)?->toISOString(),
            'updated_at'          => optional($this->updated_at)?->toISOString(),
            'deleted_at'          => optional($this->deleted_at)?->toISOString(),

            // Удобные флаги
            'is_final'            => in_array($this->status, $finalStatuses, true),
            'is_succeeded'        => $this->status === 'succeeded',

            // Короткие сведения о заказе/платеже (если подгружены)
            'order' => $this->whenLoaded('order', fn () => [
                'id'        => $this->order->id,
                'number'    => $this->order->number,
                'user_id'   => $this->order->user_id,
                'currency'  => $this->order->currency,
                'total'     => $this->order->total,
                'status'    => $this->order->status,
            ]),
            'payment' => $this->whenLoaded('payment', fn () => [
                'id'        => $this->payment->id,
                'status'    => $this->payment->status,
                'amount'    => $this->payment->amount,
                'currency'  => $this->payment->currency,
                'provider'  => $this->payment->provider,
                'provider_payment_id' => $this->payment->provider_payment_id,
            ]),
        ];
    }
}
