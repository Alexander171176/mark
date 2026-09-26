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
        Schema::create('form_translations', function (Blueprint $table) {
            $table->id();

            // Форма
            $table->foreignId('form_id')
                ->constrained('forms')
                ->cascadeOnDelete();

            // Локаль перевода: ru, kk, en и т.д.
            $table->string('locale', 10);

            // Название формы
            $table->string('title')
                ->nullable();

            // Подзаголовок
            $table->string('subtitle')
                ->nullable();

            // Описание формы
            $table->text('description')
                ->nullable();

            // Текст кнопки отправки
            $table->string('submit_text')
                ->nullable();

            // Сообщение после успешной отправки
            $table->text('success_message')
                ->nullable();

            // Сообщение при ошибке отправки
            $table->text('error_message')
                ->nullable();

            $table->timestamps();

            // Для одной формы может существовать
            // только один перевод каждой локали
            $table->unique(['form_id', 'locale']);

            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_translations');
    }
};
