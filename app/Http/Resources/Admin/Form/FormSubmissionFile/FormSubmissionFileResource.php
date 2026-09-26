<?php

namespace App\Http\Resources\Admin\Form\FormSubmissionFile;

use App\Http\Resources\Admin\Form\FormField\FormFieldSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionFileResource extends JsonResource
{
    /**
     * Полное административное представление файла заявки.
     *
     * Resource не создаёт публичный URL файла.
     * Доступ к файлу должен выполняться через
     * защищённый download endpoint.
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
            'file_name' => $this->file_name,

            /** Хранилище */
            'path' => $this->path,
            'disk' => $this->disk,

            /** Метаданные файла */
            'mime_type' => $this->mime_type,
            'extension' => $this->extension,

            'size' => $this->size !== null
                ? (int) $this->size
                : null,

            'size_kb' => $this->getSizeInKb(),
            'size_mb' => $this->getSizeInMb(),

            'sort' => (int) $this->sort,

            'metadata' => $this->metadata,

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
