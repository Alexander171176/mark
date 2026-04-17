<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Feature;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureSectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'locale'    => $this->locale,
            'title'     => $this->title,
            'subtitle'  => $this->subtitle,
            'sort'      => (int) $this->sort,
            'is_dark'   => (bool) $this->is_dark,
            'activity'  => (bool) $this->activity,

            // Подгружаем только если связь items загружена (контроллером)
            'items'     => FeatureItemResource::collection($this->whenLoaded('items')),

            'created_at'=> $this->created_at?->toISOString(),
            'updated_at'=> $this->updated_at?->toISOString(),
        ];
    }
}
