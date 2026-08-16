<?php

namespace App\Http\Resources\Admin\School\SchoolTrack;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolTrackResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * scopeForPublic() уже загружает translations
         * только для текущей локали.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translations->first()
            : null;

        return [
            'id' => $this->id,

            'parent_id' => $this->parent_id,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'slug' => $this->slug,

            'name' => $translation?->name,
            'short' => $translation?->short,
            'description' => $translation?->description,

            'meta_title' => $translation?->meta_title,
            'meta_keywords' => $translation?->meta_keywords,
            'meta_desc' => $translation?->meta_desc,

            'views' => (int) $this->views,

            'translations' => SchoolTrackTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'images' => SchoolTrackImageResource::collection(
                $this->whenLoaded('images')
            ),

            'parent' => new SchoolTrackSharedResource(
                $this->whenLoaded('parent')
            ),

            'children' => SchoolTrackSharedResource::collection(
                $this->whenLoaded('children')
            ),

            'children_recursive' => SchoolTrackSharedResource::collection(
                $this->whenLoaded('childrenRecursive')
            ),

            'courses' => $this->whenLoaded('courses', fn () => $this->courses->map(fn ($course) => [
                'id' => $course->id,
                'slug' => $course->slug,
                'title' => $course->translation?->title,
            ])),

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

            'is_root' => $this->parent_id === null,

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
