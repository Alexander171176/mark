<?php

namespace App\Http\Resources\Admin\System\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationTranslationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'location_id' => $this->location_id,
            'locale' => $this->locale,

            'title' => $this->title,
            'title_in' => $this->title_in,
            'title_from' => $this->title_from,

            'short' => $this->short,
            'description' => $this->description,

            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_desc' => $this->meta_desc,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
