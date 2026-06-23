<?php

namespace App\Http\Resources\Admin\System\ImagePreset;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImagePresetResource extends JsonResource
{
    /**
     * Преобразование ресурса в массив.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            /** Основные данные */
            'key' => $this->key,
            'description' => $this->description,

            /** Геометрия */
            'shape' => $this->shape,
            'width' => (int) $this->width,
            'height' => (int) $this->height,

            /** Удобные поля для интерфейса */
            'single_size' => (int) $this->single_size,
            'resolution' => $this->resolution,

            'aspect_ratio' => $this->height > 0
                ? round($this->width / $this->height, 2)
                : null,

            /** Поворот изображения и рамки */
            'image_rotation_enabled' => (bool) $this->image_rotation_enabled,
            'crop_rotation_enabled' => (bool) $this->crop_rotation_enabled,

            /** Ограничения */
            'max_file_size_kb' => (int) $this->max_file_size_kb,
            'max_file_size_mb' => round($this->max_file_size_kb / 1024, 2),

            /** Оригинал */
            'keep_original' => (bool) $this->keep_original,

            /** Сортировка */
            'sort' => (int) $this->sort,

            /** Временные метки */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
