<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionCreated;
use Illuminate\Support\Facades\Log;

class LogFormSubmissionCreated
{
    /**
     * Обработать событие создания заявки формы.
     */
    public function handle(
        FormSubmissionCreated $event
    ): void {
        Log::info(
            'FormSubmissionCreated',
            [
                'submission_id' =>
                    $event->submission->id,

                'form_id' =>
                    $event->submission->form_id,

                'user_id' =>
                    $event->submission->user_id,

                'source' =>
                    $event->submission->source,

                'locale' =>
                    $event->submission->locale,

                'values_count' =>
                    $event->submission->values->count(),

                'files_count' =>
                    $event->submission->files->count(),
            ]
        );
    }
}
