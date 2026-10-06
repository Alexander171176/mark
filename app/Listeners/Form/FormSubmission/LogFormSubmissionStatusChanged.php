<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionStatusChanged;
use Illuminate\Support\Facades\Log;

class LogFormSubmissionStatusChanged
{
    /**
     * Обработать событие изменения статуса заявки формы.
     */
    public function handle(
        FormSubmissionStatusChanged $event
    ): void {
        Log::info(
            'FormSubmissionStatusChanged',
            [
                'submission_id' =>
                    $event->submission->id,

                'form_id' =>
                    $event->submission->form_id,

                'old_status' =>
                    $event->oldStatus,

                'new_status' =>
                    $event->newStatus,

                'user_id' =>
                    $event->user?->id,

                'source' =>
                    $event->source,

                'comment' =>
                    $event->comment,

                'metadata' =>
                    $event->metadata,
            ]
        );
    }
}
