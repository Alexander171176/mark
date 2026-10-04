<?php

namespace App\Http\Resources\Admin\Form\FormSubmissionStatusHistory;

use App\Http\Resources\Admin\System\User\UserSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormSubmissionStatusHistoryResource extends JsonResource
{
    /**
     * Административное представление
     * записи истории статуса заявки.
     *
     * Основное назначение:
     * - история обработки заявки;
     * - Admin Show;
     * - Admin Edit;
     * - будущая CRM.
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

            'user_id' => $this->user_id !== null
                ? (int) $this->user_id
                : null,

            /**
             * Переход статуса.
             */
            'from_status' => $this->from_status,
            'to_status' => $this->to_status,

            /**
             * Источник изменения.
             */
            'source' => $this->source,

            /**
             * Комментарий.
             */
            'comment' => $this->comment,

            /**
             * Дополнительный контекст события.
             */
            'metadata' => $this->metadata,

            /**
             * Пользователь, выполнивший изменение.
             *
             * Для системных событий может быть null.
             */
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

            /**
             * Время фактического изменения статуса.
             */
            'changed_at' =>
                $this->changed_at?->toISOString(),

            /**
             * Timestamps.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
