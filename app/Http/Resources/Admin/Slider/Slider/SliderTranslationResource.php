<?php

namespace App\Http\Resources\Admin\Slider\Slider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderTranslationResource extends JsonResource
{
    /**
     * Преобразование перевода слайдера.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slider_id' => $this->slider_id,
            'locale' => $this->locale,

            /** Переводимые поля */
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => $this->description,

            /** SEO */
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,

            /** Системные даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
