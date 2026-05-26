<?php

namespace App\Http\Resources\Admin\School\Coupon;

use App\Http\Resources\Admin\School\Bundle\SchoolBundleSharedResource;
use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,

            'type' => $this->type,
            'value' => (string) $this->value,
            'currency' => $this->currency,

            'min_order_total' => $this->min_order_total !== null ? (string) $this->min_order_total : null,
            'max_uses' => $this->max_uses,
            'max_uses_per_user' => $this->max_uses_per_user,
            'used_count' => (int) $this->used_count,

            'applies_to' => $this->applies_to,

            'starts_at' => optional($this->starts_at)->toIso8601String(),
            'ends_at' => optional($this->ends_at)->toIso8601String(),

            'activity' => (bool) $this->activity,
            'stackable' => (bool) $this->stackable,

            'meta' => $this->meta,

            'is_currently_valid' => (bool) $this->is_currently_valid,
            'is_usage_limit_reached' => (bool) $this->is_usage_limit_reached,

            'courses' => SchoolCourseSharedResource::collection(
                $this->whenLoaded('courses')
            ),

            'bundles' => SchoolBundleSharedResource::collection(
                $this->whenLoaded('bundles')
            ),

            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () => (int) $this->courses_count
            ),

            'bundles_count' => $this->when(
                isset($this->bundles_count),
                fn () => (int) $this->bundles_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
