<?php

namespace App\Http\Resources\Admin\Blog\Video;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = null;

        if ($this->relationLoaded('images') && $this->images?->count()) {
            $cover = $this->images->first();
        }

        return [
            'id'       => $this->id,
            'locale'   => $this->locale,
            'title'    => $this->title,
            'url'      => $this->url,
            'activity'   => $this->activity,
            'is_private' => $this->is_private,
            'is_public'  => $this->is_public,

            'moderation_status' => $this->moderation_status,
            'is_approved'       => (int) $this->moderation_status === 1,

            'published_at' => $this->published_at?->format('Y-m-d'),
            'source_type'  => $this->source_type,

            'display_source' => match ($this->source_type) {
                'local'   => $this->video_url,
                'youtube' => $this->embed_url,
                'vimeo'   => $this->embed_url,
                'code'    => $this->embed_code,
                default   => null,
            },

            'cover_thumb_url' => $cover?->thumb_url,
            'cover_webp_url'  => $cover?->webp_url,
            'cover_image_url' => $cover?->image_url,
        ];
    }
}
