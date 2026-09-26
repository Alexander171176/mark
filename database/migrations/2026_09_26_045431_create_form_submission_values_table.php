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
        Schema::create('form_submission_values', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Связи
            |--------------------------------------------------------------------------
            */

            // Отправленная заявка
            $table->foreignId('form_submission_id')
                ->constrained('form_submissions')
                ->cascadeOnDelete();

            // Исходное поле конструктора формы.
            //
            // nullable намеренно:
            // поле формы впоследствии может быть удалено,
            // но историческое значение заявки должно сохраниться.
            $table->foreignId('form_field_id')
                ->nullable()
                ->constrained('form_fields')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Snapshot поля
            |--------------------------------------------------------------------------
            */

            // Системное имя поля на момент отправки.
            //
            // Например:
            // name
            // phone
            // email
            // equipment_type
            $table->string('field_name', 100);

            // Тип поля на момент отправки.
            //
            // Например:
            // text
            // tel
            // select
            // checkbox_group
            $table->string('field_type', 50);

            // Название поля на языке,
            // на котором пользователь отправил форму.
            //
            // Например:
            // "Ваше имя"
            // "Телефон"
            // "Тип оборудования"
            $table->string('field_label')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Значение
            |--------------------------------------------------------------------------
            */

            // Основное значение поля.
            //
            // Используется для одиночных значений:
            // text, textarea, email, tel, number,
            // select, radio, date и т.д.
            $table->longText('value')
                ->nullable();

            // Структурированное значение.
            //
            // Используется там, где поле может содержать
            // массив или сложную структуру.
            //
            // Например checkbox_group:
            // ["ventilation", "vrf", "chiller"]
            $table->json('value_json')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Отображаемое значение
            |--------------------------------------------------------------------------
            */

            // Snapshot отображаемого пользователю значения.
            //
            // Особенно полезно для:
            // select
            // radio
            // checkbox_group
            //
            // Например:
            //
            // value:
            // commercial
            //
            // display_value:
            // Коммерческий объект
            //
            // Даже если название варианта позднее изменится,
            // в старой заявке останется исходный текст.
            $table->longText('display_value')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Индексы
            |--------------------------------------------------------------------------
            */

            // В одной заявке одно системное поле
            // должно иметь одну запись.
            $table->unique([
                'form_submission_id',
                'field_name',
            ]);

            // Получение значений конкретного поля
            $table->index('form_field_id');

            // Поиск значений по системному имени поля
            $table->index([
                'field_name',
                'form_submission_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submission_values');
    }
};
