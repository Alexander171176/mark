<?php

namespace App\Http\Resources\Admin\School\SchoolLesson;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SchoolLessonSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstImage = $this->whenLoaded('images', fn () => $this->images->first());

        $thumbnailUrl = !($firstImage instanceof MissingValue) && $firstImage
            ? ($firstImage->thumb_url ?? $firstImage->image_url ?? $firstImage->url ?? null)
            : null;

        return [
            'id' => $this->id,
            'school_module_id' => $this->school_module_id,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'subtitle' => $this->translation?->subtitle,
            'short' => $this->translation?->short,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'status' => $this->status,
            'availability' => $this->availability,
            'published_at' => optional($this->published_at)->format('Y-m-d'),

            'access_type' => $this->access_type,
            'difficulty' => $this->difficulty !== null ? (int) $this->difficulty : null,
            'duration' => $this->duration !== null ? (int) $this->duration : null,

            'preview_mode' => $this->preview_mode,
            'preview_value' => $this->preview_value !== null ? (int) $this->preview_value : null,

            'popularity' => (int) $this->popularity,
            'rating_count' => (int) $this->rating_count,
            'rating_avg' => $this->rating_avg !== null ? (float) $this->rating_avg : null,
            'views' => (int) $this->views,
            'likes' => (int) $this->likes,

            'thumbnail_url' => $thumbnailUrl,
        ];
    }
}
