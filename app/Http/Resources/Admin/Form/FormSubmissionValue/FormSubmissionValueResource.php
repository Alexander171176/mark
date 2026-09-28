<?php

namespace App\Http\Resources\Admin\Form\FormSubmissionValue;

use App\Http\Resources\Admin\Form\FormField\FormFieldSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionValueResource extends JsonResource
{
    /**
     * Представление сохранённого значения
     * поля заявки.
     *
     * Snapshot-данные являются основным
     * историческим источником информации
     * о поле на момент отправки формы.
     *
     * Связь field является дополнительной:
     * исходное поле конструктора впоследствии
     * может быть изменено или удалено.
     *
     * Resource работает только с заранее
     * загруженными relations и не должен
     * выполнять дополнительные SQL-запросы.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'form_submission_id' =>
                (int) $this->form_submission_id,

            'form_field_id' =>
                $this->form_field_id !== null
                    ? (int) $this->form_field_id
                    : null,

            /** Snapshot поля */
            'field_name' => $this->field_name,
            'field_type' => $this->field_type,
            'field_label' => $this->field_label,

            /** Сохранённые значения */
            'value' => $this->value,
            'value_json' => $this->value_json,
            'display_value' => $this->display_value,

            /** Нормализованные значения */
            'raw_value' => $this->getRawValue(),

            'resolved_display_value' =>
                $this->getDisplayValue(),

            'is_multiple' =>
                $this->isMultiple(),

            /**
             * Исходное поле конструктора.
             *
             * Может быть null, если поле
             * было удалено после отправки заявки.
             */
            'field' => $this->whenLoaded(
                'field',
                function () {
                    return $this->field
                        ? new FormFieldSharedResource(
                            $this->field
                        )
                        : null;
                }
            ),

            /** Timestamps */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
