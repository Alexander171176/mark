<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Developer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeveloperItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'developer_section_id' => $this->developer_section_id,

            'title'       => $this->title,
            'subtitle'    => $this->subtitle,
            'description' => $this->description,
            'alt'         => $this->alt,

            // inline SVG (TEXT)
            'image_light' => $this->image_light,
            'image_dark'  => $this->image_dark,

            'sort'     => (int) $this->sort,
            'activity' => (bool) $this->activity,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
