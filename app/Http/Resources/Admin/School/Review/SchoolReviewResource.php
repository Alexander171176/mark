<?php

namespace App\Http\Resources\Admin\School\Review;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'user_id' => $this->user_id,

            'reviewable_type' => $this->reviewable_type,
            'reviewable_id' => $this->reviewable_id,

            'rating' => (int) $this->rating,
            'title' => $this->title,
            'body' => $this->body,

            'status' => $this->status,
            'is_public' => (bool) $this->is_public,
            'published_at' => optional($this->published_at)->toIso8601String(),

            'helpful_count' => (int) $this->helpful_count,
            'reported_count' => (int) $this->reported_count,

            'meta' => $this->meta,

            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),

            'reviewable' => $this->whenLoaded('reviewable', function () {
                if (! $this->reviewable) {
                    return null;
                }

                return [
                    'type' => class_basename($this->reviewable_type),
                    'id' => $this->reviewable->getKey(),
                    'title' => $this->reviewable->translation?->title
                        ?? $this->reviewable->translation?->name
                            ?? $this->reviewable->title
                            ?? $this->reviewable->name
                            ?? null,
                    'slug' => $this->reviewable->slug ?? null,
                ];
            }),

            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
