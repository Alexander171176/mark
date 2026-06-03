<?php

namespace App\Http\Resources\Admin\School\SchoolAssignmentSubmission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolAssignmentSubmissionSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'school_assignment_id' => $this->school_assignment_id,
            'school_lesson_id' => $this->school_lesson_id,
            'user_id' => $this->user_id,

            'status' => $this->status,
            'score' => $this->score !== null ? (float) $this->score : null,

            'is_submitted' => $this->status === 'submitted',
            'is_under_review' => $this->status === 'under_review',
            'is_graded' => $this->status === 'graded',
            'needs_changes' => $this->status === 'needs_changes',

            'submitted_at' => optional($this->submitted_at)->toIso8601String(),
            'graded_at' => optional($this->graded_at)->toIso8601String(),
        ];
    }
}
