<?php

namespace App\Http\Resources\Admin\Finance\WebhookEvent;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookEventResource extends JsonResource
{
    /**
     * Представление входящего вебхука.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,

            'provider'         => $this->provider,
            'event_type'       => $this->event_type,

            'external_id'      => $this->external_id,
            'idempotency_key'  => $this->idempotency_key,
            'signature'        => $this->signature,

            // Привязки (ID всегда есть; вложенные объекты — только если подгружены)
            'order_id'         => $this->order_id,
            'payment_id'       => $this->payment_id,
            'subscription_id'  => $this->subscription_id,

            // Сырые данные
            'payload'          => $this->payload,
            'headers'          => $this->headers,

            // Статус обработки
            'status'           => $this->status,
            'attempts'         => (int) $this->attempts,
            'error_message'    => $this->error_message,

            // Таймстемпы в ISO
            'delivered_at'     => optional($this->delivered_at)?->toISOString(),
            'processed_at'     => optional($this->processed_at)?->toISOString(),
            'created_at'       => optional($this->created_at)?->toISOString(),
            'updated_at'       => optional($this->updated_at)?->toISOString(),
            'deleted_at'       => optional($this->deleted_at)?->toISOString(),

            // Удобные флаги для UI
            'is_processed'     => $this->status === 'processed',
            'is_failed'        => $this->status === 'failed',
        ];
    }
}
