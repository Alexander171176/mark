<?php

namespace App\Http\Resources\Admin\School\CohortEnrollment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CohortEnrollmentResource extends JsonResource
{
    /**
     * Представление записи участника потока.
     *
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'course_schedule_id' => $this->course_schedule_id,
            'user_id'            => $this->user_id,
            'status'             => $this->status, // pending|approved|rejected|cancelled
            'enrolled_at'        => optional($this->enrolled_at)?->toISOString(),
            'notes'              => $this->notes,

            'created_at'         => optional($this->created_at)?->toISOString(),
            'updated_at'         => optional($this->updated_at)?->toISOString(),
            'deleted_at'         => optional($this->deleted_at)?->toISOString(),

            // Короткая информация по связям, если подгружены
            'schedule' => $this->whenLoaded('schedule', fn () => [
                'id'        => $this->schedule->id,
                'title'     => $this->schedule->title,
                'starts_at' => optional($this->schedule->starts_at)?->toISOString(),
                'ends_at'   => optional($this->schedule->ends_at)?->toISOString(),
                'status'    => $this->schedule->status,
            ]),
            'user' => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ]),
        ];
    }
}
