<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запуск миграции.
     */
    public function up(): void
    {
        Schema::create('notification_email_logs', function (Blueprint $table) {
            $table->id()
                ->comment('PK');

            /*
             * Уникальная идентификация отправки.
             */
            $table->uuid('uuid')
                ->unique('notification_email_logs_uuid_unique')
                ->comment('Уникальный идентификатор отправки');

            /*
             * Источник уведомления.
             */
            $table->string('event', 100)
                ->index('notification_email_logs_event_idx')
                ->comment('Событие: form_submission_created и другие');

            $table->string('recipient', 255)
                ->index('notification_email_logs_recipient_idx')
                ->comment('Email получателя');

            $table->string('subject', 500)
                ->nullable()
                ->comment('Тема письма');

            /*
             * Состояние отправки.
             */
            $table->string('status', 30)
                ->default('pending')
                ->index('notification_email_logs_status_idx')
                ->comment('pending, processing, retrying, sent, failed, skipped');

            $table->unsignedInteger('attempts')
                ->default(0)
                ->comment('Количество выполненных попыток');

            /*
             * Очередь и почтовый транспорт.
             */
            $table->string('queue', 100)
                ->default('emails')
                ->comment('Имя очереди Redis');

            $table->string('mailer', 50)
                ->nullable()
                ->comment('Почтовый транспорт');

            /*
             * Диагностика.
             */
            $table->string('error_type', 255)
                ->nullable()
                ->comment('Класс последнего исключения');

            $table->text('error_message')
                ->nullable()
                ->comment('Безопасное описание последней ошибки');

            /*
             * Временные отметки.
             */
            $table->timestamp('queued_at')
                ->nullable()
                ->comment('Постановка в очередь');

            $table->timestamp('processing_at')
                ->nullable()
                ->comment('Начало последней попытки');

            $table->timestamp('sent_at')
                ->nullable()
                ->comment('Передача почтовому транспорту');

            $table->timestamp('failed_at')
                ->nullable()
                ->comment('Окончательная ошибка');

            $table->timestamps();

            /*
             * Быстрые выборки для административной панели.
             */
            $table->index(
                ['status', 'created_at'],
                'notification_email_logs_status_date_idx'
            );

            $table->index(
                ['event', 'created_at'],
                'notification_email_logs_event_date_idx'
            );

            $table->index(
                ['queue', 'status'],
                'notification_email_logs_queue_status_idx'
            );

            $table->comment(
                'PulsarCMS: журнал асинхронных Email-уведомлений.'
            );
        });
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_email_logs');
    }
};
