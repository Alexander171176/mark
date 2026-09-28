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
            'locale' => $this->locale,

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
