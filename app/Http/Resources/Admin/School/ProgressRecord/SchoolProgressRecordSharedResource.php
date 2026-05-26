<?php

namespace App\Http\Resources\Admin\School\ProgressRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolProgressRecordSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'user_id' => $this->user_id,
            'school_enrollment_id' => $this->school_enrollment_id,
            'school_course_id' => $this->school_course_id,
            'school_module_id' => $this->school_module_id,
            'school_lesson_id' => $this->school_lesson_id,

            'status' => $this->status,
            'progress_percent' => (int) $this->progress_percent,
            'progress_ratio' => (float) $this->progress_ratio,
            'time_spent_seconds' => (int) $this->time_spent_seconds,

            'last_viewed_at' => optional($this->last_viewed_at)->toIso8601String(),
            'completed_at' => optional($this->completed_at)->toIso8601String(),
        ];
    }
}
