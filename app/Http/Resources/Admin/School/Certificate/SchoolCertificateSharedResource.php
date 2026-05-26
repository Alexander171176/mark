<?php

namespace App\Http\Resources\Admin\School\Certificate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCertificateSharedResource extends JsonResource
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

            'status' => $this->status,
            'score' => $this->score,
            'hours' => $this->hours !== null ? (string) $this->hours : null,

            'is_expired' => (bool) $this->is_expired,
            'is_revoked' => (bool) $this->is_revoked,
        ];
    }
}
