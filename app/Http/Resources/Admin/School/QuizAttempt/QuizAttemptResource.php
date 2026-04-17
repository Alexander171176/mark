<?php

namespace App\Http\Resources\Admin\School\QuizAttempt;

use App\Http\Resources\Admin\School\QuizAttemptItem\QuizAttemptItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptResource extends JsonResource
{
    /**
     * Представление попытки прохождения квиза.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,

            'user_id'         => $this->user_id,
            'quiz_id'         => $this->quiz_id,
            'enrollment_id'   => $this->enrollment_id,
            'course_id'       => $this->course_id,
            'module_id'       => $this->module_id,
            'lesson_id'       => $this->lesson_id,

            'attempt_number'  => $this->attempt_number,
            'score'           => $this->score,
            'max_score'       => $this->max_score,
            'percent'         => $this->percent,
            'status'          => $this->status, // in_progress|completed|graded

            'started_at'      => $this->started_at?->toISOString(),
            'finished_at'     => $this->finished_at?->toISOString(),
            'duration_seconds'=> $this->duration_seconds,

            'ip_address'      => $this->ip_address,
            'user_agent'      => $this->user_agent,

            'created_at'  => $this->created_at?->toISOString(),
            'updated_at'  => $this->updated_at?->toISOString(),

            // Короткие сведения по связям (если заранее загружены)
            'quiz' => $this->whenLoaded('quiz', fn () => [
                'id'    => $this->quiz->id,
                'title' => $this->quiz->title,
                'slug'  => $this->quiz->slug,
            ]),
            'user' => $this->whenLoaded('user', fn () => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
                'email'=> $this->user->email,
            ]),
            'course' => $this->whenLoaded('course', fn () => [
                'id'    => $this->course->id,
                'title' => $this->course->title,
                'locale'=> $this->course->locale,
            ]),
            'module' => $this->whenLoaded('module', fn () => [
                'id'    => $this->module->id,
                'title' => $this->module->title,
                'locale'=> $this->module->locale,
            ]),
            'lesson' => $this->whenLoaded('lesson', fn () => [
                'id'    => $this->lesson->id,
                'title' => $this->lesson->title,
                'locale'=> $this->lesson->locale,
            ]),

            // Детализация ответов в попытке (если нужно)
            'items' => $this->whenLoaded('items', fn () =>
            QuizAttemptItemResource::collection($this->items)
            ),
        ];
    }
}
