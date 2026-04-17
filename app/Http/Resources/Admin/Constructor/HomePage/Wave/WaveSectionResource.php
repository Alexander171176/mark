<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Wave;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WaveSectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'locale'     => $this->locale,
            'title'      => $this->title,
            'subtitle'   => $this->subtitle,
            'left_text'  => $this->left_text,
            'right_text' => $this->right_text,
            'sort'       => (int) $this->sort,
            'is_dark'    => (bool) $this->is_dark,
            'activity'   => (bool) $this->activity,

            // Когда нужно отдавать секцию вместе с технологиями:
            // 'teches' => WaveTechResource::collection($this->whenLoaded('teches')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
