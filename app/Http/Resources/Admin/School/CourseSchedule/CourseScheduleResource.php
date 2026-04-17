<?php

namespace App\Http\Resources\Admin\School\CourseSchedule;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseScheduleResource extends JsonResource
{
    /**
     * Представление расписания/потока курса.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'course_id'             => $this->course_id,
            'instructor_profile_id' => $this->instructor_profile_id,

            // Локаль и управление списком
            'locale'   => $this->locale,
            'activity' => (bool) $this->activity,
            'sort'     => (int) $this->sort,

            // Основные поля
            'title'       => $this->title,
            'slug'        => $this->slug,
            'subtitle'    => $this->subtitle,
            'short'       => $this->short,
            'description' => $this->description,

            // SEO
            'meta_title'    => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc'     => $this->meta_desc,

            // Даты проведения потока
            'starts_at'        => optional($this->starts_at)?->format('Y-m-d'),        // YYYY-MM-DD
            'ends_at'          => optional($this->ends_at)?->format('Y-m-d'),          // YYYY-MM-DD
            'enroll_starts_at' => optional($this->enroll_starts_at)?->format('Y-m-d'), // YYYY-MM-DD
            'enroll_ends_at'   => optional($this->enroll_ends_at)?->format('Y-m-d'),   // YYYY-MM-DD

            // Параметры потока
            'capacity'    => (int) $this->capacity,
            'is_online'   => (bool) $this->is_online,
            'location'    => $this->location,
            'meeting_url' => $this->meeting_url,
            'timezone'    => $this->timezone,

            // Статус и метрики
            'status' => $this->status,        // draft|published|archived|cancelled
            'views'  => (int) $this->views,   // Просмотры

            // Заметки админа
            'notes' => $this->notes,

            // Виртуальное свойство из аксессора
            'is_enrollment_open' => (bool) ($this->is_enrollment_open ?? false),

            // Таймстампы
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
            'deleted_at' => optional($this->deleted_at)?->toISOString(),

            // Короткие данные по курсу (если подгружен)
            'course' => $this->whenLoaded('course', function () {
                return [
                    'id'    => $this->course->id,
                    'title' => $this->course->title,
                    'slug'  => $this->course->slug,
                ];
            }),

            // Данные по преподавателю (если подгружен)
            'instructor' => $this->whenLoaded('instructor', function () {
                return [
                    'id'    => $this->instructor->id,
                    'title' => $this->instructor->title,
                    'slug'  => $this->instructor->slug,
                    'user'  => $this->instructor->relationLoaded('user') && $this->instructor->user
                        ? [
                            'id'    => $this->instructor->user->id,
                            'name'  => $this->instructor->user->name,
                            'email' => $this->instructor->user->email,
                        ]
                        : null,
                ];
            }),

            // Изображения потока (по аналогии с Lesson / Assignment)
            'images' => $this->whenLoaded('images', fn () =>
            CourseScheduleImageResource::collection($this->images)
            ),

            // Если будешь использовать withCount('cohortEnrollments')
            'enrollments_count' => $this->when(
                isset($this->cohort_enrollments_count),
                (int) $this->cohort_enrollments_count
            ),
        ];
    }
}
