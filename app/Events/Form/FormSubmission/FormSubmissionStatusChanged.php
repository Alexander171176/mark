<?php

namespace App\Events\Form\FormSubmission;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormSubmissionStatusChanged
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Создание события изменения статуса заявки формы.
     */
    public function __construct(
        public FormSubmission $submission,
        public string $oldStatus,
        public string $newStatus,
        public ?User $user = null,
        public string $source = 'admin',
        public ?string $comment = null,
        public array $metadata = []
    ) {}
}
