<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionStatusChanged;
use App\Jobs\SendNotificationEmailJob;
use App\Mail\Form\FormSubmissionStatusChangedMail;
use App\Services\Admin\Notification\EmailSettingsService;
use App\Services\Admin\Notification\NotificationRecipientResolver;

readonly class SendFormSubmissionStatusChangedEmail
{
    public function __construct(
        private EmailSettingsService $settings,
        private NotificationRecipientResolver $recipients
    ) {
    }

    public function handle(FormSubmissionStatusChanged $event): void
    {
        if (!$this->settings->eventEnabled('form_submission_status_changed')) {
            return;
        }

        $users = $this->recipients->withEmail(
            $this->recipients->formSubmissionStatusChanged($event)
        );

        foreach ($users as $user) {
            SendNotificationEmailJob::dispatch(
                'form_submission_status_changed',
                $user->email,
                new FormSubmissionStatusChangedMail(
                    $event->submission,
                    $event->oldStatus,
                    $event->newStatus,
                    $event->user,
                    $event->source,
                    $event->comment
                )
            )->afterCommit();
        }
    }
}
