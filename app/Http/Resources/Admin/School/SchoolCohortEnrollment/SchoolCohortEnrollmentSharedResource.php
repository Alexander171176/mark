<?php

namespace App\Http\Resources\Admin\School\SchoolCohortEnrollment;

use App\Http\Resources\Admin\School\SchoolCourseSchedule\SchoolCourseScheduleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolCohortEnrollmentSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' =>
                $this->id,

            /**
             * FK.
             */
            'school_course_schedule_id' =>
                $this->school_course_schedule_id,

            'user_id' =>
                $this->user_id,

            /**
             * Основные поля.
             */
            'status' =>
                $this->status,

            'enrolled_at' =>
                $this->enrolled_at?->toISOString(),

            /**
             * Нужны Index и frontend-поиску.
             */
            'notes' =>
                $this->notes,

            /**
             * Поток.
             *
             * Нужен:
             * - таблице;
             * - карточкам;
             * - frontend-поиску;
             * - frontend-сортировке.
             *
             * Controller загружает только
             * выбранную locale.
             */
            'schedule' =>
                new SchoolCourseScheduleSharedResource(
                    $this->whenLoaded(
                        'schedule'
                    )
                ),

            /**
             * Пользователь.
             *
             * Нужен frontend-поиску
             * и сортировкам name/email.
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
             * Нужны frontend-сортировкам.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
