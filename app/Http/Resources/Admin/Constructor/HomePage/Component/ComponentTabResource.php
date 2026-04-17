<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Component;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComponentTabResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'section_id' => (int) $this->section_id,
            'slug'       => (string) $this->slug,   // base / intermediate / blocks / advanced / utilities
            'label'      => $this->label,

            'sort'       => (int) ($this->sort ?? 0),
            'activity'   => (bool) ($this->activity ?? true),

            // Плитки внутри вкладки
            'tiles'      => ComponentTileResource::collection(
                $this->whenLoaded('tiles')
            ),

            'created_at'  => $this->created_at?->toISOString(),
            'updated_at'  => $this->updated_at?->toISOString(),
        ];
    }
}
