<?php

namespace App\Http\Resources\Admin\School\Assignment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    /**
     * Представление задания.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'course_id'              => $this->course_id,
            'module_id'              => $this->module_id,
            'lesson_id'              => $this->lesson_id,
            'instructor_profile_id'  => $this->instructor_profile_id,

            'locale'                 => $this->locale,
            'title'                  => $this->title,
            'slug'                   => $this->slug,
            'subtitle'               => $this->subtitle,
            'short'                  => $this->short,
            'description'            => $this->description,
            'instructions'           => $this->instructions,

            'max_score'              => (int) $this->max_score,
            'attempts_limit'         => (int) $this->attempts_limit,
            'grading_type'           => $this->grading_type,
            'status'                 => $this->status,
            'visibility'             => $this->visibility,
            'activity'               => (bool) $this->activity,
            'left'                   => (bool) $this->left,
            'main'                   => (bool) $this->main,
            'right'                  => (bool) $this->right,

            'due_at'                 => $this->due_at?->format('Y-m-d\TH:i'),
            'sort'                   => (int) $this->sort,
            'published_at'           => $this->published_at?->format('Y-m-d\TH:i'),

            'created_at'             => optional($this->created_at)?->toISOString(),
            'updated_at'             => optional($this->updated_at)?->toISOString(),
            'deleted_at'             => optional($this->deleted_at)?->toISOString(),

            'course' => $this->whenLoaded('course', function () {
                if (!$this->course) {
                    return null;
                }

                return [
                    'id'    => $this->course->id,
                    'title' => $this->course->title,
                    'slug'  => $this->course->slug,
                ];
            }),

            'module' => $this->whenLoaded('module', function () {
                if (!$this->module) {
                    return null;
                }

                return [
                    'id'    => $this->module->id,
                    'title' => $this->module->title,
                    'slug'  => $this->module->slug,

                    'course' => $this->module->relationLoaded('course') && $this->module->course
                        ? [
                            'id'    => $this->module->course->id,
                            'title' => $this->module->course->title,
                            'slug'  => $this->module->course->slug,
                        ]
                        : null,
                ];
            }),

            'lesson' => $this->whenLoaded('lesson', function () {
                if (!$this->lesson) {
                    return null;
                }

                return [
                    'id'    => $this->lesson->id,
                    'title' => $this->lesson->title,
                    'slug'  => $this->lesson->slug,

                    'module' => $this->lesson->relationLoaded('module') && $this->lesson->module
                        ? [
                            'id'    => $this->lesson->module->id,
                            'title' => $this->lesson->module->title,
                            'slug'  => $this->lesson->module->slug,

                            'course' => $this->lesson->module->relationLoaded('course') && $this->lesson->module->course
                                ? [
                                    'id'    => $this->lesson->module->course->id,
                                    'title' => $this->lesson->module->course->title,
                                    'slug'  => $this->lesson->module->course->slug,
                                ]
                                : null,
                        ]
                        : null,
                ];
            }),

            'instructor' => $this->whenLoaded('instructor', function () {
                if (!$this->instructor) {
                    return null;
                }

                return [
                    'id'               => $this->instructor->id,
                    'title'            => $this->instructor->title,
                    'slug'             => $this->instructor->slug,
                    'public_name'      => $this->instructor->public_name ?? null,
                    'short'            => $this->instructor->short ?? null,
                    'bio'              => $this->instructor->bio ?? null,
                    'experience_years' => $this->instructor->experience_years ?? null,
                    'hourly_rate'      => $this->instructor->hourly_rate ?? null,
                    'rating_avg'       => $this->instructor->rating_avg ?? null,
                    'rating_count'     => $this->instructor->rating_count ?? null,

                    'user' => $this->instructor->relationLoaded('user') && $this->instructor->user
                        ? [
                            'id'    => $this->instructor->user->id,
                            'name'  => $this->instructor->user->name,
                            'email' => $this->instructor->user->email,
                        ]
                        : null,

                    'images' => $this->instructor->relationLoaded('images')
                        ? $this->instructor->images
                            ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                            ->map(function ($image) {
                                return [
                                    'id'        => $image->id,
                                    'order'     => $image->pivot->order ?? null,
                                    'alt'       => $image->alt ?? null,
                                    'caption'   => $image->caption ?? null,
                                    'url'       => $image->url ?? null,
                                    'webp_url'  => $image->webp_url ?? null,
                                    'image_url' => $image->image_url ?? null,
                                    'thumb_url' => $image->thumb_url ?? null,
                                ];
                            })
                            ->values()
                        : [],
                ];
            }),

            'images' => $this->whenLoaded('images', fn () =>
            AssignmentImageResource::collection($this->images)
            ),
        ];
    }
}
