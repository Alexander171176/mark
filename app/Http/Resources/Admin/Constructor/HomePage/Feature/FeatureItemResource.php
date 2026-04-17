<?php
// app/Http/Resources/Admin/HomePage/Feature/FeatureItemResource.php

namespace App\Http\Resources\Admin\Constructor\HomePage\Feature;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'feature_section_id' => $this->feature_section_id,

            'title'       => $this->title,
            'subtitle'    => $this->subtitle,
            'description' => $this->description,

            // Инлайновые SVG / HTML (TEXT / MEDIUMTEXT в БД)
            'image_light' => $this->image_light,
            'image_dark'  => $this->image_dark,
            'alt'         => $this->alt,

            'sort'      => (int) $this->sort,
            'activity'  => (bool) $this->activity,

            'created_at'=> $this->created_at?->toISOString(),
            'updated_at'=> $this->updated_at?->toISOString(),
        ];
    }
}
