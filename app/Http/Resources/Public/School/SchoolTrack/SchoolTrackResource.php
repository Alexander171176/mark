<?php

namespace App\Http\Resources\Public\School\SchoolTrack;

use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolTrackResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translationOrFallback();

        $parentTranslation = $this->relationLoaded('parent') && $this->parent
            ? $this->parent->translationOrFallback()
            : null;

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
                    'description' => $translation->description,
                    'meta_title' => $translation->meta_title,
                    'meta_keywords' => $translation->meta_keywords,
                    'meta_desc' => $translation->meta_desc,
                ]
                : null,

            'parent' => $this->whenLoaded(
                'parent',
                fn () => $this->parent
                    ? [
                        'id' => $this->parent->id,
                        'parent_id' => $this->parent->parent_id,
                        'slug' => $this->parent->slug,

                        'translation' => $parentTranslation
                            ? [
                                'locale' => $parentTranslation->locale,
                                'name' => $parentTranslation->name,
                                'short' => $parentTranslation->short,
                            ]
                            : null,
                    ]
                    : null
            ),

            'images' => SchoolTrackImageResource::collection(
                $this->whenLoaded('images')
            ),

            'children' => SchoolTrackSharedResource::collection(
                $this->whenLoaded('children')
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

            'is_root' => $this->parent_id === null,

            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
