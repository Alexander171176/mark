<?php

namespace App\Http\Resources\Admin\School\SchoolPayoutItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolPayoutItemSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'school_payout_id' => $this->school_payout_id,

            'currency' => $this->currency,
            'amount_gross' => (string) $this->amount_gross,
            'fee_total' => (string) $this->fee_total,
            'tax_total' => (string) $this->tax_total,
            'amount_net' => (string) $this->amount_net,
            'computed_net' => $this->computed_net,

            'earned_at' => optional($this->earned_at)->toIso8601String(),
            'title' => $this->title,
            'display_label' => $this->display_label,
        ];
    }
}
