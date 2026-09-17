<?php

namespace App\Http\Resources\Public\School\SchoolTrack;

use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolTrackSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translationOrFallback();

        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'sort' => (int) $this->sort,
            'slug' => $this->slug,
            'views' => (int) $this->views,

            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'name' => $translation->name,
                    'short' => $translation->short,
                ]
                : null,

            'images' => SchoolTrackImageResource::collection(
                $this->whenLoaded('images')
            ),

            'children_count' => $this->when(
                isset($this->children_count),
                fn () => (int) $this->children_count
            ),

            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () => (int) $this->courses_count
            ),

            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'already_liked' => (bool) ($this->already_liked ?? false),

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
