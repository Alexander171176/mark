<?php

namespace App\Http\Resources\Admin\School\SchoolModule;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SchoolModuleSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstImage = $this->whenLoaded('images', fn () => $this->images->first());

        $thumbnailUrl = !($firstImage instanceof MissingValue) && $firstImage
            ? ($firstImage->thumb_url ?? $firstImage->image_url ?? $firstImage->url ?? null)
            : null;

        return [
            'id' => $this->id,
            'school_course_id' => $this->school_course_id,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'subtitle' => $this->translation?->subtitle,
            'short' => $this->translation?->short,

            'activity' => (bool) $this->activity,
            'status' => $this->status,
            'availability' => $this->availability,
            'published_at' => optional($this->published_at)->format('Y-m-d'),

            'sort' => (int) $this->sort,

            'difficulty' => $this->difficulty !== null ? (int) $this->difficulty : null,
            'duration' => $this->duration !== null ? (int) $this->duration : null,

            'lessons_count' => (int) $this->lessons_count,
            'popularity' => (int) $this->popularity,
            'rating_count' => (int) $this->rating_count,
            'rating_avg' => $this->rating_avg !== null ? (float) $this->rating_avg : null,
            'views' => (int) $this->views,
            'likes' => (int) $this->likes,

            'thumbnail_url' => $thumbnailUrl,
        ];
    }
}
