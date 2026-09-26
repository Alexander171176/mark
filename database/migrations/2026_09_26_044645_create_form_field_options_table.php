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
        Schema::create('form_field_options', function (Blueprint $table) {
            $table->id();

            // Поле формы, которому принадлежит вариант.
            //
            // Используется для типов:
            // select
            // radio
            // checkbox_group
            $table->foreignId('form_field_id')
                ->constrained('form_fields')
                ->cascadeOnDelete();

            // Системное значение варианта.
            //
            // Именно это значение будет отправляться frontend
            // и сохраняться в данных заявки.
            //
            // Например:
            // office
            // warehouse
            // residential
            $table->string('value', 255);

            // Активность варианта
            $table->boolean('activity')
                ->default(true);

            // Выбран ли вариант по умолчанию
            $table->boolean('is_default')
                ->default(false);

            // Порядок отображения
            $table->unsignedInteger('sort')
                ->default(100);

            /*
            |--------------------------------------------------------------------------
            | Дополнительные настройки
            |--------------------------------------------------------------------------
            */

            // Расширяемые настройки варианта.
            //
            // Пока может быть null.
            // В будущем позволит добавлять поведение
            // без изменения структуры таблицы.
            $table->json('settings')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Индексы
            |--------------------------------------------------------------------------
            */

            // В пределах одного поля системное значение
            // варианта должно быть уникальным
            $table->unique([
                'form_field_id',
                'value',
            ]);

            // Получение активных вариантов поля
            // в правильном порядке
            $table->index([
                'form_field_id',
                'activity',
                'sort',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_field_options');
    }
};
