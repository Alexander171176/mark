<?php

namespace App\Http\Resources\Admin\Finance\PayoutItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayoutItemResource extends JsonResource
{
    /**
     * Представление позиции выплаты (расшифровка).
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'payout_id'     => $this->payout_id,

            // Источник дохода
            'order_id'      => $this->order_id,
            'order_item_id' => $this->order_item_id,
            'course_id'     => $this->course_id,
            'bundle_id'     => $this->bundle_id,
            'subscription_id'=> $this->subscription_id,

            // Денежные поля
            'currency'      => $this->currency,
            'amount_gross'  => (string) $this->amount_gross,
            'fee_total'     => (string) $this->fee_total,
            'tax_total'     => (string) $this->tax_total,
            'amount_net'    => (string) $this->amount_net,
            'computed_net'  => $this->when(isset($this->computed_net), $this->computed_net),

            'earned_at'     => optional($this->earned_at)?->toISOString(),
            'title'         => $this->title,
            'notes'         => $this->notes,
            'meta'          => $this->meta,

            'display_label' => $this->when(isset($this->display_label), $this->display_label),

            // Связи (лениво, только если загружены)
            'order' => $this->whenLoaded('order', fn () => [
                'id'     => $this->order->id,
                'number' => $this->order->number,
                'status' => $this->order->status,
                'total'  => (string) $this->order->total,
            ]),
            'order_item' => $this->whenLoaded('orderItem', fn () => [
                'id'        => $this->orderItem->id,
                'item_type' => $this->orderItem->item_type,
                'title'     => $this->orderItem->title,
                'total'     => (string) $this->orderItem->total,
            ]),
            'course' => $this->whenLoaded('course', fn () => [
                'id'    => $this->course->id,
                'title' => $this->course->title,
                'slug'  => $this->course->slug,
            ]),
            'bundle' => $this->whenLoaded('bundle', fn () => [
                'id'    => $this->bundle->id,
                'title' => $this->bundle->title,
                'slug'  => $this->bundle->slug,
            ]),
            'subscription' => $this->whenLoaded('subscription', fn () => [
                'id'      => $this->subscription->id,
                'status'  => $this->subscription->status,
                'price'   => (string) $this->subscription->price,
                'currency'=> $this->subscription->currency,
            ]),

            'created_at'    => optional($this->created_at)?->toISOString(),
            'updated_at'    => optional($this->updated_at)?->toISOString(),
        ];
    }
}
