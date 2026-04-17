<?php

namespace App\Http\Resources\Admin\Finance\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Представление инвойса для админ-API.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'order_id'        => $this->order_id,
            'number'          => $this->number,
            'status'          => $this->status,         // draft|issued|paid|void|refunded

            'currency'        => $this->currency,
            'subtotal'        => $this->subtotal,
            'discount_total'  => $this->discount_total,
            'tax_total'       => $this->tax_total,
            'total'           => $this->total,

            'issued_at'       => optional($this->issued_at)?->toISOString(),
            'due_at'          => optional($this->due_at)?->toISOString(),
            'paid_at'         => optional($this->paid_at)?->toISOString(),

            // Снимок реквизитов плательщика
            'bill_to' => [
                'name'      => $this->bill_to_name,
                'tax_id'    => $this->bill_to_tax_id,
                'email'     => $this->bill_to_email,
                'address1'  => $this->bill_to_address1,
                'address2'  => $this->bill_to_address2,
                'city'      => $this->bill_to_city,
                'region'    => $this->bill_to_region,
                'postcode'  => $this->bill_to_postcode,
                'country'   => $this->bill_to_country,
            ],

            'notes'           => $this->notes,
            'meta'            => $this->meta,

            'created_at'      => optional($this->created_at)?->toISOString(),
            'updated_at'      => optional($this->updated_at)?->toISOString(),
            'deleted_at'      => optional($this->deleted_at)?->toISOString(),

            // Удобный флаг из аксессора
            'is_paid'         => (bool) $this->is_paid,

            // Короткая инфа о заказе (если подгружен)
            'order' => $this->whenLoaded('order', fn () => [
                'id'      => $this->order->id,
                'number'  => $this->order->number,
                'status'  => $this->order->status,
                'total'   => $this->order->total,
                'currency'=> $this->order->currency,
            ]),
        ];
    }
}
