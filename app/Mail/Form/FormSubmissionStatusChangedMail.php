<?php

namespace App\Mail\Form;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormSubmissionStatusChangedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public FormSubmission $submission,
        public string $oldStatus,
        public string $newStatus,
        public ?User $changedBy = null,
        public string $source = 'admin',
        public ?string $comment = null
    ) {
    }

    /**
     * Сформировать письмо об изменении статуса заявки.
     */
    public function build(): static
    {
        return $this
            ->subject(
                'PulsarCMS — изменён статус заявки #'
                . $this->submission->id
            )
            ->view('emails.form.submission-status-changed', [
                'submission' => $this->submission,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'changedBy' => $this->changedBy,
                'source' => $this->source,
                'comment' => $this->comment,
            ]);
    }
}
