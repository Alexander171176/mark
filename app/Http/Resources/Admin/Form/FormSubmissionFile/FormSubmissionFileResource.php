<?php

namespace App\Http\Resources\Admin\Form\FormSubmissionFile;

use App\Http\Resources\Admin\Form\FormField\FormFieldSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionFileResource extends JsonResource
{
    /**
     * Полное административное представление
     * файла заявки.
     *
     * Resource не создаёт публичный URL файла.
     *
     * Доступ к физическому файлу должен
     * выполняться через отдельный защищённый
     * download endpoint с проверкой прав.
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

            /** Порядок */
            'sort' => (int) $this->sort,

            /** Дополнительные метаданные */
            'metadata' => $this->metadata,

            /**
             * Исходное поле конструктора.
             *
             * Может быть null, если исходное
             * поле было удалено после отправки.
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
