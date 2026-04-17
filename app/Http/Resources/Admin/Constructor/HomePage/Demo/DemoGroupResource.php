<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Demo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemoGroupResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'section_id'  => $this->section_id,
            'slug'        => (string) $this->slug,
            'title'       => $this->title,
            'description' => $this->description,
            'icon_alt'    => $this->icon_alt,

            // inline SVG, храним и отдаём как текст (НЕ через Spatie)
            'icon_svg_light' => $this->icon_svg_light,
            'icon_svg_dark'  => $this->icon_svg_dark,

            'sort'      => (int) $this->sort,
            'activity'  => (bool) $this->activity,

            // Элементы группы (если подгружены)
            'items' => DemoItemResource::collection(
                $this->whenLoaded('items')
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
