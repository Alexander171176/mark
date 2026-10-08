<?php

namespace App\Services\Admin\Notification;

use App\Events\Form\FormSubmission\FormSubmissionAssigned;
use App\Events\Form\FormSubmission\FormSubmissionStatusChanged;
use App\Models\Admin\Form\FormSubmission\FormSubmission;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationRecipientResolver
{
    /**
     * Получатели уведомления о создании заявки.
     *
     * Если у формы есть владелец — только он.
     * Если владельца нет — администраторы.
     *
     * @return Collection<int, User>
     */
    public function formSubmissionCreated(
        FormSubmission $submission
    ): Collection {
        $submission->loadMissing('form.user');

        $owner = $submission->form?->user;

        if ($owner !== null) {
            return collect([$owner]);
        }

        return $this->administrators();
    }

    /**
     * Получатели уведомления о назначении заявки.
     *
     * Уведомляем только нового ответственного.
     * При снятии назначения список пустой.
     *
     * Используем данные события, а не текущее
     * состояние модели, чтобы не потерять
     * информацию о конкретном назначении.
     *
     * @return Collection<int, User>
     */
    public function formSubmissionAssigned(
        FormSubmissionAssigned $event
    ): Collection {
        if ($event->newAssignedUser === null) {
            return collect();
        }

        return collect([$event->newAssignedUser]);
    }

    /**
     * Получатели уведомления об изменении статуса.
     *
     * Правила:
     * 1. Статус должен действительно измениться.
     * 2. Получатели — владелец формы и исполнитель.
     * 3. Если оба отсутствуют — администраторы.
     * 4. Исключаем инициатора изменения.
     * 5. Убираем дубликаты.
     *
     * @return Collection<int, User>
     */
    public function formSubmissionStatusChanged(
        FormSubmissionStatusChanged $event
    ): Collection {
        if ($event->oldStatus === $event->newStatus) {
            return collect();
        }

        $submission = $event->submission;

        $submission->loadMissing([
            'form.user',
            'assignedUser',
        ]);

        $recipients = collect();

        $owner = $submission->form?->user;
        $assignedUser = $submission->assignedUser;

        if ($owner !== null) {
            $recipients->push($owner);
        }

        if ($assignedUser !== null) {
            $recipients->push($assignedUser);
        }

        // Резервные получатели — администраторы.
        if ($recipients->isEmpty()) {
            $recipients = $this->administrators();
        }

        // Исключаем инициатора и повторяющихся пользователей.
        return $this->unique(
            $this->exceptUser(
                $recipients,
                $event->user
            )
        );
    }

    /**
     * Получить администраторов PulsarCMS.
     *
     * @return Collection<int, User>
     */
    public function administrators(): Collection
    {
        return User::role('admin')->get();
    }

    /**
     * Исключить пользователя из получателей.
     *
     * @param Collection<int, User> $recipients
     * @return Collection<int, User>
     */
    public function exceptUser(
        Collection $recipients,
        ?User $user
    ): Collection {
        if ($user === null) {
            return $recipients;
        }

        return $recipients
            ->reject(
                fn (User $recipient) =>
                    (string) $recipient->getKey()
                    === (string) $user->getKey()
            )
            ->values();
    }

    /**
     * Удалить повторяющихся пользователей.
     *
     * @param Collection<int, User> $recipients
     * @return Collection<int, User>
     */
    public function unique(
        Collection $recipients
    ): Collection {
        return $recipients
            ->unique(
                fn (User $user) =>
                (string) $user->getKey()
            )
            ->values();
    }

    /**
     * Оставить пользователей с корректным Email.
     *
     * @param Collection<int, User> $recipients
     * @return Collection<int, User>
     */
    public function withEmail(
        Collection $recipients
    ): Collection {
        return $recipients
            ->filter(
                fn (User $user) =>
                    is_string($user->email)
                    && filter_var(
                        $user->email,
                        FILTER_VALIDATE_EMAIL
                    ) !== false
            )
            ->values();
    }
}
