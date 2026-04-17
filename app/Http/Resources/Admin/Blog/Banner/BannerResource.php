<?php

namespace App\Http\Resources\Admin\Blog\Banner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cover = null;

        if ($this->relationLoaded('images') && $this->images?->count()) {
            $cover = $this->images->first();
        }

        return [
            'id'       => $this->id,

            'user_id'  => $this->user_id,
            'sort'     => $this->sort,
            'activity' => $this->activity,

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

            'title'   => $this->title,
            'link'    => $this->link,
            'short'   => $this->short,
            'comment' => $this->comment,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // cover (первое изображение по order)
            'cover_thumb_url' => $cover?->thumb_url,
            'cover_webp_url'  => $cover?->webp_url,
            'cover_image_url' => $cover?->image_url,

            // images list
            'images'       => BannerImageResource::collection($this->whenLoaded('images')),
            'images_count' => $this->whenLoaded('images', fn () => $this->resource->images->count()),

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
        ];
    }
}
