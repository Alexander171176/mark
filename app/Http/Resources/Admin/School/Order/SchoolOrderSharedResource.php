<?php

namespace App\Http\Resources\Admin\School\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolOrderSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            'number' => $this->number,

            'buyer_name' => $this->buyer_name,
            'buyer_email' => $this->buyer_email,
            'buyer_phone' => $this->buyer_phone,

            'currency' => $this->currency,
            'subtotal' => (string) $this->subtotal,
            'discount_total' => (string) $this->discount_total,
            'tax_total' => (string) $this->tax_total,
            'total' => (string) $this->total,

            'status' => $this->status,
            'payment_status' => $this->payment_status,

            'is_paid' => (bool) $this->is_paid || in_array($this->payment_status, ['paid', 'succeeded'], true),
            'paid_at' => optional($this->paid_at)->toIso8601String(),

            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
