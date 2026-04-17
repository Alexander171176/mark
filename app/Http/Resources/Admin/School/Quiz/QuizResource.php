<?php

namespace App\Http\Resources\Admin\School\Quiz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Представление квиза.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'course_id'          => $this->course_id,
            'module_id'          => $this->module_id,
            'lesson_id'          => $this->lesson_id,

            'locale'             => $this->locale,
            'title'              => $this->title,
            'slug'               => $this->slug,
            'short'              => $this->short,
            'description'        => $this->description,
            'type'               => $this->type, // graded|practice

            'attempts_limit'     => $this->attempts_limit,
            'time_limit_minutes' => $this->time_limit_minutes,
            'pass_score'         => $this->pass_score,

            'activity'           => (bool) $this->activity,
            'left'               => (bool) $this->left,
            'main'               => (bool) $this->main,
            'right'              => (bool) $this->right,

            'published_at'       => optional($this->published_at)->toISOString(),
            'created_at'         => optional($this->created_at)->toISOString(),
            'updated_at'         => optional($this->updated_at)->toISOString(),

            'course' => $this->whenLoaded('course', function () {
                $course = $this->course;

                if (!$course) {
                    return null;
                }

                return [
                    'id'    => $course->id,
                    'title' => $course->title,
                    'locale' => $course->locale,
                    'slug'  => $course->slug,
                ];
            }),

            'module' => $this->whenLoaded('module', function () {
                $module = $this->module;

                if (!$module) {
                    return null;
                }

                return [
                    'id'    => $module->id,
                    'title' => $module->title,
                    'locale' => $module->locale,
                    'slug'  => $module->slug,
                ];
            }),

            'lesson' => $this->whenLoaded('lesson', function () {
                $lesson = $this->lesson;

                if (!$lesson) {
                    return null;
                }

                return [
                    'id'    => $lesson->id,
                    'title' => $lesson->title,
                    'locale' => $lesson->locale,
                    'slug'  => $lesson->slug,
                ];
            }),

            // 🔹 вот здесь, наконец, используем QuizImageResource
            'images' => QuizImageResource::collection(
                $this->whenLoaded('images')
            ),

            'questions_count' => $this->whenCounted('questions', fn () => $this->questions_count),
            'attempts_count'  => $this->whenCounted('attempts', fn () => $this->attempts_count),
        ];
    }
}
