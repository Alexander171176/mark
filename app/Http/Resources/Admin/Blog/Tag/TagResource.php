<?php

namespace App\Http\Resources\Admin\Blog\Tag;

use App\Http\Resources\Admin\Blog\Article\ArticleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'user_id' => $this->user_id,

            // flags
            'sort'     => $this->sort,
            'activity' => $this->activity,

            // moderation
            'moderation_status' => $this->moderation_status,
            'is_approved'       => (int) $this->moderation_status === 1,
            'moderated_by'      => $this->moderated_by,
            'moderated_at'      => $this->moderated_at?->toISOString(),
            'moderation_note'   => $this->moderation_note,

            // content
            'icon'        => $this->icon,
            'locale'      => $this->locale,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'subtitle'    => $this->subtitle,
            'short'       => $this->short,
            'description' => $this->description,

            // counters
            'views' => $this->views,

            // seo
            'meta_title'    => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc'     => $this->meta_desc,

            // timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // counts (если в контроллере withCount('articles'))
            'articles_count' => $this->whenCounted('articles'),

            // relations (только если подгружены)
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id'    => $this->owner?->id,
                    'name'  => $this->owner?->name,
                    'email' => $this->owner?->email,
                    'profile_photo_url' => $this->owner?->profile_photo_url, // ✅ Jetstream
                ];
            }),

            'moderator' => $this->whenLoaded('moderator', function () {
                return [
                    'id'   => $this->moderator?->id,
                    'name' => $this->moderator?->name,
                ];
            }),

            'articles' => ArticleSharedResource::collection($this->whenLoaded('articles')),
        ];
    }
}
