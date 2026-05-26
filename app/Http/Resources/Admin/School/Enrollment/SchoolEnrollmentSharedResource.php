<?php

namespace App\Http\Resources\Admin\School\Enrollment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolEnrollmentSharedResource extends JsonResource
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
            'progress_percent' => (int) $this->progress_percent,

            'started_at' => optional($this->started_at)->toIso8601String(),
            'expires_at' => optional($this->expires_at)->toIso8601String(),
            'completed_at' => optional($this->completed_at)->toIso8601String(),

            'is_accessible' => (bool) $this->is_accessible,
            'days_left' => $this->days_left,
        ];
    }
}
