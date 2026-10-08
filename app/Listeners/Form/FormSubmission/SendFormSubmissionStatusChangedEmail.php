<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionStatusChanged;
use App\Mail\Form\FormSubmissionStatusChangedMail;
use App\Services\Admin\Notification\EmailDeliveryService;
use App\Services\Admin\Notification\EmailSettingsService;
use App\Services\Admin\Notification\NotificationRecipientResolver;

readonly class SendFormSubmissionStatusChangedEmail
{
    public function __construct(
        private EmailDeliveryService $delivery,
        private EmailSettingsService $settings,
        private NotificationRecipientResolver $recipients
    ) {
    }

    /**
     * Отправить Email при изменении статуса заявки.
     */
    public function handle(
        FormSubmissionStatusChanged $event
    ): void {
        // Проверяем, разрешена ли отправка Email.
        if (!$this->settings->eventEnabled(
            'form_submission_status_changed'
        )) {
            return;
        }

        // Определяем получателей на основании события.
        $users = $this->recipients
            ->formSubmissionStatusChanged($event);

        // Оставляем пользователей с корректными Email.
        $users = $this->recipients->withEmail($users);

        foreach ($users as $user) {
            $this->delivery->send(
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
            );
        }
    }
}
