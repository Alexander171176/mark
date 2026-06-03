<?php

namespace App\Http\Resources\Admin\School\SchoolHashtag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolHashtagSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'slug' => $this->slug,
            'color' => $this->color,

            'name' => $this->translation?->name,

            'views' => (int) $this->views,
            'likes' => (int) $this->likes,
        ];
    }
}
