<?php

namespace App\Http\Resources\Admin\Blog\Comment;

use App\Http\Resources\Admin\System\User\UserSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,

            'content' => $this->content,
            'activity' => $this->activity,

            'moderation_status' => $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,

            'created_at' => $this->created_at?->toISOString(),

            'user' => new UserSharedResource($this->whenLoaded('user')),

            'replies_count' => $this->whenCounted('replies'),
        ];
    }
}
