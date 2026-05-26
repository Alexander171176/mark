<?php

namespace App\Http\Resources\Admin\School\CohortEnrollment;

use App\Http\Resources\Admin\School\CourseSchedule\SchoolCourseScheduleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCohortEnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'school_course_schedule_id' => $this->school_course_schedule_id,
            'user_id' => $this->user_id,

            'status' => $this->status,
            'enrolled_at' => optional($this->enrolled_at)->toIso8601String(),
            'notes' => $this->notes,

            'schedule' => new SchoolCourseScheduleSharedResource(
                $this->whenLoaded('schedule')
            ),

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
