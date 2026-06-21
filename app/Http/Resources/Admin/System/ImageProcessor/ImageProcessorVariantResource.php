<?php

namespace App\Http\Resources\Admin\System\ImageProcessor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageProcessorVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            /** Связь */
            'image_processor_profile_id'
            => (int) $this->image_processor_profile_id,

            /** Основные данные */
            'key' => $this->key,
            'name' => $this->name,
            'description' => $this->description,

            /** Активность */
            'activity' => (bool) $this->activity,

            /** Размеры */
            'width' => (int) $this->width,
            'height' => (int) $this->height,

            /**
             * Удобное поле для UI
             * 1200 × 800
             */
            'resolution' => "{$this->width}×{$this->height}",

            /**
             * Соотношение сторон
             * 1.5
             */
            'aspect_ratio' => $this->height > 0
                ? round($this->width / $this->height, 2)
                : null,

            /** Обработка */
            'allow_rotate' => (bool) $this->allow_rotate,
            'quality' => (int) $this->quality,

            'format' => $this->format,
            'fit' => $this->fit,
            'shape' => $this->shape,

            /** Цвета фона */
            'background_light' => $this->background_light,
            'background_dark' => $this->background_dark,

            /** Оригинал */
            'keep_original' => (bool) $this->keep_original,

            /** Сортировка */
            'sort' => (int) $this->sort,

            /** Временные метки */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            /** Связи */
            'profile' => $this->whenLoaded('profile', function () {
                return [
                    'id' => $this->profile?->id,
                    'key' => $this->profile?->key,
                    'name' => $this->profile?->name,
                ];
            }),
        ];
    }
}
