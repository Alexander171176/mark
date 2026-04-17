<?php

namespace App\Http\Resources\Admin\School\AssignmentSubmission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentSubmissionResource extends JsonResource
{
    /**
     * Представление работы по заданию.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,

            // Привязки
            'assignment_id' => $this->assignment_id,
            'lesson_id'     => $this->lesson_id,
            'user_id'       => $this->user_id,
            'graded_by'     => $this->graded_by,

            // Контент
            'content'       => $this->content,
            'attachments'   => $this->attachments, // уже cast: array|null

            // Проверка/оценка
            'status'        => $this->status,
            'score'         => is_null($this->score) ? null : (float)$this->score,
            'review_comment'=> $this->review_comment,

            // Тайминги
            'submitted_at'  => optional($this->submitted_at)?->toISOString(),
            'graded_at'     => optional($this->graded_at)?->toISOString(),
            'created_at'    => optional($this->created_at)?->toISOString(),
            'updated_at'    => optional($this->updated_at)?->toISOString(),
            'deleted_at'    => optional($this->deleted_at)?->toISOString(),

            // Удобные флаги
            'is_submitted'     => $this->status === 'submitted',
            'is_under_review'  => $this->status === 'under_review',
            'is_graded'        => $this->status === 'graded',
            'needs_changes'    => $this->status === 'needs_changes',

            // Вложенные сущности (по мере подгрузки)
            'assignment' => $this->whenLoaded('assignment', function () {
                return [
                    'id'    => $this->assignment->id,
                    'title' => $this->assignment->title,
                    'slug'  => $this->assignment->slug,
                ];
            }),
            'lesson' => $this->whenLoaded('lesson', function () {
                return [
                    'id'    => $this->lesson->id,
                    'title' => $this->lesson->title,
                    'slug'  => $this->lesson->slug,
                ];
            }),
            'student' => $this->whenLoaded('student', function () {
                return [
                    'id'    => $this->student->id,
                    'name'  => $this->student->name,
                    'email' => $this->student->email,
                ];
            }),
            'grader' => $this->whenLoaded('grader', function () {
                return [
                    'id'    => $this->grader->id,
                    'name'  => $this->grader->name,
                    'email' => $this->grader->email,
                ];
            }),
        ];
    }
}
