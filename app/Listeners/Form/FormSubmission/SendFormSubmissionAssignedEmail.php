<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionAssigned;
use App\Mail\Form\FormSubmissionAssignedMail;
use App\Services\Admin\Notification\EmailDeliveryService;
use App\Services\Admin\Notification\EmailSettingsService;
use App\Services\Admin\Notification\NotificationRecipientResolver;

readonly class SendFormSubmissionAssignedEmail
{
    public function __construct(
        private EmailDeliveryService $delivery,
        private EmailSettingsService $settings,
        private NotificationRecipientResolver $recipients
    ) {
    }

    /**
     * Отправить Email новому ответственному
     * сотруднику при назначении заявки.
     */
    public function handle(
        FormSubmissionAssigned $event
    ): void {
        // Проверяем настройку отправки Email.
        if (!$this->settings->eventEnabled(
            'form_submission_assigned'
        )) {
            return;
        }

        // Получатели определяются по событию.
        // При снятии назначения список будет пустым.
        $users = $this->recipients
            ->formSubmissionAssigned($event);

        // Оставляем только корректные Email.
        $users = $this->recipients->withEmail($users);

        foreach ($users as $user) {
            $this->delivery->send(
                'form_submission_assigned',
                $user->email,
                new FormSubmissionAssignedMail(
                    $event->submission,
                    $user,
                    $event->user
                )
            );
        }
    }
}
