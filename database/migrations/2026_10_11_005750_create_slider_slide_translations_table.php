
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slide_translations', function (Blueprint $table) {

            // Основные данные перевода.
            $table->id(); // Уникальный идентификатор перевода.

            $table->foreignId('slider_slide_id')
                ->constrained('slider_slides')
                ->cascadeOnDelete(); // Слайд, которому принадлежит перевод.

            $table->string('locale', 12); // Код языка: ru, kk, en и другие.

            // Переводимые текстовые поля слайда.
            $table->string('label')
                ->nullable(); // Короткая надпись над заголовком.

            $table->string('title')
                ->nullable(); // Основной заголовок слайда.

            $table->string('accent')
                ->nullable(); // Акцентная часть заголовка.

            $table->longText('description')
                ->nullable(); // Описание или основной текст слайда.

            // Системные поля.
            $table->timestamps(); // Даты создания и изменения перевода.

            // Уникальность перевода.
            $table->unique(
                ['slider_slide_id', 'locale'],
                'slider_slide_translation_locale_unique'
            ); // Один перевод на каждый язык конкретного слайда.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slide_translations');
    }
};
