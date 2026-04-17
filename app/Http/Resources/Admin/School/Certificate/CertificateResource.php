<?php

namespace App\Http\Resources\Admin\School\Certificate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,

            'user_id'             => $this->user_id,
            'user'                => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ]),

            'course_id'           => $this->course_id,
            'course'              => $this->whenLoaded('course', fn () => [
                'id'    => $this->course->id,
                'title' => $this->course->title ?? null,
                'slug'  => $this->course->slug ?? null,
            ]),

            'enrollment_id'       => $this->enrollment_id,
            'enrollment'          => $this->whenLoaded('enrollment', fn () => [
                'id'          => $this->enrollment->id,
                'status'      => $this->enrollment->status,
                'started_at'  => optional($this->enrollment->started_at)?->toIso8601String(),
                'completed_at'=> optional($this->enrollment->completed_at)?->toIso8601String(),
            ]),

            'number'              => $this->number,
            'verification_code'   => $this->verification_code,

            'issued_at'           => optional($this->issued_at)?->toIso8601String(),
            'expires_at'          => optional($this->expires_at)?->toIso8601String(),
            'revoked_at'          => optional($this->revoked_at)?->toIso8601String(),

            'score'               => $this->score,
            'hours'               => $this->hours,

            'status'              => $this->status,
            'name_on_certificate' => $this->name_on_certificate,
            'notes'               => $this->notes,
            'meta'                => $this->meta,

            // Удобные флаги из аксессоров модели
            'is_expired'          => (bool) $this->is_expired,
            'is_revoked'          => (bool) $this->is_revoked,

            'created_at'          => optional($this->created_at)?->toIso8601String(),
            'updated_at'          => optional($this->updated_at)?->toIso8601String(),
            'deleted_at'          => optional($this->deleted_at)?->toIso8601String(),
        ];
    }
}
