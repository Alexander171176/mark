<?php

namespace App\Http\Resources\Admin\Constructor\HomePage\Reason;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReasonItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'section_id' => (int) $this->section_id,

            // Контент
            'align'      => $this->align,          // хранится в БД
            'layout'     => $this->align,          // алиас для фронта
            'title'      => $this->title,
            'text'       => $this->text,

            // ALT
            'image_alt'  => $this->image_alt,
            'light_alt'  => $this->light_alt,
            'dark_alt'   => $this->dark_alt,

            // Одиночная схема (если добавите аксессоры — отрендерится)
            'image_url'       => $this->when(isset($this->image_url), $this->image_url),
            'image_webp_url'  => $this->when(isset($this->image_webp_url), $this->image_webp_url),
            'image_thumb_url' => $this->when(isset($this->image_thumb_url), $this->image_thumb_url),

            // Двойная схема (реальные аксессоры уже есть в модели)
            'light_image_url' => $this->light_image_url,
            'light_webp_url'  => $this->light_webp_url,
            'light_thumb_url' => $this->light_thumb_url,

            'dark_image_url'  => $this->dark_image_url,
            'dark_webp_url'   => $this->dark_webp_url,
            'dark_thumb_url'  => $this->dark_thumb_url,

            // Технические
            'sort'       => (int) ($this->sort ?? 0),
            'activity'   => (bool) ($this->activity ?? true),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
