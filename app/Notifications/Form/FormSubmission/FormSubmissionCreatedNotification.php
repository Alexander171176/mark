<?php

namespace App\Notifications\Form\FormSubmission;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Notifications\PulsarNotification;

class FormSubmissionCreatedNotification extends PulsarNotification
{
    /**
     * Создание уведомления
     * о поступлении новой заявки.
     */
    public function __construct(
        public FormSubmission $submission
    ) {}

    /**
     * Данные внутреннего уведомления PulsarCMS.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->databaseData(
            self::CATEGORY_FORM,
            'form_submission_created',
            self::LEVEL_INFO,
            'Получена новая заявка',
            "Получена новая заявка №{$this->submission->id}.",
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

                'form_code' =>
                    $this->submission->form?->code,

                'submitted_user_id' =>
                    $this->submission->user_id,
            ]
        );
    }
}
