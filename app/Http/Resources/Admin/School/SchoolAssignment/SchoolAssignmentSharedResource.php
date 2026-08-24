<?php

namespace App\Http\Resources\Admin\School\SchoolAssignment;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolAssignmentSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Admin Index заранее загружает
         * только выбранную локаль.
         */
        $translation = $this->relationLoaded('translations')
            ? $this->translations->first()
            : null;

        /**
         * Первое изображение соответствует
         * pivot order связи images().
         */
        $primaryImage = $this->relationLoaded('images')
            ? $this->images->first()
            : null;

        return [
            'id' => $this->id,

            'school_course_id' => $this->school_course_id,
            'school_module_id' => $this->school_module_id,
            'school_lesson_id' => $this->school_lesson_id,
            'school_instructor_profile_id' => $this->school_instructor_profile_id,

            /**
             * Основные поля.
             */
            'slug' => $this->slug,
            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            /**
             * Позиции публичных блоков.
             */
            'left' => (bool) $this->left,
            'main' => (bool) $this->main,
            'right' => (bool) $this->right,

            /**
             * Перевод выбранной локали.
             *
             * description/instructions нужны
             * frontend-поиску.
             */
            'translation' => $translation
                ? [
                    'locale' => $translation->locale,
                    'title' => $translation->title,
                    'subtitle' => $translation->subtitle,
                    'short' => $translation->short,
                    'description' => $translation->description,
                    'instructions' => $translation->instructions,
                ]
                : null,

            /**
             * Публикация.
             */
            'published_at' => $this->published_at?->toISOString(),
            'due_at' => $this->due_at?->toISOString(),

            /**
             * Состояние задания.
             */
            'status' => $this->status,
            'visibility' => $this->visibility,
            'grading_type' => $this->grading_type,

            'attempts_limit' => (int) $this->attempts_limit,
            'max_score' => (int) $this->max_score,

            'is_overdue' => (bool) $this->is_overdue,

            /**
             * Изображения.
             *
             * Controller загружает images.media.
             */
            'primary_image' => $primaryImage
                ? new SchoolAssignmentImageResource($primaryImage)
                : null,

            'images' => SchoolAssignmentImageResource::collection(
                $this->whenLoaded('images')
            ),

            /**
             * Связанные сущности.
             */
            'course' => new SchoolCourseSharedResource(
                $this->whenLoaded('course')
            ),

            'module' => new SchoolModuleSharedResource(
                $this->whenLoaded('module')
            ),

            'lesson' => new SchoolLessonSharedResource(
                $this->whenLoaded('lesson')
            ),

            'instructor' => new SchoolInstructorProfileSharedResource(
                $this->whenLoaded('instructor')
            ),

            /**
             * Counts.
             */
            'submissions_count' => $this->when(
                isset($this->submissions_count),
                fn () => (int) $this->submissions_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            /**
             * Даты нужны frontend-сортировке.
             */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
