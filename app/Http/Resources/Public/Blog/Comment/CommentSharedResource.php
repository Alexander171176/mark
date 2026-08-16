<?php

namespace App\Http\Resources\Public\Blog\Comment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,

            'user_id' => $this->user_id
                ? (int) $this->user_id
                : null,

            'parent_id' => $this->parent_id
                ? (int) $this->parent_id
                : null,

            'content' => $this->content,

            'user' => $this->whenLoaded(
                'user',
                fn () => $this->user
                    ? [
                        'id' => (int) $this->user->id,
                        'name' => $this->user->name,
                        'profile_photo_url' =>
                            $this->user->profile_photo_url,
                    ]
                    : null
            ),

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
