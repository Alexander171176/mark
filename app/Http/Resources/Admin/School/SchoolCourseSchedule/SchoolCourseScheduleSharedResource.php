<?php

namespace App\Http\Resources\Admin\School\SchoolCourseSchedule;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCourseScheduleSharedResource extends JsonResource
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

        /**
         * Готовая миниатюра для Index.
         *
         * Controller обязан загрузить
         * images.media.
         */
        $thumbnailUrl = $primaryImage
            ? (
                $primaryImage->thumb_url
                ?? $primaryImage->webp_url
                ?? $primaryImage->image_url
                ?? $primaryImage->url
                ?? null
            )
            : null;

        return [
            'id' =>
                $this->id,

            'school_course_id' =>
                $this->school_course_id,

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
             * Перевод выбранной локали.
             *
             * description оставляем:
             * она нужна frontend-поиску.
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

                    'description' =>
                        $translation->description,
                ]
                : null,

            /**
             * Основные даты потока.
             */
            'starts_at' =>
                $this->starts_at?->format(
                    'Y-m-d\TH:i'
                ),

            'ends_at' =>
                $this->ends_at?->format(
                    'Y-m-d\TH:i'
                ),

            'enroll_starts_at' =>
                $this->enroll_starts_at?->format(
                    'Y-m-d\TH:i'
                ),

            'enroll_ends_at' =>
                $this->enroll_ends_at?->format(
                    'Y-m-d\TH:i'
                ),

            /**
             * Параметры потока.
             */
            'capacity' =>
                (int) $this->capacity,

            'is_online' =>
                (bool) $this->is_online,

            'location' =>
                $this->location,

            'meeting_url' =>
                $this->meeting_url,

            'timezone' =>
                $this->timezone,

            'status' =>
                $this->status,

            'views' =>
                (int) $this->views,

            /**
             * Вычисляемое состояние записи.
             */
            'is_enrollment_open' =>
                (bool) $this->is_enrollment_open,

            /**
             * Миниатюра Index.
             */
            'thumbnail_url' =>
                $thumbnailUrl,

            /**
             * Связанный курс.
             *
             * Controller загружает только
             * translations выбранной локали.
             */
            'course' =>
                new SchoolCourseSharedResource(
                    $this->whenLoaded(
                        'course'
                    )
                ),

            /**
             * Преподаватель.
             */
            'instructor' =>
                new SchoolInstructorProfileSharedResource(
                    $this->whenLoaded(
                        'instructor'
                    )
                ),

            /**
             * Counts.
             */
            'cohort_enrollments_count' => $this->when(
                isset($this->cohort_enrollments_count),
                fn () =>
                (int) $this->cohort_enrollments_count
            ),

            'images_count' => $this->when(
                isset($this->images_count),
                fn () =>
                (int) $this->images_count
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
