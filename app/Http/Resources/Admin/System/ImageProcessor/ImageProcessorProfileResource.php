<?php

namespace App\Http\Resources\Admin\System\ImageProcessor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageProcessorProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            /** Основные данные */
            'key' => $this->key,
            'name' => $this->name,
            'description' => $this->description,

            /** Настройки */
            'activity' => (bool) $this->activity,
            'sort' => (int) $this->sort,

            /** Временные метки */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            /** Счётчики */
            'variants_count' => $this->whenCounted('variants'),

            /** Связи */
            'variants' => ImageProcessorVariantResource::collection(
                $this->whenLoaded('variants')
            ),
        ];
    }
}
