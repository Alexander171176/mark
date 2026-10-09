<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionAssigned;
use App\Jobs\SendNotificationEmailJob;
use App\Mail\Form\FormSubmissionAssignedMail;
use App\Services\Admin\Notification\EmailSettingsService;
use App\Services\Admin\Notification\NotificationRecipientResolver;

readonly class SendFormSubmissionAssignedEmail
{
    public function __construct(
        private EmailSettingsService $settings,
        private NotificationRecipientResolver $recipients
    ) {
    }

    public function handle(FormSubmissionAssigned $event): void
    {
        if (!$this->settings->eventEnabled('form_submission_assigned')) {
            return;
        }

        $users = $this->recipients->withEmail(
            $this->recipients->formSubmissionAssigned($event)
        );

        foreach ($users as $user) {
            SendNotificationEmailJob::dispatch(
                'form_submission_assigned',
                $user->email,
                new FormSubmissionAssignedMail(
                    $event->submission,
                    $user,
                    $event->user
                )
            )->afterCommit();
        }
    }
}
