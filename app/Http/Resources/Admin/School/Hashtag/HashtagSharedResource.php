<?php

namespace App\Http\Resources\Admin\School\Hashtag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HashtagSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'locale' => $this->locale,
            'color' => $this->color,
            'views' => $this->views,
            'likes' => $this->likes,
        ];
    }
}
