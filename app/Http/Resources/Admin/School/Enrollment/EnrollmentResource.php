<?php

namespace App\Http\Resources\Admin\School\Enrollment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    /**
     * Представление зачисления.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'course_id'        => $this->course_id,
            'course_schedule_id'=> $this->course_schedule_id,
            'order_id'         => $this->order_id,

            'status'           => $this->status, // active|completed|cancelled|expired|paused
            'started_at'       => optional($this->started_at)?->toISOString(),
            'expires_at'       => optional($this->expires_at)?->toISOString(),
            'completed_at'     => optional($this->completed_at)?->toISOString(),

            'progress_percent' => $this->progress_percent,
            'notes'            => $this->notes,
            'meta'             => $this->meta,

            'created_at'       => optional($this->created_at)?->toISOString(),
            'updated_at'       => optional($this->updated_at)?->toISOString(),

            // Удобные вычисляемые флаги/поля
            'is_accessible'    => $this->is_accessible ?? ($this->status === 'active'
                    && (is_null($this->expires_at) || $this->expires_at->isFuture())),
            'days_left'        => $this->days_left, // аксессор из модели (null, если без срока)

            // Короткие сведения по связям (если загружены)
            'user' => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ]),
            'course' => $this->whenLoaded('course', fn () => [
                'id'    => $this->course->id,
                'title' => $this->course->title,
                'slug'  => $this->course->slug,
            ]),
            'schedule' => $this->whenLoaded('schedule', fn () => [
                'id'        => $this->schedule->id,
                'title'     => $this->schedule->title,
                'starts_at' => optional($this->schedule->starts_at)?->toISOString(),
                'ends_at'   => optional($this->schedule->ends_at)?->toISOString(),
            ]),
            'certificate' => $this->whenLoaded('certificate', fn () => [
                'id'         => $this->certificate->id,
                'number'     => $this->certificate->number ?? null,
                'issued_at'  => optional($this->certificate->issued_at)?->toISOString(),
            ]),
        ];
    }
}
