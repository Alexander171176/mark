<?php

namespace App\Http\Resources\Admin\School\CourseSchedule;

use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\InstructorProfile\SchoolInstructorProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCourseScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_course_id' => $this->school_course_id,
            'school_instructor_profile_id' => $this->school_instructor_profile_id,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'subtitle' => $this->translation?->subtitle,
            'short' => $this->translation?->short,
            'description' => $this->translation?->description,

            'meta_title' => $this->translation?->meta_title,
            'meta_keywords' => $this->translation?->meta_keywords,
            'meta_desc' => $this->translation?->meta_desc,

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
            'notes' => $this->notes,

            'is_enrollment_open' => (bool) $this->is_enrollment_open,

            'primary_image' => $this->whenLoaded(
                'images',
                fn () => $this->primary_image
                    ? new SchoolCourseScheduleImageResource($this->primary_image)
                    : null
            ),

            'images' => SchoolCourseScheduleImageResource::collection(
                $this->whenLoaded('images')
            ),

            'translations' => SchoolCourseScheduleTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'course' => new SchoolCourseSharedResource(
                $this->whenLoaded('course')
            ),

            'instructor' => new SchoolInstructorProfileResource(
                $this->whenLoaded('instructor')
            ),

            'cohort_enrollments' => $this->whenLoaded('cohortEnrollments', fn () => $this->cohortEnrollments->map(fn ($enrollment) => [
                'id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'status' => $enrollment->status,
                'enrolled_at' => optional($enrollment->enrolled_at)->toIso8601String(),

                'user' => $enrollment->relationLoaded('user') && $enrollment->user
                    ? [
                        'id' => $enrollment->user->id,
                        'name' => $enrollment->user->name,
                        'email' => $enrollment->user->email,
                    ]
                    : null,
            ])),

            'cohort_enrollments_count' => $this->when(
                isset($this->cohort_enrollments_count),
                fn () => (int) $this->cohort_enrollments_count
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
