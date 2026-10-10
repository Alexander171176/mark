<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionCreated;
use App\Mail\Form\FormSubmissionCreatedMail;
use App\Services\Admin\Notification\EmailSettingsService;
use App\Services\Admin\Notification\NotificationEmailLogService;
use App\Services\Admin\Notification\NotificationRecipientResolver;

readonly class SendFormSubmissionCreatedEmail
{
    public function __construct(
        private EmailSettingsService $settings,
        private NotificationRecipientResolver $recipients,
        private NotificationEmailLogService $emailLogs
    ) {
    }

    public function handle(FormSubmissionCreated $event): void
    {
        if (!$this->settings->eventEnabled('form_submission_created')) {
            return;
        }

        $submission = $event->submission;

        $users = $this->recipients->withEmail(
            $this->recipients->formSubmissionCreated($submission)
        );

        foreach ($users as $user) {
            $this->emailLogs->dispatch(
                'form_submission_created',
                $user->email,
                new FormSubmissionCreatedMail($submission)
            );
        }
    }
}
