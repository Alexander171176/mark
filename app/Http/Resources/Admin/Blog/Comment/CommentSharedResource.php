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
            'id' =>
                $this->id,

            'user_id' =>
                $this->user_id,

            'parent_id' =>
                $this->parent_id,

            'content' =>
                $this->content,

            'activity' =>
                (bool) $this->activity,

            'moderation_status' =>
                (int) $this->moderation_status,

            'is_approved' =>
                (int) $this->moderation_status === 1,

            'created_at' =>
                $this->created_at?->toISOString(),

            'user' => $this->whenLoaded(
                'user',
                fn () => $this->user
                    ? new UserSharedResource(
                        $this->user
                    )
                    : null
            ),

            'replies_count' => $this->when(
                isset($this->replies_count),
                fn () => (int) $this->replies_count
            ),
        ];
    }
}
