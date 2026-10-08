<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionCreated;
use App\Mail\Form\FormSubmissionCreatedMail;
use App\Services\Admin\Notification\EmailDeliveryService;
use App\Services\Admin\Notification\EmailSettingsService;
use App\Services\Admin\Notification\NotificationRecipientResolver;

readonly class SendFormSubmissionCreatedEmail
{
    public function __construct(
        private EmailDeliveryService          $delivery,
        private EmailSettingsService          $settings,
        private NotificationRecipientResolver $recipients
    ) {
    }

    /**
     * Отправить Email о новой заявке.
     */
    public function handle(
        FormSubmissionCreated $event
    ): void {
        if (!$this->settings->eventEnabled(
            'form_submission_created'
        )) {
            return;
        }

        $submission = $event->submission;

        // Определяем получателей.
        $users = $this->recipients
            ->formSubmissionCreated($submission);

        // Для Email оставляем пользователей
        // только с корректными адресами.
        $users = $this->recipients->withEmail($users);

        foreach ($users as $user) {
            $this->delivery->send(
                'form_submission_created',
                $user->email,
                new FormSubmissionCreatedMail($submission)
            );
        }
    }
}
