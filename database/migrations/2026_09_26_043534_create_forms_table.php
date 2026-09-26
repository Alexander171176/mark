<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();

            // Владелец формы
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Уникальный системный код формы.
            // Используется для подключения формы на страницах приложения:
            // consultation, selection, specification, callback и т.д.
            $table->string('code', 100)
                ->unique();

            // Статус формы:
            // draft     - черновик
            // published - опубликована
            // archived  - архивная
            $table->string('status', 50)
                ->default('draft');

            // Активность формы
            $table->boolean('activity')
                ->default(true);

            // Порядок сортировки
            $table->unsignedInteger('sort')
                ->default(100);

            /*
            |--------------------------------------------------------------------------
            | Защита от спама
            |--------------------------------------------------------------------------
            */

            // Общая встроенная защита от спама
            $table->boolean('spam_protection')
                ->default(true);

            // Honeypot-поле
            $table->boolean('honeypot_enabled')
                ->default(true);

            // Минимальное время заполнения формы в секундах
            $table->unsignedSmallInteger('min_submit_seconds')
                ->default(3);

            // Максимальное количество отправок за указанный период.
            // 0 - ограничение отключено.
            $table->unsignedSmallInteger('rate_limit')
                ->default(5);

            // Период ограничения в минутах
            $table->unsignedSmallInteger('rate_limit_minutes')
                ->default(10);

            // CAPTCHA.
            // По умолчанию выключена.
            // Конкретный CAPTCHA-провайдер будет задаваться
            // глобальными настройками приложения.
            $table->boolean('captcha_enabled')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Поведение формы
            |--------------------------------------------------------------------------
            */

            // Разрешить отправку формы только авторизованным пользователям
            $table->boolean('auth_required')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            // Расширяемые настройки формы.
            // Позволяет добавлять новые возможности без изменения структуры БД.
            $table->json('settings')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Индексы
            |--------------------------------------------------------------------------
            */

            $table->index(['activity', 'status']);
            $table->index(['activity', 'sort']);
            $table->index(['user_id', 'activity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
