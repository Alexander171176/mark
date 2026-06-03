<?php

namespace App\Http\Resources\Admin\School\SchoolQaThread;

use App\Http\Resources\Admin\School\SchoolQaMessage\SchoolQaMessageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQaThreadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'user_id' => $this->user_id,

            'threadable_type' => $this->threadable_type,
            'threadable_id' => $this->threadable_id,

            'title' => $this->title,
            'body' => $this->body,

            'status' => $this->status,
            'is_locked' => (bool) $this->is_locked,
            'is_pinned' => (bool) $this->is_pinned,

            'replies_count' => (int) $this->replies_count,
            'last_reply_at' => optional($this->last_reply_at)->toIso8601String(),
            'last_activity_at' => optional($this->last_activity_at)->toIso8601String(),

            'meta' => $this->meta,

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),

            'threadable' => $this->whenLoaded('threadable', function () {
                if (! $this->threadable) {
                    return null;
                }

                return [
                    'type' => class_basename($this->threadable_type),
                    'id' => $this->threadable->getKey(),
                    'title' => $this->threadable->translation?->title
                        ?? $this->threadable->translation?->name
                            ?? $this->threadable->title
                            ?? $this->threadable->name
                            ?? null,
                    'slug' => $this->threadable->slug ?? null,
                ];
            }),

            'messages' => SchoolQaMessageResource::collection(
                $this->whenLoaded('messages')
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
