<?php

namespace App\Http\Resources\Admin\School\SchoolTrack;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SchoolTrackSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstImage = $this->whenLoaded('images', fn () => $this->images->first());

        $thumbnailUrl = !($firstImage instanceof MissingValue) && $firstImage
            ? $firstImage->thumb_url
            : null;

        return [
            'id' => $this->id,

            'parent_id' => $this->parent_id,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'slug' => $this->slug,

            'name' => $this->translation?->name,
            'short' => $this->translation?->short,

            'views' => (int) $this->views,

            'thumbnail_url' => $thumbnailUrl,

            'children' => self::collection(
                $this->whenLoaded('children')
            ),

            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            'children_count' => $this->when(
                isset($this->children_count),
                fn () => (int) $this->children_count
            ),

            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () => (int) $this->courses_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
