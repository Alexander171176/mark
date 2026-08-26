<?php

namespace App\Http\Resources\Admin\School\SchoolQuiz;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolLesson\SchoolLessonSharedResource;
use App\Http\Resources\Admin\School\SchoolModule\SchoolModuleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolQuizSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /**
         * Admin Index заранее загружает
         * только выбранную локаль.
         */
        $translation = $this->relationLoaded(
            'translations'
        )
            ? $this->translations->first()
            : null;

        /**
         * Первое изображение уже соответствует
         * pivot order связи images().
         */
        $primaryImage = $this->relationLoaded(
            'images'
        )
            ? $this->images->first()
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

            'slug' =>
                $this->slug,

            /**
             * Перевод выбранной локали.
             */
            'translation' => $translation
                ? [
                    'locale' =>
                        $translation->locale,

                    'title' =>
                        $translation->title,

                    'short' =>
                        $translation->short,

                    /**
                     * Нужен frontend-поиску.
                     */
                    'description' =>
                        $translation->description,
                ]
                : null,

            'type' =>
                $this->type,

            'attempts_limit' =>
                (int) $this->attempts_limit,

            'time_limit_minutes' =>
                $this->time_limit_minutes !== null
                    ? (int) $this->time_limit_minutes
                    : null,

            'pass_score' =>
                (int) $this->pass_score,

            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,

            'left' =>
                (bool) $this->left,

            'main' =>
                (bool) $this->main,

            'right' =>
                (bool) $this->right,

            'published_at' =>
                $this->published_at?->format(
                    'Y-m-d'
                ),

            /**
             * Изображения.
             *
             * Controller обязан загрузить
             * images.media.
             */
            'primary_image' => $primaryImage
                ? new SchoolQuizImageResource(
                    $primaryImage
                )
                : null,

            'images' =>
                SchoolQuizImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Иерархия.
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

            /**
             * Counts нужны Index
             * и frontend-сортировке.
             */
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

            /**
             * Даты нужны frontend-сортировке.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
