<?php

namespace App\Http\Resources\Admin\School\Invoice;

use App\Http\Resources\Admin\School\Order\SchoolOrderSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_order_id' => $this->school_order_id,

            'number' => $this->number,
            'status' => $this->status,

            'currency' => $this->currency,
            'subtotal' => (string) $this->subtotal,
            'discount_total' => (string) $this->discount_total,
            'tax_total' => (string) $this->tax_total,
            'total' => (string) $this->total,

            'issued_at' => optional($this->issued_at)->toIso8601String(),
            'due_at' => optional($this->due_at)->toIso8601String(),
            'paid_at' => optional($this->paid_at)->toIso8601String(),

            'bill_to' => [
                'name' => $this->bill_to_name,
                'tax_id' => $this->bill_to_tax_id,
                'email' => $this->bill_to_email,
                'address1' => $this->bill_to_address1,
                'address2' => $this->bill_to_address2,
                'city' => $this->bill_to_city,
                'region' => $this->bill_to_region,
                'postcode' => $this->bill_to_postcode,
                'country' => $this->bill_to_country,
            ],

            'notes' => $this->notes,
            'meta' => $this->meta,

            'is_paid' => (bool) $this->is_paid,

            'order' => new SchoolOrderSharedResource(
                $this->whenLoaded('order')
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
