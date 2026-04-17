<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Quality;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QualityItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'section_id'   => $this->section_id,

            'top_title'    => $this->top_title,
            'title'        => $this->title,
            'description'  => $this->description,

            // Анимационные/поведенческие параметры
            'reveal_from'  => $this->reveal_from,          // 'bottom' | 'top' | 'left' | 'right'
            'delay'        => (int) ($this->delay ?? 0),   // ms
            'threshold'    => is_null($this->threshold) ? null : (float) $this->threshold,
            'distance'     => is_null($this->distance) ? null : (int) $this->distance, // px

            // SVG как текст + alt
            'icon_svg_light'=> $this->icon_svg_light,
            'icon_svg_dark' => $this->icon_svg_dark,
            'icon_alt'      => $this->icon_alt,

            // Служебные
            'sort'       => (int) ($this->sort ?? 0),
            'activity'   => (bool) ($this->activity ?? true),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
