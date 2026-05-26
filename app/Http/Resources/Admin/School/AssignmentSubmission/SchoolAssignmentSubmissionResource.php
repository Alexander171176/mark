<?php

namespace App\Http\Resources\Admin\School\AssignmentSubmission;

use App\Http\Resources\Admin\School\Assignment\SchoolAssignmentSharedResource;
use App\Http\Resources\Admin\School\Lesson\SchoolLessonSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolAssignmentSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'school_assignment_id' => $this->school_assignment_id,
            'school_lesson_id' => $this->school_lesson_id,
            'user_id' => $this->user_id,
            'graded_by' => $this->graded_by,

            'content' => $this->content,
            'attachments' => $this->attachments,

            'status' => $this->status,
            'score' => $this->score !== null ? (float) $this->score : null,
            'review_comment' => $this->review_comment,

            'submitted_at' => optional($this->submitted_at)->toIso8601String(),
            'graded_at' => optional($this->graded_at)->toIso8601String(),

            'is_submitted' => $this->status === 'submitted',
            'is_under_review' => $this->status === 'under_review',
            'is_graded' => $this->status === 'graded',
            'needs_changes' => $this->status === 'needs_changes',

            'assignment' => new SchoolAssignmentSharedResource(
                $this->whenLoaded('assignment')
            ),

            'lesson' => new SchoolLessonSharedResource(
                $this->whenLoaded('lesson')
            ),

            'student' => $this->whenLoaded('student', fn () => [
                'id' => $this->student->id,
                'name' => $this->student->name,
                'email' => $this->student->email,
            ]),

            'grader' => $this->whenLoaded('grader', fn () => [
                'id' => $this->grader->id,
                'name' => $this->grader->name,
                'email' => $this->grader->email,
            ]),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
