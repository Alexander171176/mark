<?php

namespace App\Http\Resources\Admin\Form\FormSubmission;

use App\Http\Resources\Admin\Form\Form\FormSharedResource;
use App\Http\Resources\Admin\System\User\UserSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionSharedResource extends JsonResource
{
    /**
     * Компактное представление заявки.
     *
     * Основное назначение:
     * - Admin Index;
     * - связанные сущности;
     * - административные списки заявок.
     *
     * Resource читает только заранее загруженные
     * relations/counts и не должен создавать
     * дополнительные SQL-запросы.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** Основные данные */
            'id' => (int) $this->id,

            'form_id' => (int) $this->form_id,

            'user_id' => $this->user_id !== null
                ? (int) $this->user_id
                : null,

            'assigned_user_id' => $this->assigned_user_id !== null
                ? (int) $this->assigned_user_id
                : null,

            /** Состояние */
            'status' => $this->status,

            /** Источник */
            'source' => $this->source,
            'locale' => $this->locale,

            /** Форма */
            'form' => new FormSharedResource(
                $this->whenLoaded('form')
            ),

            /** Отправитель */
            'user' => new UserSharedResource(
                $this->whenLoaded('user')
            ),

            /** Ответственный */
            'assigned_user' => new UserSharedResource(
                $this->whenLoaded('assignedUser')
            ),

            /** Счётчики */
            'values_count' => $this->whenCounted('values'),
            'files_count' => $this->whenCounted('files'),

            /** Обработка */
            'processed_at' => $this->processed_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),

            /** Время отправки */
            'submitted_at' => $this->submitted_at?->toISOString(),

            /** Технические даты */
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
