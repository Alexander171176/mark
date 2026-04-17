<?php

namespace App\Http\Resources\Admin\School\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $thumbnailUrl = null;

        if ($this->relationLoaded('images')) {
            $firstImage = $this->images->first();
            $thumbnailUrl = $firstImage?->thumb_url
                ?? $firstImage?->url
                ?? $firstImage?->image_url
                ?? null;
        }

        return [
            'id' => $this->id,
            'instructor_profile_id' => $this->instructor_profile_id,

            'locale' => $this->locale,
            'title' => $this->title,
            'slug' => $this->slug,
            'subtitle' => $this->subtitle,
            'short' => $this->short,

            'activity' => $this->activity,
            'status' => $this->status,
            'published_at' => $this->published_at?->format('Y-m-d'),

            'sort' => $this->sort,
            'views' => $this->views,
            'likes' => $this->likes,
            'rating_avg' => $this->rating_avg,
            'rating_count' => $this->rating_count,
            'students_count' => $this->students_count,
            'popularity' => $this->popularity,

            'duration' => $this->duration,
            'level' => $this->level,
            'difficulty' => $this->difficulty,

            'left' => $this->left,
            'main' => $this->main,
            'right' => $this->right,
            'is_new' => $this->is_new,
            'is_hit' => $this->is_hit,
            'is_sale' => $this->is_sale,

            'thumbnail_url' => $thumbnailUrl,

            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'order' => $image->pivot->order ?? null,
                        'alt' => $image->alt ?? null,
                        'caption' => $image->caption ?? null,
                        'url' => $image->url ?? null,
                        'webp_url' => $image->webp_url ?? null,
                        'image_url' => $image->image_url ?? null,
                        'thumb_url' => $image->thumb_url ?? null,
                    ];
                })->values();
            }, []),

            'instructorProfile' => $this->whenLoaded('instructorProfile', function () {
                return [
                    'id' => $this->instructorProfile->id,
                    'title' => $this->instructorProfile->title,
                    'slug' => $this->instructorProfile->slug,
                    'public_name' => $this->instructorProfile->public_name,
                    'short' => $this->instructorProfile->short,
                    'hourly_rate' => $this->instructorProfile->hourly_rate,
                    'rating_avg' => $this->instructorProfile->rating_avg,
                    'rating_count' => $this->instructorProfile->rating_count,

                    'images' => $this->instructorProfile->relationLoaded('images')
                        ? $this->instructorProfile->images
                            ->sortBy(fn ($image) => $image->pivot->order ?? PHP_INT_MAX)
                            ->map(function ($image) {
                                return [
                                    'id' => $image->id,
                                    'order' => $image->pivot->order ?? null,
                                    'alt' => $image->alt ?? null,
                                    'caption' => $image->caption ?? null,
                                    'url' => $image->url ?? null,
                                    'webp_url' => $image->webp_url ?? null,
                                    'image_url' => $image->image_url ?? null,
                                    'thumb_url' => $image->thumb_url ?? null,
                                ];
                            })
                            ->values()
                        : [],
                ];
            }),
        ];
    }
}
