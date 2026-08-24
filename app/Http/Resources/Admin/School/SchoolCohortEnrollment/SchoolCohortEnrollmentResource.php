<?php

namespace App\Http\Resources\Admin\School\SchoolCohortEnrollment;

use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCohortEnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' =>
                $this->id,

            /**
             * Связи.
             */
            'school_course_schedule_id' =>
                $this->school_course_schedule_id,

            'user_id' =>
                $this->user_id,

            /**
             * Основные данные записи.
             */
            'status' =>
                $this->status,

            /**
             * Edit использует datetime-local.
             */
            'enrolled_at' =>
                $this->enrolled_at?->format(
                    'Y-m-d\TH:i'
                ),

            'notes' =>
                $this->notes,

            /**
             * Поток.
             *
             * Controller заранее загружает:
             *
             * schedule.translations
             * только для выбранной locale.
             *
             * Также может быть загружен
             * schedule.course.translations.
             */
            'schedule' =>
                new SchoolCourseScheduleSharedResource(
                    $this->whenLoaded(
                        'schedule'
                    )
                ),

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
             * Служебные даты.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
