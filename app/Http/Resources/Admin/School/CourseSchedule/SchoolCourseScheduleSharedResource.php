<?php

namespace App\Http\Resources\Admin\School\CourseSchedule;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SchoolCourseScheduleSharedResource extends JsonResource
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
            'school_instructor_profile_id' => $this->school_instructor_profile_id,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'subtitle' => $this->translation?->subtitle,
            'short' => $this->translation?->short,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'starts_at' => optional($this->starts_at)->format('Y-m-d\TH:i'),
            'ends_at' => optional($this->ends_at)->format('Y-m-d\TH:i'),
            'enroll_starts_at' => optional($this->enroll_starts_at)->format('Y-m-d\TH:i'),
            'enroll_ends_at' => optional($this->enroll_ends_at)->format('Y-m-d\TH:i'),

            'capacity' => (int) $this->capacity,
            'is_online' => (bool) $this->is_online,

            'location' => $this->location,
            'meeting_url' => $this->meeting_url,
            'timezone' => $this->timezone,

            'status' => $this->status,
            'views' => (int) $this->views,

            'is_enrollment_open' => (bool) $this->is_enrollment_open,

            'thumbnail_url' => $thumbnailUrl,
        ];
    }
}
