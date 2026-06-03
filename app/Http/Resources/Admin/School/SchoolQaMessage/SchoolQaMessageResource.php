<?php

namespace App\Http\Resources\Admin\School\SchoolQaMessage;

use App\Http\Resources\Admin\School\SchoolQaThread\SchoolQaThreadSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQaMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'thread_id' => $this->thread_id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,

            'body' => $this->body,

            'is_private' => (bool) $this->is_private,
            'is_pinned' => (bool) $this->is_pinned,

            'replies_count' => (int) $this->replies_count,
            'edited_at' => optional($this->edited_at)->toIso8601String(),

            'meta' => $this->meta,

            'thread' => new SchoolQaThreadSharedResource(
                $this->whenLoaded('thread')
            ),

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),

            'parent' => $this->whenLoaded('parent', fn () => [
                'id' => $this->parent->id,
                'user_id' => $this->parent->user_id,
                'body' => $this->parent->body,
                'created_at' => optional($this->parent->created_at)->toIso8601String(),
            ]),

            'replies' => self::collection(
                $this->whenLoaded('replies')
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
