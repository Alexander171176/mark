<?php

namespace App\Http\Resources\Admin\School\Payout;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolPayoutSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'school_instructor_profile_id' => $this->school_instructor_profile_id,
            'school_provider_account_id' => $this->school_provider_account_id,

            'number' => $this->number,
            'status' => $this->status,
            'method' => $this->method,
            'currency' => $this->currency,

            'amount_gross' => (string) $this->amount_gross,
            'fee_total' => (string) $this->fee_total,
            'tax_total' => (string) $this->tax_total,
            'amount_net' => (string) $this->amount_net,
            'computed_net' => $this->computed_net,

            'period_start' => optional($this->period_start)->toDateString(),
            'period_end' => optional($this->period_end)->toDateString(),
            'paid_at' => optional($this->paid_at)->toIso8601String(),

            'display_name' => $this->display_name,
        ];
    }
}
