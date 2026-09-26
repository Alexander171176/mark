<?php

namespace App\Http\Resources\Admin\Form\FormSubmission;

use App\Http\Resources\Admin\Form\Form\FormSharedResource;
use App\Http\Resources\Admin\Form\FormSubmissionFile\FormSubmissionFileResource;
use App\Http\Resources\Admin\Form\FormSubmissionValue\FormSubmissionValueResource;
use App\Http\Resources\Admin\System\User\UserSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionResource extends JsonResource
{
    /**
     * Полное административное представление заявки формы.
     *
     * Основное назначение:
     * - Show;
     * - Edit;
     * - обработка заявки в административной панели / CRM.
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
            'page_url' => $this->page_url,
            'locale' => $this->locale,

            /** Контекст */
            'context' => $this->context,

            /** Маркетинговые данные */
            'utm_source' => $this->utm_source,
            'utm_medium' => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_content' => $this->utm_content,
            'utm_term' => $this->utm_term,

            /** Технические данные */
            'ip' => $this->ip,
            'user_agent' => $this->user_agent,
            'session_id' => $this->session_id,

            /** Форма */
            'form' => new FormSharedResource(
                $this->whenLoaded('form')
            ),

            /** Авторизованный отправитель */
            'user' => new UserSharedResource(
                $this->whenLoaded('user')
            ),

            /** Ответственный сотрудник */
            'assigned_user' => new UserSharedResource(
                $this->whenLoaded('assignedUser')
            ),

            /** Значения полей */
            'values' => FormSubmissionValueResource::collection(
                $this->whenLoaded('values')
            ),

            /** Файлы */
            'files' => FormSubmissionFileResource::collection(
                $this->whenLoaded('files')
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
