<?php

namespace App\Http\Resources\Admin\School\QaThread;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQaThreadSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            'threadable_type' => $this->threadable_type,
            'threadable_id' => $this->threadable_id,

            'title' => $this->title,
            'status' => $this->status,

            'is_locked' => (bool) $this->is_locked,
            'is_pinned' => (bool) $this->is_pinned,

            'replies_count' => (int) $this->replies_count,

            'last_reply_at' => optional($this->last_reply_at)->toIso8601String(),
            'last_activity_at' => optional($this->last_activity_at)->toIso8601String(),
        ];
    }
}
