<?php

namespace App\Events\Form\FormSubmission;

use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormSubmissionAssigned
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Создание события изменения
     * ответственного сотрудника заявки.
     */
    public function __construct(
        public FormSubmission $submission,
        public ?User $oldAssignedUser,
        public ?User $newAssignedUser,
        public ?User $user = null,
        public string $source = 'admin'
    ) {}
}
