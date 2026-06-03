<?php

namespace App\Http\Resources\Admin\School\SchoolWebhookEvent;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolWebhookEventSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'provider' => $this->provider,
            'event_type' => $this->event_type,
            'external_id' => $this->external_id,

            'status' => $this->status,
            'attempts' => (int) $this->attempts,

            'is_processed' => $this->status === 'processed',
            'is_failed' => $this->status === 'failed',

            'delivered_at' => optional($this->delivered_at)->toIso8601String(),
            'processed_at' => optional($this->processed_at)->toIso8601String(),
        ];
    }
}
