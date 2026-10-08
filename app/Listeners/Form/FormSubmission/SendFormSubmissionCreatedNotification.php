<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionCreated;
use App\Notifications\Form\FormSubmission\FormSubmissionCreatedNotification;
use App\Services\Admin\Notification\NotificationRecipientResolver;

readonly class SendFormSubmissionCreatedNotification
{
    public function __construct(
        private NotificationRecipientResolver $recipients
    ) {
    }

    /**
     * Отправить внутренние уведомления
     * получателям новой заявки.
     */
    public function handle(
        FormSubmissionCreated $event
    ): void {
        $submission = $event->submission;

        $users = $this->recipients
            ->formSubmissionCreated($submission);

        foreach ($users as $user) {
            $user->notify(
                new FormSubmissionCreatedNotification(
                    $submission
                )
            );
        }
    }
}
