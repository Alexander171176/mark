<?php

namespace App\Http\Resources\Admin\School\Quiz;

use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\Lesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\Module\SchoolModuleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'school_course_id' => $this->school_course_id,
            'school_module_id' => $this->school_module_id,
            'school_lesson_id' => $this->school_lesson_id,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'short' => $this->translation?->short,
            'description' => $this->translation?->description,

            'type' => $this->type,

            'attempts_limit' => (int) $this->attempts_limit,
            'time_limit_minutes' => $this->time_limit_minutes !== null
                ? (int) $this->time_limit_minutes
                : null,
            'pass_score' => (int) $this->pass_score,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            'published_at' => optional($this->published_at)->toIso8601String(),

            'primary_image' => $this->whenLoaded(
                'images',
                fn () => $this->primary_image
                    ? new SchoolQuizImageResource($this->primary_image)
                    : null
            ),

            'images' => SchoolQuizImageResource::collection(
                $this->whenLoaded('images')
            ),

            'translations' => SchoolQuizTranslationResource::collection(
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

            'questions' => $this->whenLoaded('questions', fn () => $this->questions->map(fn ($question) => [
                'id' => $question->id,
                'school_quiz_id' => $question->school_quiz_id,
                'sort' => (int) $question->sort,
                'question_type' => $question->question_type,
                'points' => (int) $question->points,
                'activity' => (bool) $question->activity,
                'question_text' => $question->translation?->question_text,
            ])),

            'attempts' => $this->whenLoaded('attempts', fn () => $this->attempts->map(fn ($attempt) => [
                'id' => $attempt->id,
                'user_id' => $attempt->user_id,
                'attempt_number' => (int) $attempt->attempt_number,
                'score' => (int) $attempt->score,
                'max_score' => (int) $attempt->max_score,
                'percent' => (int) $attempt->percent,
                'status' => $attempt->status,
                'started_at' => optional($attempt->started_at)->toIso8601String(),
                'finished_at' => optional($attempt->finished_at)->toIso8601String(),
            ])),

            'questions_count' => $this->when(
                isset($this->questions_count),
                fn () => (int) $this->questions_count
            ),

            'attempts_count' => $this->when(
                isset($this->attempts_count),
                fn () => (int) $this->attempts_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
