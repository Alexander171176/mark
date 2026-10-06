<?php

namespace App\Events\Form\FormSubmission;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormSubmissionCreated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Создание события новой заявки формы.
     */
    public function __construct(
        public FormSubmission $submission
    ) {}
}
