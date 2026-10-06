<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблицы уведомлений.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();

            /**
             * Класс Laravel Notification.
             */
            $table->string('type');

            /**
             * Получатель уведомления.
             *
             * Создаёт:
             * - notifiable_type;
             * - notifiable_id;
             * - составной индекс.
             */
            $table->morphs('notifiable');

            /**
             * Данные уведомления.
             *
             * Здесь будет храниться универсальная
             * структура уведомлений PulsarCMS.
             */
            $table->text('data');

            /**
             * Дата прочтения уведомления.
             *
             * null — уведомление не прочитано.
             */
            $table->timestamp('read_at')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Удаление таблицы уведомлений.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
