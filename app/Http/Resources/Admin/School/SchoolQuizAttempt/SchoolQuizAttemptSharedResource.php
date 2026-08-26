<?php

namespace App\Http\Resources\Admin\School\SchoolQuizAttempt;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolEnrollment\SchoolEnrollmentSharedResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use App\Http\Resources\Admin\School\SchoolQuiz\SchoolQuizSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizAttemptSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' =>
                $this->id,

            'user_id' =>
                $this->user_id,

            'school_quiz_id' =>
                $this->school_quiz_id,

            'school_enrollment_id' =>
                $this->school_enrollment_id,

            'school_course_id' =>
                $this->school_course_id,

            'school_module_id' =>
                $this->school_module_id,

            'school_lesson_id' =>
                $this->school_lesson_id,

            'attempt_number' =>
                (int) $this->attempt_number,

            'score' =>
                (int) $this->score,

            'max_score' =>
                (int) $this->max_score,

            'percent' =>
                (int) $this->percent,

            'status' =>
                $this->status,

            'started_at' =>
                $this->started_at?->toISOString(),

            'finished_at' =>
                $this->finished_at?->toISOString(),

            'duration_seconds' =>
                (int) $this->duration_seconds,

            /**
             * Нужны frontend-поиску.
             */
            'ip_address' =>
                $this->ip_address,

            'user_agent' =>
                $this->user_agent,

            'user' => $this->whenLoaded(
                'user',
                fn () => [
                    'id' =>
                        $this->user->id,

                    'name' =>
                        $this->user->name,

                    'email' =>
                        $this->user->email,
                ]
            ),

            'quiz' =>
                new SchoolQuizSharedResource(
                    $this->whenLoaded('quiz')
                ),

            'enrollment' =>
                new SchoolEnrollmentSharedResource(
                    $this->whenLoaded('enrollment')
                ),

            'course' =>
                new SchoolCourseSharedResource(
                    $this->whenLoaded('course')
                ),

            'module' =>
                new SchoolModuleSharedResource(
                    $this->whenLoaded('module')
                ),

            'lesson' =>
                new SchoolLessonSharedResource(
                    $this->whenLoaded('lesson')
                ),

            /**
             * В Index сами items не нужны,
             * только count.
             */
            'items_count' => $this->when(
                isset($this->items_count),
                fn () => (int) $this->items_count
            ),

            /**
             * Нужны frontend-сортировке.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
