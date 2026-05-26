<?php

namespace App\Http\Resources\Admin\School\Enrollment;

use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\CourseSchedule\SchoolCourseScheduleSharedResource;
use App\Http\Resources\Admin\School\Order\SchoolOrderSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolEnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'user_id' => $this->user_id,
            'school_course_id' => $this->school_course_id,
            'school_course_schedule_id' => $this->school_course_schedule_id,
            'school_order_id' => $this->school_order_id,

            'status' => $this->status,

            'started_at' => optional($this->started_at)->toIso8601String(),
            'expires_at' => optional($this->expires_at)->toIso8601String(),
            'completed_at' => optional($this->completed_at)->toIso8601String(),

            'progress_percent' => (int) $this->progress_percent,
            'notes' => $this->notes,
            'meta' => $this->meta,

            'is_accessible' => (bool) $this->is_accessible,
            'days_left' => $this->days_left,

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),

            'course' => new SchoolCourseSharedResource(
                $this->whenLoaded('course')
            ),

            'schedule' => new SchoolCourseScheduleSharedResource(
                $this->whenLoaded('schedule')
            ),

            'order' => new SchoolOrderSharedResource(
                $this->whenLoaded('order')
            ),

            'progress_records' => $this->whenLoaded('progressRecords', fn () => $this->progressRecords->map(fn ($record) => [
                'id' => $record->id,
                'school_course_id' => $record->school_course_id,
                'school_module_id' => $record->school_module_id,
                'school_lesson_id' => $record->school_lesson_id,
                'status' => $record->status,
                'progress_percent' => (int) $record->progress_percent,
                'time_spent_seconds' => (int) $record->time_spent_seconds,
                'last_viewed_at' => optional($record->last_viewed_at)->toIso8601String(),
                'completed_at' => optional($record->completed_at)->toIso8601String(),
            ])),

            'certificate' => $this->whenLoaded('certificate', fn () => $this->certificate ? [
                'id' => $this->certificate->id,
                'number' => $this->certificate->number,
                'verification_code' => $this->certificate->verification_code,
                'status' => $this->certificate->status,
                'issued_at' => optional($this->certificate->issued_at)->toIso8601String(),
                'expires_at' => optional($this->certificate->expires_at)->toIso8601String(),
            ] : null),

            'progress_records_count' => $this->when(
                isset($this->progress_records_count),
                fn () => (int) $this->progress_records_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
