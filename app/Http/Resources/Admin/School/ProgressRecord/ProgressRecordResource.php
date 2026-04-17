<?php

namespace App\Http\Resources\Admin\School\ProgressRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,

            // связи (id + короткая витрина, если подгружено)
            'user_id'             => $this->user_id,
            'user'                => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ]),

            'enrollment_id'       => $this->enrollment_id,
            'enrollment'          => $this->whenLoaded('enrollment', fn () => [
                'id'        => $this->enrollment->id,
                'course_id' => $this->enrollment->course_id,
                'status'    => $this->enrollment->status,
            ]),

            'course_id'           => $this->course_id,
            'course'              => $this->whenLoaded('course', fn () => [
                'id'    => $this->course->id,
                'title' => $this->course->title ?? null,
                'slug'  => $this->course->slug ?? null,
            ]),

            'module_id'           => $this->module_id,
            'module'              => $this->whenLoaded('module', fn () => [
                'id'    => $this->module->id,
                'title' => $this->module->title ?? null,
                'slug'  => $this->module->slug ?? null,
            ]),

            'lesson_id'           => $this->lesson_id,
            'lesson'              => $this->whenLoaded('lesson', fn () => [
                'id'    => $this->lesson->id,
                'title' => $this->lesson->title ?? null,
                'slug'  => $this->lesson->slug ?? null,
                'type'  => $this->lesson->type ?? null,
            ]),

            // прогресс
            'status'              => $this->status,
            'progress_percent'    => (int) $this->progress_percent,
            'progress_ratio'      => $this->when(isset($this->progress_ratio), fn () => $this->progress_ratio),
            'time_spent_seconds'  => (int) $this->time_spent_seconds,

            // тайминги
            'last_viewed_at'      => optional($this->last_viewed_at)?->toIso8601String(),
            'completed_at'        => optional($this->completed_at)?->toIso8601String(),

            // системные
            'created_at'          => optional($this->created_at)?->toIso8601String(),
            'updated_at'          => optional($this->updated_at)?->toIso8601String(),
        ];
    }
}
