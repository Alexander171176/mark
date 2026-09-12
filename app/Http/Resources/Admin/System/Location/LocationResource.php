<?php

namespace App\Http\Resources\Admin\System\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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

            'parent_id' => $this->parent_id,

            'type' => $this->type,
            'slug' => $this->slug,
            'code' => $this->code,

            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timezone' => $this->timezone,

            'activity' => (bool) $this->activity,
            'is_default' => (bool) $this->is_default,
            'sort' => $this->sort,

            'translations' => LocationTranslationResource::collection(
                $this->whenLoaded('translations')
            ),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
