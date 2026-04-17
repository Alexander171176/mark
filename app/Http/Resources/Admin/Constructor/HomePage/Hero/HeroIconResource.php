<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Hero;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroIconResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'hero_section_id' => $this->hero_section_id,
            'label'           => $this->label,
            'svg'             => $this->svg, // raw SVG из БД
            'sort'            => (int) $this->sort,
            'activity'        => (bool) $this->activity,
            'created_at'      => $this->created_at?->toISOString(),
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
