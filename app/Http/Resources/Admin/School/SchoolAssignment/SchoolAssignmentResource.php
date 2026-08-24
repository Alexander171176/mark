<?php

namespace App\Http\Resources\Admin\School\SchoolAssignment;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolAssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        $translation = $this->relationLoaded('translations')
            ? (
            $this->translations->firstWhere(
                'locale',
                $locale
            )
                ?: $this->translations->firstWhere(
                'locale',
                $fallbackLocale
            )
                ?: $this->translations->first()
            )
            : null;

        return [
            'id' =>
                $this->id,

            'school_course_id' =>
                $this->school_course_id,

            'school_module_id' =>
                $this->school_module_id,

            'school_lesson_id' =>
                $this->school_lesson_id,

            'school_instructor_profile_id' =>
                $this->school_instructor_profile_id,

            /**
             * Основные поля.
             */
            'slug' =>
                $this->slug,

            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,

            /**
             * Позиции.
             */
            'left' =>
                (bool) $this->left,

            'main' =>
                (bool) $this->main,

            'right' =>
                (bool) $this->right,

            /**
             * Публикация.
             */
            'published_at' =>
                $this->published_at?->format(
                    'Y-m-d\TH:i'
                ),

            'due_at' =>
                $this->due_at?->format(
                    'Y-m-d\TH:i'
                ),

            /**
             * Состояние задания.
             */
            'status' =>
                $this->status,

            'visibility' =>
                $this->visibility,

            'attempts_limit' =>
                (int) $this->attempts_limit,

            'grading_type' =>
                $this->grading_type,

            'max_score' =>
                (int) $this->max_score,

            'is_overdue' =>
                (bool) $this->is_overdue,

            /**
             * Текущий перевод.
             *
             * Только из уже загруженной
             * коллекции translations.
             */
            'translation' => $translation
                ? new SchoolAssignmentTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы нужны Edit.
             */
            'translations' =>
                SchoolAssignmentTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            /**
             * Изображения.
             *
             * Controller загружает images.media.
             */
            'images' =>
                SchoolAssignmentImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Связанные сущности.
             */
            'course' =>
                new SchoolCourseSharedResource(
                    $this->whenLoaded(
                        'course'
                    )
                ),

            'module' =>
                new SchoolModuleSharedResource(
                    $this->whenLoaded(
                        'module'
                    )
                ),

            'lesson' =>
                new SchoolLessonSharedResource(
                    $this->whenLoaded(
                        'lesson'
                    )
                ),

            'instructor' =>
                new SchoolInstructorProfileSharedResource(
                    $this->whenLoaded(
                        'instructor'
                    )
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

            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
