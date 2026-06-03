<?php

namespace App\Http\Resources\Admin\School\SchoolPayout;

use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use App\Http\Resources\Admin\School\SchoolPayoutItem\SchoolPayoutItemResource;
use App\Http\Resources\Admin\School\SchoolProviderAccount\SchoolProviderAccountSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolPayoutResource extends JsonResource
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

            'period_start' => optional($this->period_start)->toDateString(),
            'period_end' => optional($this->period_end)->toDateString(),

            'amount_gross' => (string) $this->amount_gross,
            'fee_total' => (string) $this->fee_total,
            'tax_total' => (string) $this->tax_total,
            'amount_net' => (string) $this->amount_net,
            'computed_net' => $this->computed_net,

            'paid_at' => optional($this->paid_at)->toIso8601String(),

            'notes' => $this->notes,
            'meta' => $this->meta,

            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,

            'display_name' => $this->display_name,

            'instructor' => new SchoolInstructorProfileSharedResource(
                $this->whenLoaded('instructor')
            ),

            'provider_account' => new SchoolProviderAccountSharedResource(
                $this->whenLoaded('providerAccount')
            ),

            'items' => SchoolPayoutItemResource::collection(
                $this->whenLoaded('items')
            ),

            'creator' => $this->whenLoaded('creator', fn () => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'email' => $this->creator->email,
            ]),

            'updater' => $this->whenLoaded('updater', fn () => [
                'id' => $this->updater->id,
                'name' => $this->updater->name,
                'email' => $this->updater->email,
            ]),

            'items_count' => $this->when(
                isset($this->items_count),
                fn () => (int) $this->items_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
