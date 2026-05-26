<?php

namespace App\Http\Resources\Admin\School\InstructorProfile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

class SchoolInstructorProfileSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstImage = $this->whenLoaded('images', fn () => $this->images->first());

        $thumbnailUrl = !($firstImage instanceof MissingValue) && $firstImage
            ? ($firstImage->thumb_url ?? $firstImage->image_url ?? $firstImage->url ?? null)
            : null;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            'slug' => $this->slug,
            'title' => $this->translation?->title,
            'short' => $this->translation?->short,

            'sort' => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'experience_years' => $this->experience_years !== null
                ? (int) $this->experience_years
                : null,

            'hourly_rate' => $this->hourly_rate !== null
                ? (string) $this->hourly_rate
                : null,

            'rating_avg' => $this->rating_avg !== null ? (float) $this->rating_avg : null,
            'rating_count' => (int) $this->rating_count,

            'views' => (int) $this->views,
            'public_name' => $this->public_name,

            'thumbnail_url' => $thumbnailUrl,

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
        ];
    }
}
