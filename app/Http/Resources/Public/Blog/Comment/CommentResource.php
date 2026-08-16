<?php

namespace App\Http\Resources\Public\Blog\Comment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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

            /**
             * Текст комментария.
             */
            'content' => $this->content,

            /**
             * Автор.
             *
             * Email и административные
             * данные публичной части не нужны.
             */
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

            /**
             * Публичные ответы.
             */
            'replies' => CommentSharedResource::collection(
                $this->whenLoaded('replies')
            ),

            /**
             * Количество только публичных ответов.
             */
            'replies_count' => $this->when(
                isset($this->replies_count),
                fn () => (int) $this->replies_count
            ),

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
