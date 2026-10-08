<?php

namespace App\Mail\Form;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormSubmissionAssignedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public FormSubmission $submission,
        public User $assignedUser,
        public ?User $assignedBy = null
    ) {
    }

    /**
     * Сформировать письмо о назначении
     * ответственного сотрудника.
     */
    public function build(): static
    {
        return $this
            ->subject(
                'PulsarCMS — вам назначена заявка #'
                . $this->submission->id
            )
            ->view('emails.form.submission-assigned', [
                'submission' => $this->submission,
                'assignedUser' => $this->assignedUser,
                'assignedBy' => $this->assignedBy,
            ]);
    }
}
