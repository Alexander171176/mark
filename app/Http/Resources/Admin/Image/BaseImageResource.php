<?php

namespace App\Http\Resources\Admin\Image;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Базовый ресурс для всех моделей изображений
 * (LessonImage, AssignmentImage, CourseImage, ModuleImage, etc.)
 *
 * Требования к модели:
 *  - аксессоры: image_url, webp_url, thumb_url
 *  - Spatie Media-Library: getFirstMedia('images')
 */
class BaseImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $firstMedia = $this->getFirstMedia('images');

        return [
            'id'         => $this->id,
            'order'      => $this->pivot?->order ?? $this->order,
            'alt'        => $this->alt,
            'caption'    => $this->caption,

            // URL из аксессоров модели
            'url'        => $this->image_url,
            'webp_url'   => $this->webp_url,
            'thumb_url'  => $this->thumb_url,

            // Даты
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // Доп. инфо по файлу
            'mime_type'  => $firstMedia?->mime_type,
            'size'       => $firstMedia?->size,
            'size_human' => $firstMedia?->humanReadableSize,
        ];
    }
}
