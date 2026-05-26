<?php

namespace App\Http\Resources\Admin\School\Certificate;

use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\Enrollment\SchoolEnrollmentSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'user_id' => $this->user_id,
            'school_course_id' => $this->school_course_id,
            'school_enrollment_id' => $this->school_enrollment_id,

            'number' => $this->number,
            'verification_code' => $this->verification_code,

            'issued_at' => optional($this->issued_at)->toIso8601String(),
            'expires_at' => optional($this->expires_at)->toIso8601String(),
            'revoked_at' => optional($this->revoked_at)->toIso8601String(),

            'score' => $this->score,
            'hours' => $this->hours !== null ? (string) $this->hours : null,

            'status' => $this->status,
            'name_on_certificate' => $this->name_on_certificate,
            'notes' => $this->notes,
            'meta' => $this->meta,

            'is_expired' => (bool) $this->is_expired,
            'is_revoked' => (bool) $this->is_revoked,

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),

            'course' => new SchoolCourseSharedResource(
                $this->whenLoaded('course')
            ),

            'enrollment' => new SchoolEnrollmentSharedResource(
                $this->whenLoaded('enrollment')
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
