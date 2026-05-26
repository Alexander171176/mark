<?php

namespace App\Http\Resources\Admin\School\Review;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolReviewSharedResource extends JsonResource
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
            'status' => $this->status,
            'is_public' => (bool) $this->is_public,

            'published_at' => optional($this->published_at)->toIso8601String(),
        ];
    }
}
