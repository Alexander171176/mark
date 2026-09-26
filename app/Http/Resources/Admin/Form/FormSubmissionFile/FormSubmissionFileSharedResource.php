<?php

namespace App\Http\Resources\Admin\Form\FormSubmissionFile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionFileSharedResource extends JsonResource
{
    /**
     * Компактное представление файла заявки.
     *
     * Resource не раскрывает физический путь
     * или Laravel filesystem disk.
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
            'field_label' => $this->field_label,

            /** Файл */
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'extension' => $this->extension,

            /** Размер */
            'size' => $this->size !== null
                ? (int) $this->size
                : null,

            'size_kb' => $this->getSizeInKb(),
            'size_mb' => $this->getSizeInMb(),

            /** Порядок */
            'sort' => (int) $this->sort,

            /** Даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
