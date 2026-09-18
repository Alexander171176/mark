<?php

namespace App\Http\Resources\Public\School\SchoolAssignment;

use App\Http\Resources\Admin\School\SchoolAssignment\SchoolAssignmentImageResource;
use App\Http\Resources\Public\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Public\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use App\Http\Resources\Public\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Public\School\SchoolModule\SchoolModuleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolAssignmentResource extends JsonResource
{
    /**
     * Полное представление задания
     * для Public Show.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $this->relationLoaded(
            'translations'
        )
            ? $this->translationOrFallback(
                $locale,
                $fallbackLocale
            )
            : null;

        return [
            'id' => $this->id,

            'school_course_id' =>
                $this->school_course_id,

            'school_module_id' =>
                $this->school_module_id,

            'school_lesson_id' =>
                $this->school_lesson_id,

            'school_instructor_profile_id' =>
                $this->school_instructor_profile_id,

            'slug' => $this->slug,
            'sort' => $this->sort,

            'status' => $this->status,
            'visibility' => $this->visibility,

            'attempts_limit' =>
                $this->attempts_limit,

            'grading_type' =>
                $this->grading_type,

            'max_score' =>
                $this->max_score,

            'due_at' =>
                $this->due_at?->toISOString(),

            'published_at' =>
                $this->published_at?->toISOString(),

            'is_overdue' =>
                $this->is_overdue,

            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'title' =>
                        $translation->title,

                    'subtitle' =>
                        $translation->subtitle,

                    'short' =>
                        $translation->short,

                    'description' =>
                        $translation->description,

                    'instructions' =>
                        $translation->instructions,

                    'meta_title' =>
                        $translation->meta_title,

                    'meta_keywords' =>
                        $translation->meta_keywords,

                    'meta_description' =>
                        $translation->meta_description,
                ]
                : null,

            'course' =>
                SchoolCourseSharedResource::make(
                    $this->whenLoaded('course')
                ),

            'module' =>
                SchoolModuleSharedResource::make(
                    $this->whenLoaded('module')
                ),

            'lesson' =>
                SchoolLessonSharedResource::make(
                    $this->whenLoaded('lesson')
                ),

            'instructor' =>
                SchoolInstructorProfileSharedResource::make(
                    $this->whenLoaded('instructor')
                ),

            'images' =>
                SchoolAssignmentImageResource::collection(
                    $this->whenLoaded('images')
                ),

            'submissions_count' =>
                $this->whenCounted(
                    'submissions'
                ),

            'images_count' =>
                $this->whenCounted(
                    'images'
                ),

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
