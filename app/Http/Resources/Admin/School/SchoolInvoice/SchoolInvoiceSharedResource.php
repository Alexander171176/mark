<?php

namespace App\Http\Resources\Admin\School\SchoolInvoice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolInvoiceSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_order_id' => $this->school_order_id,

            'number' => $this->number,
            'status' => $this->status,

            'currency' => $this->currency,
            'total' => (string) $this->total,

            'issued_at' => optional($this->issued_at)->toIso8601String(),
            'due_at' => optional($this->due_at)->toIso8601String(),
            'paid_at' => optional($this->paid_at)->toIso8601String(),

            'is_paid' => (bool) $this->is_paid,
        ];
    }
}
