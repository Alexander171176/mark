<?php

namespace App\Http\Resources\Admin\School\SchoolWebhookEvent;

use App\Http\Resources\Admin\School\SchoolOrder\SchoolOrderSharedResource;
use App\Http\Resources\Admin\School\SchoolPayment\SchoolPaymentResource;
use App\Http\Resources\Admin\School\SchoolSubscription\SchoolSubscriptionSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolWebhookEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'provider' => $this->provider,
            'event_type' => $this->event_type,

            'external_id' => $this->external_id,
            'idempotency_key' => $this->idempotency_key,
            'signature' => $this->signature,

            'school_order_id' => $this->school_order_id,
            'school_payment_id' => $this->school_payment_id,
            'school_subscription_id' => $this->school_subscription_id,

            'payload' => $this->payload,
            'headers' => $this->headers,

            'status' => $this->status,
            'attempts' => (int) $this->attempts,
            'error_message' => $this->error_message,

            'delivered_at' => optional($this->delivered_at)->toIso8601String(),
            'processed_at' => optional($this->processed_at)->toIso8601String(),

            'is_processed' => $this->status === 'processed',
            'is_failed' => $this->status === 'failed',

            'order' => new SchoolOrderSharedResource(
                $this->whenLoaded('order')
            ),

            'payment' => new SchoolPaymentResource(
                $this->whenLoaded('payment')
            ),

            'subscription' => new SchoolSubscriptionSharedResource(
                $this->whenLoaded('subscription')
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
