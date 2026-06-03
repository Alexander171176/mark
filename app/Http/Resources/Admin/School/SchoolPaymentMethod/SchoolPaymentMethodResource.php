<?php

namespace App\Http\Resources\Admin\School\SchoolPaymentMethod;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolPaymentMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'code' => $this->code,
            'name' => $this->name,
            'provider' => $this->provider,
            'type' => $this->type,

            'supports_refund' => (bool) $this->supports_refund,
            'supports_recurring' => (bool) $this->supports_recurring,

            'activity' => (bool) $this->activity,
            'sort' => (int) $this->sort,
            'meta' => $this->meta,

            'payments_count' => $this->when(
                isset($this->payments_count),
                fn () => (int) $this->payments_count
            ),

            'user_payment_methods_count' => $this->when(
                isset($this->user_payment_methods_count),
                fn () => (int) $this->user_payment_methods_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
