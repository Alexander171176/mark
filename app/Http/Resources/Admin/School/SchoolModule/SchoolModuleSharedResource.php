<?php

namespace App\Http\Resources\Admin\School\SchoolModule;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolModuleSharedResource extends JsonResource
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
         * Первое изображение уже соответствует
         * pivot order связи images().
         */
        $primaryImage = $this->relationLoaded('images')
            ? $this->images->first()
            : null;

        return [
            'id' =>
                $this->id,

            'school_course_id' =>
                $this->school_course_id,

            /**
             * Основные поля.
             */
            'sort' =>
                (int) $this->sort,

            'activity' =>
                (bool) $this->activity,

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

                    'subtitle' =>
                        $translation->subtitle,

                    'short' =>
                        $translation->short,

                    /**
                     * Нужен frontend-поиску.
                     */
                    'description' =>
                        $translation->description,
                ]
                : null,

            /**
             * Публикация / состояние.
             */
            'published_at' =>
                $this->published_at?->format('Y-m-d'),

            'status' =>
                $this->status,

            'availability' =>
                $this->availability,

            'difficulty' =>
                $this->difficulty !== null
                    ? (int) $this->difficulty
                    : null,

            'duration' =>
                $this->duration !== null
                    ? (int) $this->duration
                    : null,

            /**
             * Статистика.
             */
            'lessons_count' =>
                (int) $this->lessons_count,

            'popularity' =>
                (int) $this->popularity,

            'rating_count' =>
                (int) $this->rating_count,

            'rating_avg' =>
                $this->rating_avg !== null
                    ? (float) $this->rating_avg
                    : null,

            'views' =>
                (int) $this->views,

            'likes' =>
                (int) $this->likes,

            /**
             * Изображения.
             *
             * Controller обязан загрузить
             * images.media.
             */
            'primary_image' => $primaryImage
                ? new SchoolModuleImageResource(
                    $primaryImage
                )
                : null,

            'images' =>
                SchoolModuleImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Родительский курс.
             *
             * Нужен также frontend-поиску.
             */
            'course' =>
                new SchoolCourseSharedResource(
                    $this->whenLoaded(
                        'course'
                    )
                ),

            /**
             * Counts для отображения
             * и frontend-сортировки.
             */
            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'likes_count' => $this->when(
                isset($this->likes_count),
                fn () => (int) $this->likes_count
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
