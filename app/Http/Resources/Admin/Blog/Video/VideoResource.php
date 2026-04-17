<?php

namespace App\Http\Resources\Admin\Blog\Video;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = null;

        if ($this->relationLoaded('images') && $this->images?->count()) {
            $cover = $this->images->first(); // уже отсортировано по pivot.order
        }

        return [
            'id'       => $this->id,

            'user_id'  => $this->user_id,
            'sort'     => $this->sort,
            'activity'   => $this->activity,
            'is_private' => $this->is_private,
            'is_public'  => $this->is_public,

            'left'  => $this->left,
            'main'  => $this->main,
            'right' => $this->right,

            'locale' => $this->locale,

            // moderation
            'moderation_status' => $this->moderation_status,
            'is_approved'       => (int) $this->moderation_status === 1,
            'moderated_by'      => $this->moderated_by,
            'moderated_at'      => $this->moderated_at?->toISOString(),
            'moderation_note'   => $this->moderation_note,

            'title'       => $this->title,
            'url'         => $this->url,
            'short'       => $this->short,
            'description' => $this->description,
            'pseudonym'   => $this->pseudonym,

            // dates
            'published_at'  => $this->published_at?->format('Y-m-d'),
            'show_from_at'  => $this->show_from_at?->format('Y-m-d\TH:i'),
            'show_to_at'    => $this->show_to_at?->format('Y-m-d\TH:i'),

            'duration'    => $this->duration,
            'source_type' => $this->source_type,

            // источники видео (через accessors модели)
            'video_url'         => $this->video_url,
            'embed_url'         => $this->embed_url,
            'embed_code'        => $this->embed_code,
            'external_video_id' => $this->external_video_id,

            'views' => $this->views,

            'meta_title'    => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc'     => $this->meta_desc,

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

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // cover image (если есть)
            'cover_thumb_url' => $cover?->thumb_url,
            'cover_webp_url'  => $cover?->webp_url,
            'cover_image_url' => $cover?->image_url,

            // counts (если подгружали withCount)
            'images_count'   => $this->whenCounted('images'),
            'comments_count' => $this->whenCounted('comments'),
            'likes_count'    => $this->whenCounted('likes'),

            // relations
            'images' => VideoImageResource::collection($this->whenLoaded('images')),
            'related_videos' => VideoSharedResource::collection($this->whenLoaded('relatedVideos')),
        ];
    }
}
