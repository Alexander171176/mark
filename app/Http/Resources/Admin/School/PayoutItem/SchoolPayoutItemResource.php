<?php

namespace App\Http\Resources\Admin\School\PayoutItem;

use App\Http\Resources\Admin\School\Bundle\SchoolBundleSharedResource;
use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\Order\SchoolOrderSharedResource;
use App\Http\Resources\Admin\School\Payout\SchoolPayoutSharedResource;
use App\Http\Resources\Admin\School\Subscription\SchoolSubscriptionSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolPayoutItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'school_payout_id' => $this->school_payout_id,
            'school_order_id' => $this->school_order_id,
            'school_order_item_id' => $this->school_order_item_id,
            'school_course_id' => $this->school_course_id,
            'school_bundle_id' => $this->school_bundle_id,
            'school_subscription_id' => $this->school_subscription_id,

            'currency' => $this->currency,
            'amount_gross' => (string) $this->amount_gross,
            'fee_total' => (string) $this->fee_total,
            'tax_total' => (string) $this->tax_total,
            'amount_net' => (string) $this->amount_net,
            'computed_net' => $this->computed_net,

            'earned_at' => optional($this->earned_at)->toIso8601String(),

            'title' => $this->title,
            'notes' => $this->notes,
            'meta' => $this->meta,
            'display_label' => $this->display_label,

            'payout' => new SchoolPayoutSharedResource(
                $this->whenLoaded('payout')
            ),

            'order' => new SchoolOrderSharedResource(
                $this->whenLoaded('order')
            ),

            'order_item' => $this->whenLoaded('orderItem', fn () => [
                'id' => $this->orderItem->id,
                'school_order_id' => $this->orderItem->school_order_id,
                'type' => $this->orderItem->type,
                'title' => $this->orderItem->title,
                'sku' => $this->orderItem->sku,
                'currency' => $this->orderItem->currency,
                'quantity' => (int) $this->orderItem->quantity,
                'unit_price' => (string) $this->orderItem->unit_price,
                'discount' => (string) $this->orderItem->discount,
                'total' => (string) $this->orderItem->total,
            ]),

            'course' => new SchoolCourseSharedResource(
                $this->whenLoaded('course')
            ),

            'bundle' => new SchoolBundleSharedResource(
                $this->whenLoaded('bundle')
            ),

            'subscription' => new SchoolSubscriptionSharedResource(
                $this->whenLoaded('subscription')
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
