<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionStatusChanged;
use App\Models\User;
use App\Notifications\Form\FormSubmission\FormSubmissionStatusChangedNotification;
use Illuminate\Support\Collection;

class SendFormSubmissionStatusChangedNotification
{
    /**
     * Отправить уведомления об изменении статуса заявки.
     */
    public function handle(
        FormSubmissionStatusChanged $event
    ): void {
        /**
         * Не отправляем уведомление,
         * если статус фактически не изменился.
         */
        if ($event->oldStatus === $event->newStatus) {
            return;
        }

        $submission = $event->submission;

        /**
         * Загружаем связи с формой, владельцем
         * и ответственным сотрудником.
         */
        $submission->loadMissing([
            'form.user',
            'assignedUser',
        ]);

        $owner = $submission->form?->user;
        $assignedUser = $submission->assignedUser;

        /**
         * Формируем список получателей.
         */
        $recipients = collect();

        if ($owner !== null) {
            $recipients->push($owner);
        }

        if ($assignedUser !== null) {
            $recipients->push($assignedUser);
        }

        /**
         * Если нет ни владельца формы,
         * ни ответственного сотрудника,
         * уведомляем администраторов.
         */
        if ($recipients->isEmpty()) {
            $recipients = User::role('admin')->get();
        }

        /**
         * Убираем инициатора изменения статуса
         * и дублирующихся получателей.
         */
        $recipients = $recipients
            ->filter(
                fn (User $user) =>
                    $event->user === null
                    || $user->id !== $event->user->id
            )
            ->unique('id');

        /**
         * Отправляем уведомления.
         */
        foreach ($recipients as $recipient) {
            $recipient->notify(
                new FormSubmissionStatusChangedNotification(
                    $submission,
                    $event->oldStatus,
                    $event->newStatus,
                    $event->user,
                    $event->source,
                    $event->comment
                )
            );
        }
    }
}
