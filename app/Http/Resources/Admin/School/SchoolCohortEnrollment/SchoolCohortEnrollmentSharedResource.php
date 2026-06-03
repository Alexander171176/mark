<?php

namespace App\Http\Resources\Admin\School\SchoolCohortEnrollment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCohortEnrollmentSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'school_course_schedule_id' => $this->school_course_schedule_id,
            'user_id' => $this->user_id,

            'status' => $this->status,
            'enrolled_at' => optional($this->enrolled_at)->toIso8601String(),

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
        ];
    }
}
