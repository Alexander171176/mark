<?php

namespace App\Http\Resources\Admin\Blog\Comment;

use App\Http\Resources\Admin\Blog\BlogArticle\BlogArticleSharedResource;
use App\Http\Resources\Admin\Blog\BlogBanner\BlogBannerSharedResource;
use App\Http\Resources\Admin\Blog\BlogRubric\BlogRubricSharedResource;
use App\Http\Resources\Admin\Blog\BlogTag\BlogTagSharedResource;
use App\Http\Resources\Admin\Blog\BlogVideo\BlogVideoSharedResource;
use App\Http\Resources\Admin\System\User\UserSharedResource;
use App\Models\Admin\Blog\BlogArticle\BlogArticle;
use App\Models\Admin\Blog\BlogBanner\BlogBanner;
use App\Models\Admin\Blog\BlogRubric\BlogRubric;
use App\Models\Admin\Blog\BlogTag\BlogTag;
use App\Models\Admin\Blog\BlogVideo\BlogVideo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $commentableTitle = null;

        if ($this->relationLoaded('commentable') && $this->commentable) {
            if ($this->commentable instanceof BlogArticle) {
                $commentableTitle = $this->commentable->getTranslatedTitle();
            } elseif ($this->commentable instanceof BlogVideo) {
                $commentableTitle = $this->commentable->getTranslatedTitle();
            } elseif ($this->commentable instanceof BlogRubric) {
                $commentableTitle = $this->commentable->getTranslatedTitle();
            } elseif ($this->commentable instanceof BlogTag) {
                $commentableTitle = $this->commentable->getTranslatedName();
            } elseif ($this->commentable instanceof BlogBanner) {
                $commentableTitle = $this->commentable->getTranslatedTitle();
            } else {
                $commentableTitle = $this->commentable->title
                    ?? $this->commentable->name
                    ?? null;
            }
        }

        return [
            'id' => $this->id,

            // keys
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,

            // content
            'content' => $this->content,
            'activity' => $this->activity,

            // moderation
            'moderation_status' => $this->moderation_status,
            'is_approved' => (int) $this->moderation_status === 1,
            'moderated_by' => $this->moderated_by,
            'moderated_at' => $this->moderated_at?->toISOString(),
            'moderation_note' => $this->moderation_note,

            // timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // relations
            'user' => new UserSharedResource($this->whenLoaded('user')),

            'moderator' => $this->whenLoaded('moderator', function () {
                return new UserSharedResource($this->moderator);
            }),

            // polymorphic
            'commentable_type' => $this->commentable_type,
            'commentable_id' => $this->commentable_id,
            'commentable_title' => $commentableTitle,

            'commentable' => $this->whenLoaded('commentable', function () {
                if (!$this->commentable) {
                    return null;
                }

                return match (true) {
                    $this->commentable instanceof BlogArticle => new BlogArticleSharedResource($this->commentable),
                    $this->commentable instanceof BlogVideo => new BlogVideoSharedResource($this->commentable),
                    $this->commentable instanceof BlogRubric => new BlogRubricSharedResource($this->commentable),
                    $this->commentable instanceof BlogTag => new BlogTagSharedResource($this->commentable),
                    $this->commentable instanceof BlogBanner => new BlogBannerSharedResource($this->commentable),
                    default => [
                        'id' => $this->commentable_id,
                        'type' => class_basename((string) $this->commentable_type),
                    ],
                };
            }),

            // replies
            'replies' => CommentSharedResource::collection($this->whenLoaded('replies')),
            'replies_count' => $this->whenCounted('replies'),
        ];
    }
}
