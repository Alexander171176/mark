<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Hero;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroSectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'locale'      => $this->locale,
            'title'       => $this->title,
            'subtitle'    => $this->subtitle,
            'badge_text'  => $this->badge_text,
            'description' => $this->description,

            'primary_btn_text' => $this->primary_btn_text,
            'primary_btn_url' => $this->primary_btn_url,
            'primary_btn_target' => $this->primary_btn_target,
            'secondary_btn_text' => $this->secondary_btn_text,
            'secondary_btn_url' => $this->secondary_btn_url,
            'secondary_btn_target' => $this->secondary_btn_target,

            'is_dark'  => (bool) $this->is_dark,
            'activity' => (bool) $this->activity,

            // Подтягиваются, если заранее загружены ->with(['icons','screenshots'])
            'icons'       => HeroIconResource::collection($this->whenLoaded('icons')),
            'screenshots' => HeroScreenshotResource::collection($this->whenLoaded('screenshots')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
