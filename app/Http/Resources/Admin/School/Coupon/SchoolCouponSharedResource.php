<?php

namespace App\Http\Resources\Admin\School\Coupon;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCouponSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'code' => $this->code,
            'name' => $this->name,

            'type' => $this->type,
            'value' => (string) $this->value,
            'currency' => $this->currency,

            'applies_to' => $this->applies_to,
            'activity' => (bool) $this->activity,
            'stackable' => (bool) $this->stackable,

            'is_currently_valid' => (bool) $this->is_currently_valid,
            'is_usage_limit_reached' => (bool) $this->is_usage_limit_reached,
        ];
    }
}
