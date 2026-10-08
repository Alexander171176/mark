<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionAssigned;
use App\Notifications\Form\FormSubmission\FormSubmissionAssignedNotification;
use App\Services\Admin\Notification\NotificationRecipientResolver;

readonly class SendFormSubmissionAssignedNotification
{
    public function __construct(
        private NotificationRecipientResolver $recipients
    ) {
    }

    /**
     * Отправить внутреннее уведомление
     * новому ответственному сотруднику.
     */
    public function handle(
        FormSubmissionAssigned $event
    ): void {
        $users = $this->recipients
            ->formSubmissionAssigned($event);

        foreach ($users as $user) {
            $user->notify(
                new FormSubmissionAssignedNotification(
                    $event->submission,
                    $event->user
                )
            );
        }
    }
}
