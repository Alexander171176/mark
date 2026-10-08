<?php

namespace App\Providers;

use App\Events\Form\FormSubmission\FormSubmissionAssigned;
use App\Events\Form\FormSubmission\FormSubmissionCreated;
use App\Events\Form\FormSubmission\FormSubmissionStatusChanged;
use App\Listeners\Form\FormSubmission\LogFormSubmissionAssigned;
use App\Listeners\Form\FormSubmission\LogFormSubmissionCreated;
use App\Listeners\Form\FormSubmission\LogFormSubmissionStatusChanged;
use App\Listeners\Form\FormSubmission\SendFormSubmissionAssignedNotification;
use App\Listeners\Form\FormSubmission\SendFormSubmissionCreatedNotification;
use App\Listeners\Form\FormSubmission\SendFormSubmissionStatusChangedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Сопоставления событий и слушателей приложения.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        /**
         * Системные события Laravel.
         */
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        /**
         * События заявок динамических форм.
         */
        FormSubmissionCreated::class => [
            LogFormSubmissionCreated::class,
            SendFormSubmissionCreatedNotification::class,
        ],

        FormSubmissionStatusChanged::class => [
            LogFormSubmissionStatusChanged::class,
            SendFormSubmissionStatusChangedNotification::class,
        ],

        FormSubmissionAssigned::class => [
            LogFormSubmissionAssigned::class,
            SendFormSubmissionAssignedNotification::class,
        ],
    ];

    /**
     * Зарегистрировать события приложения.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Определяет, должны ли события и слушатели
     * автоматически обнаруживаться Laravel.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
