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
        Schema::create('form_field_translations', function (Blueprint $table) {
            $table->id();

            // Поле формы
            $table->foreignId('form_field_id')
                ->constrained('form_fields')
                ->cascadeOnDelete();

            // Локаль: ru, kk, en и т.д.
            $table->string('locale', 10);

            // Название поля
            // Например: "Ваше имя"
            $table->string('label')
                ->nullable();

            // Placeholder
            // Например: "Введите ваше имя"
            $table->string('placeholder')
                ->nullable();

            // Дополнительное описание / подсказка под полем
            $table->text('description')
                ->nullable();

            $table->timestamps();

            // Один перевод каждой локали для одного поля
            $table->unique(['form_field_id', 'locale']);

            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_field_translations');
    }
};
