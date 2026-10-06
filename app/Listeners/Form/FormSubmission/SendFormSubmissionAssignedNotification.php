<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionAssigned;
use App\Notifications\Form\FormSubmission\FormSubmissionAssignedNotification;

class SendFormSubmissionAssignedNotification
{
    /**
     * Отправить внутреннее уведомление
     * новому ответственному сотруднику.
     */
    public function handle(
        FormSubmissionAssigned $event
    ): void {
        /**
         * Если назначение снято,
         * нового получателя уведомления нет.
         */
        if ($event->newAssignedUser === null) {
            return;
        }

        /**
         * Уведомляем нового ответственного сотрудника.
         */
        $event->newAssignedUser->notify(
            new FormSubmissionAssignedNotification(
                $event->submission,
                $event->user
            )
        );
    }
}
