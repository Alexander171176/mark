<?php

namespace App\Http\Resources\Admin\Form\Form;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormTranslationResource extends JsonResource
{
    /**
     * Представление перевода формы.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** Основные данные */
            'id' => (int) $this->id,
            'form_id' => (int) $this->form_id,
            'locale' => $this->locale,

            /** Контент */
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => $this->description,

            /** Сообщения формы */
            'submit_text' => $this->submit_text,
            'success_message' => $this->success_message,
            'error_message' => $this->error_message,

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
