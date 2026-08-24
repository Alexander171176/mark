<?php

namespace App\Http\Resources\Admin\School\SchoolEnrollment;

use App\Http\Resources\Admin\School\SchoolCourse\SchoolCourseSharedResource;
use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use App\Http\Resources\Admin\School\SchoolOrder\SchoolOrderSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolEnrollmentResource extends JsonResource
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

            /**
             * Edit будет использовать
             * datetime-local.
             */
            'started_at' =>
                $this->started_at?->format(
                    'Y-m-d\TH:i'
                ),

            'expires_at' =>
                $this->expires_at?->format(
                    'Y-m-d\TH:i'
                ),

            'completed_at' =>
                $this->completed_at?->format(
                    'Y-m-d\TH:i'
                ),

            'progress_percent' =>
                (int) $this->progress_percent,

            'notes' =>
                $this->notes,

            'meta' =>
                $this->meta,

            /**
             * Вычисляемые состояния.
             */
            'is_accessible' =>
                (bool) $this->is_accessible,

            'days_left' =>
                $this->days_left,

            /**
             * Пользователь.
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
             * Edit-контроллер заранее
             * загрузит translations
             * только выбранной locale.
             */
            'course' =>
                new SchoolCourseSharedResource(
                    $this->whenLoaded(
                        'course'
                    )
                ),

            /**
             * Поток.
             */
            'schedule' =>
                new SchoolCourseScheduleSharedResource(
                    $this->whenLoaded(
                        'schedule'
                    )
                ),

            /**
             * Заказ.
             */
            'order' =>
                new SchoolOrderSharedResource(
                    $this->whenLoaded(
                        'order'
                    )
                ),

            /**
             * Записи прогресса.
             *
             * Нужны только если relation
             * специально загружена.
             */
            'progress_records' =>
                $this->whenLoaded(
                    'progressRecords',
                    fn () =>
                    $this->progressRecords->map(
                        fn ($record) => [
                            'id' =>
                                $record->id,

                            'school_course_id' =>
                                $record->school_course_id,

                            'school_module_id' =>
                                $record->school_module_id,

                            'school_lesson_id' =>
                                $record->school_lesson_id,

                            'status' =>
                                $record->status,

                            'progress_percent' =>
                                (int) $record->progress_percent,

                            'time_spent_seconds' =>
                                (int) $record->time_spent_seconds,

                            'last_viewed_at' =>
                                $record
                                    ->last_viewed_at
                                    ?->toISOString(),

                            'completed_at' =>
                                $record
                                    ->completed_at
                                    ?->toISOString(),
                        ]
                    )
                ),

            /**
             * Сертификат.
             */
            'certificate' => $this->whenLoaded(
                'certificate',
                fn () => $this->certificate
                    ? [
                        'id' =>
                            $this->certificate->id,

                        'number' =>
                            $this->certificate->number,

                        'verification_code' =>
                            $this->certificate->verification_code,

                        'status' =>
                            $this->certificate->status,

                        'issued_at' =>
                            $this->certificate
                                ->issued_at
                                ?->toISOString(),

                        'expires_at' =>
                            $this->certificate
                                ->expires_at
                                ?->toISOString(),
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
             * Служебные даты.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
