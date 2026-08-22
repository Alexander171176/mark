<?php

namespace App\Http\Resources\Admin\School\SchoolCourse;

use App\Http\Resources\Admin\School\SchoolHashtag\SchoolHashtagSharedResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use App\Http\Resources\Admin\School\SchoolTrack\SchoolTrackSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCourseSharedResource extends JsonResource
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

            'school_instructor_profile_id' =>
                $this->school_instructor_profile_id,

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
             * Флаги.
             */
            'is_new' =>
                (bool) $this->is_new,

            'is_hit' =>
                (bool) $this->is_hit,

            'is_sale' =>
                (bool) $this->is_sale,

            'left' =>
                (bool) $this->left,

            'main' =>
                (bool) $this->main,

            'right' =>
                (bool) $this->right,

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
                $this->published_at?->format(
                    'Y-m-d'
                ),

            'level' =>
                $this->level,

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
            'students_count' =>
                (int) $this->students_count,

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
                ? new SchoolCourseImageResource(
                    $primaryImage
                )
                : null,

            'images' =>
                SchoolCourseImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Инструктор.
             */
            'instructorProfile' =>
                new SchoolInstructorProfileSharedResource(
                    $this->whenLoaded(
                        'instructorProfile'
                    )
                ),

            /**
             * Треки.
             *
             * Нужны также frontend-поиску.
             */
            'tracks' =>
                SchoolTrackSharedResource::collection(
                    $this->whenLoaded(
                        'tracks'
                    )
                ),

            /**
             * Хештеги.
             *
             * Нужны также frontend-поиску.
             */
            'hashtags' =>
                SchoolHashtagSharedResource::collection(
                    $this->whenLoaded(
                        'hashtags'
                    )
                ),

            /**
             * Counts для отображения
             * и frontend-сортировки.
             */
            'modules_count' => $this->when(
                isset($this->modules_count),
                fn () => (int) $this->modules_count
            ),

            'lessons_count' => $this->when(
                isset($this->lessons_count),
                fn () => (int) $this->lessons_count
            ),

            'tracks_count' => $this->when(
                isset($this->tracks_count),
                fn () => (int) $this->tracks_count
            ),

            'hashtags_count' => $this->when(
                isset($this->hashtags_count),
                fn () => (int) $this->hashtags_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () => (int) $this->images_count
            ),

            'prices_count' => $this->when(
                isset($this->prices_count),
                fn () => (int) $this->prices_count
            ),

            'reviews_count' => $this->when(
                isset($this->reviews_count),
                fn () => (int) $this->reviews_count
            ),

            'enrollments_count' => $this->when(
                isset($this->enrollments_count),
                fn () => (int) $this->enrollments_count
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
