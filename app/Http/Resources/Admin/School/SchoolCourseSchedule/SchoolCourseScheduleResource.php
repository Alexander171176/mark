<?php

namespace App\Http\Resources\Admin\School\SchoolCourseSchedule;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolInstructorProfile\SchoolInstructorProfileSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCourseScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        $fallbackLocale = config(
            'app.fallback_locale',
            'ru'
        );

        /**
         * Текущий перевод.
         *
         * Никакого отдельного relation translation
         * Resource больше не требует.
         *
         * Edit загружает все translations,
         * после чего нужный перевод определяется
         * уже в памяти:
         *
         * current
         * → fallback
         * → первый доступный.
         */
        $translation = $this->relationLoaded(
            'translations'
        )
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
             * Текущий перевод.
             */
            'translation' => $translation
                ? new SchoolCourseScheduleTranslationResource(
                    $translation
                )
                : null,

            /**
             * Все переводы нужны Edit.
             */
            'translations' =>
                SchoolCourseScheduleTranslationResource::collection(
                    $this->whenLoaded(
                        'translations'
                    )
                ),

            /**
             * Даты расписания.
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

            'notes' =>
                $this->notes,

            /**
             * Вычисляемое состояние.
             */
            'is_enrollment_open' =>
                (bool) $this->is_enrollment_open,

            /**
             * Изображения нужны Edit.
             *
             * Controller обязан загрузить
             * images.media.
             */
            'images' =>
                SchoolCourseScheduleImageResource::collection(
                    $this->whenLoaded(
                        'images'
                    )
                ),

            /**
             * Связанный курс.
             */
            'course' =>
                new SchoolCourseSharedResource(
                    $this->whenLoaded(
                        'course'
                    )
                ),

            /**
             * Преподаватель.
             *
             * Полный InstructorProfileResource
             * здесь не нужен.
             */
            'instructor' =>
                new SchoolInstructorProfileSharedResource(
                    $this->whenLoaded(
                        'instructor'
                    )
                ),

            /**
             * Зачисления.
             *
             * Оставляем условно, поскольку
             * Resource может использоваться
             * там, где relation специально
             * загружена.
             */
            'cohort_enrollments' =>
                $this->whenLoaded(
                    'cohortEnrollments',
                    fn () =>
                    $this->cohortEnrollments->map(
                        fn ($enrollment) => [
                            'id' =>
                                $enrollment->id,

                            'user_id' =>
                                $enrollment->user_id,

                            'status' =>
                                $enrollment->status,

                            'enrolled_at' =>
                                $enrollment
                                    ->enrolled_at
                                    ?->toISOString(),

                            'user' =>
                                $enrollment->relationLoaded(
                                    'user'
                                )
                                && $enrollment->user
                                    ? [
                                    'id' =>
                                        $enrollment->user->id,

                                    'name' =>
                                        $enrollment->user->name,

                                    'email' =>
                                        $enrollment->user->email,
                                ]
                                    : null,
                        ]
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
             * Служебные даты.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
