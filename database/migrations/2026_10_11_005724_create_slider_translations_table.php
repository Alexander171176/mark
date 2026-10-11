
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_translations', function (Blueprint $table) {

            // Основные данные перевода.
            $table->id(); // Уникальный идентификатор перевода.

            $table->foreignId('slider_id')
                ->constrained('sliders')
                ->cascadeOnDelete(); // Слайдер, которому принадлежит перевод.

            $table->string('locale', 12); // Код языка: ru, en, kk и другие.

            // Переводимые текстовые поля.
            $table->string('title')
                ->nullable(); // Название слайдера на выбранном языке.

            $table->string('subtitle')
                ->nullable(); // Дополнительный подзаголовок слайдера.

            $table->text('description')
                ->nullable(); // Описание слайдера на выбранном языке.

            // SEO-поля.
            $table->string('meta_title')
                ->nullable(); // SEO-заголовок слайдера.

            $table->text('meta_description')
                ->nullable(); // SEO-описание слайдера.

            // Системные поля.
            $table->timestamps(); // Даты создания и изменения перевода.

            // Индексы.
            $table->unique(
                ['slider_id', 'locale'],
                'slider_translations_slider_locale_unique'
            ); // Один перевод для каждого языка одного слайдера.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_translations');
    }
};
