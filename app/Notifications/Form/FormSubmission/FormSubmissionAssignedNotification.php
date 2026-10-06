<?php

namespace App\Notifications\Form\FormSubmission;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\User;
use App\Notifications\PulsarNotification;

class FormSubmissionAssignedNotification extends PulsarNotification
{
    /**
     * Создание уведомления
     * о назначении заявки сотруднику.
     */
    public function __construct(
        public FormSubmission $submission,
        public ?User $assignedByUser = null
    ) {}

    /**
     * Данные внутреннего уведомления PulsarCMS.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->databaseData(
            self::CATEGORY_FORM,
            'form_submission_assigned',
            self::LEVEL_INFO,
            'Вам назначена заявка',
            "Вам назначена заявка №{$this->submission->id}.",
            'form_submission',
            $this->submission->id,
            route(
                'admin.formSubmissions.show',
                $this->submission->id,
                false
            ),
            [
                'form_id' =>
                    $this->submission->form_id,

                'submission_id' =>
                    $this->submission->id,

                'assigned_user_id' =>
                    $notifiable->id,

                'assigned_by_user_id' =>
                    $this->assignedByUser?->id,
            ]
        );
    }
}
