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
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();

            // Форма, которой принадлежит поле
            $table->foreignId('form_id')
                ->constrained('forms')
                ->cascadeOnDelete();

            // Системное имя поля:
            // name, phone, email, company, message и т.д.
            //
            // Используется при отправке формы и сохранении значения.
            $table->string('name', 100);

            // Тип поля:
            // text
            // textarea
            // email
            // tel
            // number
            // select
            // radio
            // checkbox
            // checkbox_group
            // file
            // date
            // datetime
            // hidden
            // url
            // password
            // range
            //
            // ENUM не используем.
            // Доступные типы будут определяться конфигурацией приложения.
            $table->string('type', 50)
                ->default('text');

            // Активность поля
            $table->boolean('activity')
                ->default(true);

            // Обязательное поле
            $table->boolean('required')
                ->default(false);

            // Только для чтения
            $table->boolean('readonly')
                ->default(false);

            // Отключённое поле
            $table->boolean('disabled')
                ->default(false);

            // Порядок отображения поля
            $table->unsignedInteger('sort')
                ->default(100);

            /*
            |--------------------------------------------------------------------------
            | Значения
            |--------------------------------------------------------------------------
            */

            // Значение по умолчанию.
            //
            // Для большинства простых полей этого достаточно.
            // Сложные значения могут храниться в settings.
            $table->text('default_value')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Валидация
            |--------------------------------------------------------------------------
            */

            // Правила Laravel Validation.
            //
            // Например:
            // ["string", "max:255"]
            // ["email", "max:255"]
            //
            // required намеренно хранится отдельной колонкой,
            // чтобы конструктор мог удобно управлять обязательностью поля.
            $table->json('validation')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Отображение
            |--------------------------------------------------------------------------
            */

            // Ширина поля в сетке формы.
            //
            // Например:
            // full
            // 1/2
            // 1/3
            // 2/3
            //
            // Конкретное преобразование в Tailwind-классы
            // выполняется на frontend.
            $table->string('width', 20)
                ->default('full');

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            // Настройки, специфичные для конкретного типа поля.
            //
            // Например textarea:
            // {
            //     "rows": 5,
            //     "maxlength": 2000
            // }
            //
            // Например number:
            // {
            //     "min": 0,
            //     "max": 1000,
            //     "step": 1
            // }
            //
            // Например file:
            // {
            //     "multiple": true,
            //     "max_files": 5,
            //     "max_size": 10485760,
            //     "extensions": ["pdf", "doc", "docx"]
            // }
            $table->json('settings')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Индексы
            |--------------------------------------------------------------------------
            */

            // В одной форме системное имя поля должно быть уникальным
            $table->unique(['form_id', 'name']);

            // Получение активных полей формы в нужном порядке
            $table->index(['form_id', 'activity', 'sort']);

            // Для административной фильтрации по типам полей
            $table->index(['form_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
