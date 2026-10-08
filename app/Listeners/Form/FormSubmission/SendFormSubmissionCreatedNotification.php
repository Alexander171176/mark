<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionCreated;
use App\Models\User;
use App\Notifications\Form\FormSubmission\FormSubmissionCreatedNotification;

class SendFormSubmissionCreatedNotification
{
    /**
     * Отправить внутреннее уведомление
     * о поступлении новой заявки.
     */
    public function handle(
        FormSubmissionCreated $event
    ): void {
        $submission = $event->submission;

        /**
         * Загружаем форму и владельца.
         */
        $submission->loadMissing('form.user');

        $owner = $submission->form?->user;

        /**
         * Если у формы есть владелец,
         * уведомляем только его.
         */
        if ($owner !== null) {
            $owner->notify(
                new FormSubmissionCreatedNotification(
                    $submission
                )
            );

            return;
        }

        /**
         * Если владельца нет,
         * уведомляем администраторов PulsarCMS.
         */
        User::role('admin')
            ->get()
            ->each(function (User $admin) use ($submission) {
                $admin->notify(
                    new FormSubmissionCreatedNotification(
                        $submission
                    )
                );
            });
    }
}
