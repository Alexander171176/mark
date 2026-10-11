<?php

namespace App\Http\Resources\Admin\Slider\SliderSlide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideTranslationResource extends JsonResource
{
    /**
     * Перевод текстового содержимого слайда.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slider_slide_id' => $this->slider_slide_id,
            'locale' => $this->locale,

            /** Переводимые поля */
            'label' => $this->label,
            'title' => $this->title,
            'accent' => $this->accent,
            'description' => $this->description,

            /** Системные даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
