<?php

namespace App\Http\Resources\Admin\School\SchoolRefund;

use App\Http\Resources\Admin\School\SchoolOrder\SchoolOrderSharedResource;
use App\Http\Resources\Admin\School\SchoolPayment\SchoolPaymentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolRefundResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $finalStatuses = ['succeeded', 'failed', 'canceled'];

        return [
            'id' => $this->id,

            'school_order_id' => $this->school_order_id,
            'school_payment_id' => $this->school_payment_id,

            'provider' => $this->provider,
            'provider_refund_id' => $this->provider_refund_id,

            'status' => $this->status,
            'currency' => $this->currency,
            'amount' => (string) $this->amount,

            'reason' => $this->reason,
            'notes' => $this->notes,
            'meta' => $this->meta,

            'requested_at' => optional($this->requested_at)->toIso8601String(),
            'processed_at' => optional($this->processed_at)->toIso8601String(),

            'is_final' => in_array($this->status, $finalStatuses, true),
            'is_succeeded' => $this->status === 'succeeded',

            'order' => new SchoolOrderSharedResource(
                $this->whenLoaded('order')
            ),

            'payment' => new SchoolPaymentResource(
                $this->whenLoaded('payment')
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
