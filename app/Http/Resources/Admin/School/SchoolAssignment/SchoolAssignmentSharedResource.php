<?php

namespace App\Http\Resources\Admin\School\SchoolAssignment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SchoolAssignmentSharedResource extends JsonResource
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
            'school_module_id' => $this->school_module_id,
            'school_lesson_id' => $this->school_lesson_id,
            'school_instructor_profile_id' => $this->school_instructor_profile_id,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'subtitle' => $this->translation?->subtitle,
            'short' => $this->translation?->short,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            'status' => $this->status,
            'visibility' => $this->visibility,
            'grading_type' => $this->grading_type,

            'attempts_limit' => (int) $this->attempts_limit,
            'max_score' => (int) $this->max_score,

            'published_at' => optional($this->published_at)->format('Y-m-d'),
            'due_at' => optional($this->due_at)->format('Y-m-d\TH:i'),

            'is_overdue' => (bool) $this->is_overdue,

            'thumbnail_url' => $thumbnailUrl,
        ];
    }
}
