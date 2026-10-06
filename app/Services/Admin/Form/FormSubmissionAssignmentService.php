<?php

namespace App\Services\Admin\Form;

use App\Events\Form\FormSubmission\FormSubmissionAssigned;
use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FormSubmissionAssignmentService
{
    /**
     * Изменить ответственного сотрудника заявки.
     *
     * Централизует:
     * - проверку фактического изменения;
     * - изменение assigned_user_id;
     * - отправку события изменения назначения.
     *
     * Если ответственный сотрудник не изменился,
     * заявка не сохраняется и событие не создаётся.
     */
    public function assign(
        FormSubmission $submission,
        ?User $assignedUser,
        ?User $user = null,
        string $source = 'admin'
    ): FormSubmission {
        $oldAssignedUserId =
            $submission->assigned_user_id;

        $newAssignedUserId =
            $assignedUser?->id;

        /**
         * Ответственный сотрудник
         * фактически не изменился.
         */
        if (
            $oldAssignedUserId ===
            $newAssignedUserId
        ) {
            return $submission;
        }

        /**
         * Сохраняем предыдущего ответственного
         * для передачи в событие.
         */
        $oldAssignedUser =
            $oldAssignedUserId !== null
                ? User::query()->find(
                $oldAssignedUserId
            )
                : null;

        /**
         * Изменение ответственного сотрудника
         * выполняем внутри транзакции.
         */
        $submission = DB::transaction(
            function () use (
                $submission,
                $newAssignedUserId
            ): FormSubmission {
                $submission->assigned_user_id =
                    $newAssignedUserId;

                $submission->save();

                return $submission;
            }
        );

        /**
         * Событие отправляем только после
         * успешного завершения транзакции.
         */
        FormSubmissionAssigned::dispatch(
            $submission,
            $oldAssignedUser,
            $assignedUser,
            $user,
            $source
        );

        return $submission;
    }
}
