<?php

namespace App\Listeners\Form\FormSubmission;

use App\Events\Form\FormSubmission\FormSubmissionAssigned;
use Illuminate\Support\Facades\Log;

class LogFormSubmissionAssigned
{
    /**
     * Обработать событие изменения
     * ответственного сотрудника заявки.
     */
    public function handle(
        FormSubmissionAssigned $event
    ): void {
        Log::info(
            'FormSubmissionAssigned',
            [
                'submission_id' =>
                    $event->submission->id,

                'form_id' =>
                    $event->submission->form_id,

                'old_assigned_user_id' =>
                    $event->oldAssignedUser?->id,

                'new_assigned_user_id' =>
                    $event->newAssignedUser?->id,

                'user_id' =>
                    $event->user?->id,

                'source' =>
                    $event->source,
            ]
        );
    }
}
