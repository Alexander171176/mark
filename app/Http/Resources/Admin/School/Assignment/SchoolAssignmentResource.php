<?php

namespace App\Http\Resources\Admin\School\Assignment;

use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\InstructorProfile\SchoolInstructorProfileResource;
use App\Http\Resources\Admin\School\Lesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\Module\SchoolModuleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'school_course_id' => $this->school_course_id,
            'school_module_id' => $this->school_module_id,
            'school_lesson_id' => $this->school_lesson_id,
            'school_instructor_profile_id' => $this->school_instructor_profile_id,

            'slug' => $this->slug,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            'title' => $this->translation?->title,
            'subtitle' => $this->translation?->subtitle,
            'short' => $this->translation?->short,
            'description' => $this->translation?->description,
            'instructions' => $this->translation?->instructions,

            'published_at' => optional($this->published_at)->format('Y-m-d\TH:i'),
            'due_at' => optional($this->due_at)->format('Y-m-d\TH:i'),

            'status' => $this->status,
            'visibility' => $this->visibility,
            'attempts_limit' => (int) $this->attempts_limit,
            'grading_type' => $this->grading_type,
            'max_score' => (int) $this->max_score,

            'is_overdue' => (bool) $this->is_overdue,

            'primary_image' => $this->whenLoaded(
                'images',
                fn () => $this->primary_image
                    ? new SchoolAssignmentImageResource($this->primary_image)
                    : null
            ),

            'images' => SchoolAssignmentImageResource::collection(
                $this->whenLoaded('images')
            ),

            'translations' => SchoolAssignmentTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'course' => new SchoolCourseSharedResource(
                $this->whenLoaded('course')
            ),

            'module' => new SchoolModuleSharedResource(
                $this->whenLoaded('module')
            ),

            'lesson' => new SchoolLessonSharedResource(
                $this->whenLoaded('lesson')
            ),

            'instructor' => new SchoolInstructorProfileResource(
                $this->whenLoaded('instructor')
            ),

            'submissions' => $this->whenLoaded('submissions', fn () => $this->submissions->map(fn ($submission) => [
                'id' => $submission->id,
                'user_id' => $submission->user_id,
                'status' => $submission->status,
                'score' => $submission->score !== null ? (string) $submission->score : null,
                'submitted_at' => optional($submission->submitted_at)->toIso8601String(),
                'graded_at' => optional($submission->graded_at)->toIso8601String(),
            ])),

            'submissions_count' => $this->when(isset($this->submissions_count), fn () => (int) $this->submissions_count),
            'images_count' => $this->when(isset($this->images_count), fn () => (int) $this->images_count),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
