<?php

namespace App\Http\Resources\Admin\Slider\SliderSlideAdvantage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideAdvantageTranslationResource extends JsonResource
{
    /** Перевод самостоятельного преимущества слайда. */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'slider_slide_advantage_id' => (int) $this->slider_slide_advantage_id,
            'locale' => $this->locale,

            // Переводимые поля.
            'title' => $this->title,
            'text' => $this->text,

            // Системные даты.
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
