<?php

namespace App\Http\Resources\Admin\School\Quiz;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SchoolQuizSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstImage = $this->whenLoaded('images', fn () => $this->images->first());

        $thumbnailUrl = !($firstImage instanceof MissingValue) && $firstImage
            ? ($firstImage->thumb_url ?? $firstImage->image_url ?? $firstImage->url ?? null)
            : null;

        return [
            'id' => $this->id,

            'school_course_id' => $this->school_course_id,
            'school_module_id' => $this->school_module_id,
            'school_lesson_id' => $this->school_lesson_id,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'short' => $this->translation?->short,

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

            'published_at' => optional($this->published_at)->format('Y-m-d'),

            'thumbnail_url' => $thumbnailUrl,

            'course' => $this->whenLoaded('course', fn () => [
                'id' => $this->course->id,
                'title' => $this->course->translation?->title,
            ]),

            'module' => $this->whenLoaded('module', fn () => [
                'id' => $this->module->id,
                'title' => $this->module->translation?->title,
            ]),

            'lesson' => $this->whenLoaded('lesson', fn () => [
                'id' => $this->lesson->id,
                'title' => $this->lesson->translation?->title,
            ]),
        ];
    }
}
