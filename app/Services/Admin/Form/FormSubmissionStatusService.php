<?php

namespace App\Services\Admin\Form;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\Admin\Form\FormSubmissionStatusHistory\FormSubmissionStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FormSubmissionStatusService
{
    /**
     * Изменить статус заявки.
     *
     * Централизует:
     * - изменение статуса;
     * - фиксацию начала обработки;
     * - фиксацию / очистку завершения;
     * - создание истории перехода.
     *
     * Если статус не изменился,
     * запись истории не создаётся.
     */
    public function changeStatus(
        FormSubmission $submission,
        string $newStatus,
        ?User $user = null,
        string $source = 'admin',
        ?string $comment = null,
        array $metadata = []
    ): FormSubmission {
        $this->validateStatus($newStatus);

        $oldStatus = $submission->status;

        if ($oldStatus === $newStatus) {
            return $submission;
        }

        return DB::transaction(function () use (
            $submission,
            $oldStatus,
            $newStatus,
            $user,
            $source,
            $comment,
            $metadata
        ) {
            $submission->status = $newStatus;

            /**
             * Первое начало обработки.
             *
             * processed_at фиксируется один раз
             * и при последующих переходах не изменяется.
             */
            if (
                $newStatus === FormSubmission::STATUS_PROCESSING
                && $submission->processed_at === null
            ) {
                $submission->processed_at = now();
            }

            /**
             * Завершение заявки.
             *
             * При переходе в completed фиксируем время.
             * При выходе из completed очищаем его.
             */
            if ($newStatus === FormSubmission::STATUS_COMPLETED) {
                $submission->completed_at = now();
            } elseif ($oldStatus === FormSubmission::STATUS_COMPLETED) {
                $submission->completed_at = null;
            }

            $submission->save();

            FormSubmissionStatusHistory::query()->create([
                'form_submission_id' => $submission->id,
                'user_id' => $user?->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'source' => $source,
                'comment' => $comment,
                'metadata' => $metadata !== [] ? $metadata : null,
                'changed_at' => now(),
            ]);

            return $submission;
        });
    }

    /**
     * Проверить допустимость статуса.
     */
    protected function validateStatus(string $status): void
    {
        if (!in_array($status, $this->statuses(), true)) {
            throw new InvalidArgumentException(
                sprintf('Недопустимый статус заявки: %s', $status)
            );
        }
    }

    /**
     * Допустимые статусы заявки.
     *
     * @return array<int, string>
     */
    protected function statuses(): array
    {
        return [
            FormSubmission::STATUS_NEW,
            FormSubmission::STATUS_PROCESSING,
            FormSubmission::STATUS_COMPLETED,
            FormSubmission::STATUS_CANCELLED,
            FormSubmission::STATUS_SPAM,
        ];
    }
}
