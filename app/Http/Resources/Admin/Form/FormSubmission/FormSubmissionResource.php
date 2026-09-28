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
     * Полное административное представление
     * заявки формы.
     *
     * Основное назначение:
     * - Show;
     * - Edit;
     * - обработка заявки в Admin / CRM.
     *
     * Resource работает только с заранее
     * загруженными relations/counts и не должен
     * выполнять дополнительные SQL-запросы.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'form_id' => (int) $this->form_id,

            'user_id' => $this->user_id !== null
                ? (int) $this->user_id
                : null,

            'assigned_user_id' =>
                $this->assigned_user_id !== null
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

            /** Родительская форма */
            'form' => $this->whenLoaded(
                'form',
                function () {
                    return $this->form
                        ? new FormSharedResource(
                            $this->form
                        )
                        : null;
                }
            ),

            /** Авторизованный отправитель */
            'user' => $this->whenLoaded(
                'user',
                function () {
                    return $this->user
                        ? new UserSharedResource(
                            $this->user
                        )
                        : null;
                }
            ),

            /** Ответственный сотрудник */
            'assigned_user' => $this->whenLoaded(
                'assignedUser',
                function () {
                    return $this->assignedUser
                        ? new UserSharedResource(
                            $this->assignedUser
                        )
                        : null;
                }
            ),

            /** Snapshot-значения полей */
            'values' =>
                FormSubmissionValueResource::collection(
                    $this->whenLoaded(
                        'values'
                    )
                ),

            /** Прикреплённые файлы */
            'files' =>
                FormSubmissionFileResource::collection(
                    $this->whenLoaded(
                        'files'
                    )
                ),

            /** Счётчики */
            'values_count' => $this->when(
                isset($this->values_count),
                fn () => (int) $this->values_count
            ),

            'files_count' => $this->when(
                isset($this->files_count),
                fn () => (int) $this->files_count
            ),

            /** Обработка */
            'processed_at' =>
                $this->processed_at?->toISOString(),

            'completed_at' =>
                $this->completed_at?->toISOString(),

            /** Время отправки */
            'submitted_at' =>
                $this->submitted_at?->toISOString(),

            /** Timestamps */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
