<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionStatusChanged;
use App\Notifications\Form\FormSubmission\FormSubmissionStatusChangedNotification;
use App\Services\Admin\Notification\NotificationRecipientResolver;

readonly class SendFormSubmissionStatusChangedNotification
{
    public function __construct(
        private NotificationRecipientResolver $recipients
    ) {
    }

    /**
     * Отправить внутренние уведомления
     * об изменении статуса заявки.
     */
    public function handle(
        FormSubmissionStatusChanged $event
    ): void {
        $users = $this->recipients
            ->formSubmissionStatusChanged($event);

        foreach ($users as $user) {
            $user->notify(
                new FormSubmissionStatusChangedNotification(
                    $event->submission,
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
