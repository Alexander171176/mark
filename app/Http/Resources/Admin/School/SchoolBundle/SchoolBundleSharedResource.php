<?php

namespace App\Http\Resources\Admin\School\SchoolBundle;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SchoolBundleSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstImage = $this->whenLoaded('images', fn () => $this->images->first());

        $thumbnailUrl = !($firstImage instanceof MissingValue) && $firstImage
            ? ($firstImage->thumb_url ?? $firstImage->image_url ?? $firstImage->url ?? null)
            : null;

        return [
            'id' => $this->id,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'subtitle' => $this->translation?->subtitle,
            'short' => $this->translation?->short,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'published_at' => optional($this->published_at)->format('Y-m-d'),

            'views' => (int) $this->views,
            'likes' => (int) $this->likes,

            'thumbnail_url' => $thumbnailUrl,
        ];
    }
}
