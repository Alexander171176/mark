<?php

namespace App\Http\Resources\Admin\Blog\Banner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $thumbnailUrl = null;

        if ($this->relationLoaded('images') && $this->images?->count()) {
            $img = $this->images->first();

            // BaseImage accessors:
            // thumb_url / webp_url / image_url
            $thumbnailUrl = $img->thumb_url
                ?: $img->webp_url
                    ?: $img->image_url;
        }

        return [
            'id'       => $this->id,
            'title'    => $this->title,
            'link'     => $this->link,
            'activity' => $this->activity,

            'left'  => $this->left,
            'main'  => $this->main,
            'right' => $this->right,

            'locale' => $this->locale,

            'thumbnail_url' => $thumbnailUrl,
        ];
    }
}
