<?php

namespace App\Http\Resources\Admin\Slider\SliderSlideAction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderSlideActionTranslationResource extends JsonResource
{
    /**
     * Ресурс перевода кнопки или действия.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slider_slide_action_id' => $this->slider_slide_action_id,
            'locale' => $this->locale,

            /** Переводимые поля */
            'label' => $this->label,
            'title' => $this->title,
            'aria_label' => $this->aria_label,

            /** Системные даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
