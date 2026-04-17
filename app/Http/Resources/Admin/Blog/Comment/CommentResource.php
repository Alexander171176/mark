<?php

namespace App\Http\Resources\Admin\Blog\Comment;

use App\Http\Resources\Admin\Blog\Article\ArticleSharedResource;
use App\Http\Resources\Admin\Blog\Video\VideoSharedResource;
use App\Http\Resources\Admin\System\User\UserSharedResource;
use App\Models\Admin\Blog\Article\Article;
use App\Models\Admin\Blog\Video\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $commentableTitle = null;

        if ($this->relationLoaded('commentable') && $this->commentable) {
            $commentableTitle = $this->commentable->title
                ?? $this->commentable->name
                ?? null;
        }

        return [
            'id' => $this->id,

            // keys
            'user_id'   => $this->user_id,
            'parent_id' => $this->parent_id,

            // content
            'content'  => $this->content,
            'activity' => $this->activity,

            // moderation (вместо approved)
            'moderation_status' => $this->moderation_status,
            'is_approved'       => (int) $this->moderation_status === 1,
            'moderated_by'      => $this->moderated_by,
            'moderated_at'      => $this->moderated_at?->toISOString(),
            'moderation_note'   => $this->moderation_note,

            // timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // relations
            'user' => new UserSharedResource($this->whenLoaded('user')),

            'moderator' => $this->whenLoaded('moderator', function () {
                // если нет отдельного UserSharedResource под System — можно заменить на массив
                return new UserSharedResource($this->moderator);
            }),

            // polymorphic
            'commentable_type'  => $this->commentable_type,
            'commentable_id'    => $this->commentable_id,
            'commentable_title' => $commentableTitle,

            'commentable' => $this->whenLoaded('commentable', function () {
                return match ($this->commentable_type) {
                    Article::class => new ArticleSharedResource($this->commentable),
                    Video::class   => new VideoSharedResource($this->commentable),
                    default        => [
                        'id'   => $this->commentable_id,
                        'type' => class_basename((string) $this->commentable_type),
                    ],
                };
            }),

            // replies (лучше отдавать shared, чтобы не раздувать рекурсию)
            'replies' => CommentSharedResource::collection($this->whenLoaded('replies')),
            'replies_count' => $this->whenCounted('replies'),
        ];
    }
}
