<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Component;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComponentFeatureResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'section_id'      => (int) $this->section_id,
            'box_class'       => $this->box_class ?? 'is-primary',
            'title'           => $this->title,
            'text'            => $this->text,

            // SVG (светлая/тёмная версии)
            'icon_svg_light'  => $this->icon_svg_light,
            'icon_svg_dark'   => $this->icon_svg_dark,
            'icon_alt'        => $this->icon_alt,

            'sort'            => (int) ($this->sort ?? 0),
            'activity'        => (bool) ($this->activity ?? true),

            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
