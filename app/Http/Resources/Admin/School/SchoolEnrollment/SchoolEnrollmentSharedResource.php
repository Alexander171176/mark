<?php

namespace App\Http\Resources\Admin\School\SchoolEnrollment;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use App\Http\Resources\Admin\School\SchoolOrder\SchoolOrderSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolEnrollmentSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' =>
                $this->id,

            /**
             * FK.
             */
            'user_id' =>
                $this->user_id,

            'school_course_id' =>
                $this->school_course_id,

            'school_course_schedule_id' =>
                $this->school_course_schedule_id,

            'school_order_id' =>
                $this->school_order_id,

            /**
             * Основные данные.
             */
            'status' =>
                $this->status,

            'progress_percent' =>
                (int) $this->progress_percent,

            'notes' =>
                $this->notes,

            /**
             * Даты.
             *
             * ISO нужен frontend-сортировке
             * и отображению.
             */
            'started_at' =>
                $this->started_at?->toISOString(),

            'expires_at' =>
                $this->expires_at?->toISOString(),

            'completed_at' =>
                $this->completed_at?->toISOString(),

            /**
             * Вычисляемые состояния.
             */
            'is_accessible' =>
                (bool) $this->is_accessible,

            'days_left' =>
                $this->days_left,

            /**
             * Пользователь.
             *
             * Нужен:
             * - отображению;
             * - frontend-поиску;
             * - frontend-сортировке.
             */
            'user' => $this->whenLoaded(
                'user',
                fn () => $this->user
                    ? [
                        'id' =>
                            $this->user->id,

                        'name' =>
                            $this->user->name,

                        'email' =>
                            $this->user->email,
                    ]
                    : null
            ),

            /**
             * Курс.
             *
             * Controller загружает только
             * translations выбранной locale.
             */
            'course' =>
                new SchoolCourseSharedResource(
                    $this->whenLoaded(
                        'course'
                    )
                ),

            /**
             * Поток.
             *
             * Controller загружает:
             *
             * schedule.translations
             * schedule.course.translations
             *
             * только выбранной locale.
             */
            'schedule' =>
                new SchoolCourseScheduleSharedResource(
                    $this->whenLoaded(
                        'schedule'
                    )
                ),

            /**
             * Заказ.
             *
             * Номер заказа нужен
             * frontend-поиску.
             */
            'order' =>
                new SchoolOrderSharedResource(
                    $this->whenLoaded(
                        'order'
                    )
                ),

            /**
             * Сертификат.
             *
             * Index показывает сам факт
             * наличия сертификата.
             *
             * Полный CertificateResource
             * здесь не требуется.
             */
            'certificate' => $this->whenLoaded(
                'certificate',
                fn () => $this->certificate
                    ? [
                        'id' =>
                            $this->certificate->id,

                        'number' =>
                            $this->certificate->number,

                        'status' =>
                            $this->certificate->status,
                    ]
                    : null
            ),

            /**
             * Counts.
             */
            'progress_records_count' => $this->when(
                isset($this->progress_records_count),
                fn () =>
                (int) $this->progress_records_count
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
