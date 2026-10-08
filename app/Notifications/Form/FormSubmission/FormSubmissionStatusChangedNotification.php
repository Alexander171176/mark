<?php

namespace App\Notifications\Form\FormSubmission;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\User;
use App\Notifications\PulsarNotification;

class FormSubmissionStatusChangedNotification extends PulsarNotification
{
    /**
     * Уведомление об изменении статуса заявки.
     */
    public function __construct(
        public FormSubmission $submission,
        public string $oldStatus,
        public string $newStatus,
        public ?User $changedByUser = null,
        public string $source = 'admin',
        public ?string $comment = null
    ) {}

    /**
     * Данные внутреннего уведомления PulsarCMS.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->databaseData(
            self::CATEGORY_FORM,
            'form_submission_status_changed',
            self::LEVEL_INFO,
            'Изменён статус заявки',
            "Статус заявки №{$this->submission->id} изменён: {$this->oldStatus} → {$this->newStatus}.",
            'form_submission',
            $this->submission->id,
            route(
                'admin.formSubmissions.show',
                $this->submission->id,
                false
            ),
            [
                'form_id' => $this->submission->form_id,
                'submission_id' => $this->submission->id,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
                'changed_by_user_id' => $this->changedByUser?->id,
                'source' => $this->source,
                'comment' => $this->comment,
            ]
        );
    }
}
