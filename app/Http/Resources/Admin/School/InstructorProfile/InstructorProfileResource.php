<?php

namespace App\Http\Resources\Admin\School\InstructorProfile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorProfileResource extends JsonResource
{
    /**
     * Представление профиля инструктора.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Базовые поля
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'locale'           => $this->locale,
            'title'            => $this->title,
            'slug'             => $this->slug,
            'short'            => $this->short,
            'bio'              => $this->bio,
            'activity'         => (bool) $this->activity,
            'sort'             => (int) $this->sort,
            'views'            => (int) ($this->views ?? 0),

            // Параметры профиля
            'hourly_rate'      => $this->hourly_rate ? (string) $this->hourly_rate : null, // decimal как строка
            'experience_years' => $this->experience_years !== null ? (int) $this->experience_years : null,
            'social_links'     => $this->social_links ?? [],

            // Рейтинг
            'rating' => [
                'avg'   => $this->rating_avg !== null ? (float) $this->rating_avg : null,
                'count' => (int) ($this->rating_count ?? 0),
            ],

            // SEO
            'meta_title'       => $this->meta_title,
            'meta_keywords'    => $this->meta_keywords,
            'meta_desc'        => $this->meta_desc,

            // Медиа (Spatie Media Library) Изображения (если загружены)
            'images'   => InstructorProfileImageResource::collection($this->whenLoaded('images')),

            // Удобные вычисляемые поля
            'public_name'      => $this->public_name,

            // Даты (ISO строки)
            'created_at'       => optional($this->created_at)?->toIso8601String(),
            'updated_at'       => optional($this->updated_at)?->toIso8601String(),
            'deleted_at'       => optional($this->deleted_at)?->toIso8601String(),

            /* ================= связи (только если загружены) ================= */

            // Владелец профиля
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),

            // Курсы (кратко)
            'courses' => $this->whenLoaded('courses', function () {
                return $this->courses->map(function ($course) {
                    return [
                        'id'    => $course->id,
                        'title' => $course->title,
                        'slug'  => $course->slug,
                    ];
                });
            }),

            // Выплаты (кратко)
            'payouts' => $this->whenLoaded('payouts', function () {
                return $this->payouts->map(function ($payout) {
                    return [
                        'id'       => $payout->id,
                        'status'   => $payout->status ?? null,
                        'amount'   => isset($payout->amount) ? (string) $payout->amount : null,
                        'currency' => $payout->currency ?? null,
                        'paid_at'  => optional($payout->paid_at)?->toIso8601String(),
                    ];
                });
            }),

            /* ================= счётчики, если добавлены через withCount ================= */
            'courses_count' => $this->when(isset($this->courses_count), (int) $this->courses_count),
            'payouts_count' => $this->when(isset($this->payouts_count), (int) $this->payouts_count),
        ];
    }
}
