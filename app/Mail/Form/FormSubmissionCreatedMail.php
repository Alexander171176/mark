<?php

namespace App\Mail\Form;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormSubmissionCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public FormSubmission $submission
    ) {
    }

    /**
     * Сформировать письмо о новой заявке.
     */
    public function build(): static
    {
        return $this
            ->subject(
                'PulsarCMS — новая заявка #' . $this->submission->id
            )
            ->view('emails.form.submission-created', [
                'submission' => $this->submission,
            ]);
    }
}
