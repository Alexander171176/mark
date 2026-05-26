<?php

namespace App\Http\Resources\Admin\School\Module;

use App\Http\Resources\Admin\School\Course\SchoolCourseSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_course_id' => $this->school_course_id,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'subtitle' => $this->translation?->subtitle,
            'short' => $this->translation?->short,
            'description' => $this->translation?->description,

            'meta_title' => $this->translation?->meta_title,
            'meta_keywords' => $this->translation?->meta_keywords,
            'meta_desc' => $this->translation?->meta_desc,

            'published_at' => optional($this->published_at)->format('Y-m-d'),

            'status' => $this->status,
            'availability' => $this->availability,

            'difficulty' => $this->difficulty !== null ? (int) $this->difficulty : null,
            'duration' => $this->duration !== null ? (int) $this->duration : null,

            'lessons_count' => (int) $this->lessons_count,
            'popularity' => (int) $this->popularity,
            'rating_count' => (int) $this->rating_count,
            'rating_avg' => $this->rating_avg !== null ? (float) $this->rating_avg : null,
            'views' => (int) $this->views,
            'likes' => (int) $this->likes,

            'already_liked' => auth()->check()
                ? $this->likes()->where('user_id', auth()->id())->exists()
                : false,

            'primary_image' => $this->whenLoaded(
                'images',
                fn () => $this->primary_image
                    ? new SchoolModuleImageResource($this->primary_image)
                    : null
            ),

            'images' => SchoolModuleImageResource::collection(
                $this->whenLoaded('images')
            ),

            'translations' => SchoolModuleTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'course' => new SchoolCourseSharedResource(
                $this->whenLoaded('course')
            ),

            'lessons' => $this->whenLoaded('lessons', fn () => $this->lessons->map(fn ($lesson) => [
                'id' => $lesson->id,
                'school_module_id' => $lesson->school_module_id,
                'slug' => $lesson->slug,
                'title' => $lesson->translation?->title,
                'subtitle' => $lesson->translation?->subtitle,
                'short' => $lesson->translation?->short,
                'sort' => (int) $lesson->sort,
                'activity' => (bool) $lesson->activity,
                'status' => $lesson->status,
                'availability' => $lesson->availability,
                'access_type' => $lesson->access_type,
                'difficulty' => $lesson->difficulty !== null ? (int) $lesson->difficulty : null,
                'duration' => $lesson->duration !== null ? (int) $lesson->duration : null,
                'views' => (int) $lesson->views,
                'likes' => (int) $lesson->likes,
            ])),

            'likes_count' => $this->when(isset($this->likes_count), fn () => (int) $this->likes_count),
            'images_count' => $this->when(isset($this->images_count), fn () => (int) $this->images_count),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
