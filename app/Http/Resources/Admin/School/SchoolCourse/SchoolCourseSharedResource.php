<?php

namespace App\Http\Resources\Admin\School\SchoolCourse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SchoolCourseSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstImage = $this->whenLoaded('images', fn () => $this->images->first());

        $thumbnailUrl = !($firstImage instanceof MissingValue) && $firstImage
            ? ($firstImage->thumb_url ?? $firstImage->image_url ?? $firstImage->url ?? null)
            : null;

        return [
            'id' => $this->id,
            'school_instructor_profile_id' => $this->school_instructor_profile_id,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'subtitle' => $this->translation?->subtitle,
            'short' => $this->translation?->short,

            'activity' => (bool) $this->activity,
            'status' => $this->status,
            'availability' => $this->availability,
            'published_at' => optional($this->published_at)->format('Y-m-d'),

            'sort' => (int) $this->sort,
            'views' => (int) $this->views,
            'likes' => (int) $this->likes,

            'rating_avg' => $this->rating_avg !== null ? (float) $this->rating_avg : null,
            'rating_count' => (int) $this->rating_count,
            'students_count' => (int) $this->students_count,
            'popularity' => (int) $this->popularity,

            'duration' => $this->duration !== null ? (int) $this->duration : null,
            'level' => $this->level,
            'difficulty' => $this->difficulty !== null ? (int) $this->difficulty : null,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            'is_new' => (bool) $this->is_new,
            'is_hit' => (bool) $this->is_hit,
            'is_sale' => (bool) $this->is_sale,

            'thumbnail_url' => $thumbnailUrl,
        ];
    }
}
