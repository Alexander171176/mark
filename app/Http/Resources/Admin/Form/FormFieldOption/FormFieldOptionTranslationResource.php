<?php

namespace App\Http\Resources\Admin\Form\FormFieldOption;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldOptionTranslationResource extends JsonResource
{
    /**
     * Представление перевода варианта поля формы.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** Основные данные */
            'id' => (int) $this->id,
            'form_field_option_id' => (int) $this->form_field_option_id,
            'locale' => $this->locale,

            /** Контент */
            'label' => $this->label,
            'description' => $this->description,

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
