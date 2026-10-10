<?php

namespace App\Http\Resources\Admin\NotificationEmailLog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationEmailLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'event' => $this->event,
            'recipient' => $this->recipient,
            'subject' => $this->subject,
            'status' => $this->status,
            'attempts' => $this->attempts,
            'queue' => $this->queue,
            'mailer' => $this->mailer,
            'error_type' => $this->error_type,
            'error_message' => $this->error_message,
            'queued_at' => $this->queued_at?->toIso8601String(),
            'processing_at' => $this->processing_at?->toIso8601String(),
            'sent_at' => $this->sent_at?->toIso8601String(),
            'failed_at' => $this->failed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
