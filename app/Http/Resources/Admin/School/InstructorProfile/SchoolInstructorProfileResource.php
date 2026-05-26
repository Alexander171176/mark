<?php

namespace App\Http\Resources\Admin\School\InstructorProfile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolInstructorProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'user_id' => $this->user_id,
            'slug' => $this->slug,

            'title' => $this->translation?->title,
            'short' => $this->translation?->short,
            'bio' => $this->translation?->bio,

            'meta_title' => $this->translation?->meta_title,
            'meta_keywords' => $this->translation?->meta_keywords,
            'meta_desc' => $this->translation?->meta_desc,

            'experience_years' => $this->experience_years !== null
                ? (int) $this->experience_years
                : null,

            'hourly_rate' => $this->hourly_rate !== null
                ? (string) $this->hourly_rate
                : null,

            'views' => (int) $this->views,

            'rating' => [
                'avg' => $this->rating_avg !== null ? (float) $this->rating_avg : null,
                'count' => (int) $this->rating_count,
            ],

            'social_links' => $this->social_links ?? [],

            'public_name' => $this->public_name,

            'primary_image' => new SchoolInstructorProfileImageResource(
                $this->whenLoaded('images', fn () => $this->primary_image)
            ),

            'images' => SchoolInstructorProfileImageResource::collection(
                $this->whenLoaded('images')
            ),

            'translations' => SchoolInstructorProfileTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),

            'courses' => $this->whenLoaded('courses', fn () => $this->courses->map(fn ($course) => [
                'id' => $course->id,
                'slug' => $course->slug,
                'title' => $course->translation?->title,
            ])),

            'payouts' => $this->whenLoaded('payouts', fn () => $this->payouts->map(fn ($payout) => [
                'id' => $payout->id,
                'number' => $payout->number,
                'status' => $payout->status,
                'currency' => $payout->currency,
                'amount_gross' => (string) $payout->amount_gross,
                'amount_net' => (string) $payout->amount_net,
                'paid_at' => optional($payout->paid_at)->toIso8601String(),
            ])),

            'courses_count' => $this->when(
                isset($this->courses_count),
                fn () => (int) $this->courses_count
            ),

            'payouts_count' => $this->when(
                isset($this->payouts_count),
                fn () => (int) $this->payouts_count
            ),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
