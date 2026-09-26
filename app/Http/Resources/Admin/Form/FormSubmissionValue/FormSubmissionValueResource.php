<?php

namespace App\Http\Resources\Admin\Form\FormSubmissionValue;

use App\Http\Resources\Admin\Form\FormField\FormFieldSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionValueResource extends JsonResource
{
    /**
     * Представление сохранённого значения поля заявки.
     *
     * Исторические snapshot-данные являются основным
     * источником информации о поле на момент отправки.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** Основные данные */
            'id' => (int) $this->id,
            'form_submission_id' => (int) $this->form_submission_id,

            'form_field_id' => $this->form_field_id !== null
                ? (int) $this->form_field_id
                : null,

            /** Snapshot поля */
            'field_name' => $this->field_name,
            'field_type' => $this->field_type,
            'field_label' => $this->field_label,

            /** Значения */
            'value' => $this->value,
            'value_json' => $this->value_json,
            'display_value' => $this->display_value,

            /** Нормализованные значения */
            'raw_value' => $this->getRawValue(),
            'resolved_display_value' => $this->getDisplayValue(),
            'is_multiple' => $this->isMultiple(),

            /** Исходное поле конструктора */
            'field' => new FormFieldSharedResource(
                $this->whenLoaded('field')
            ),

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
