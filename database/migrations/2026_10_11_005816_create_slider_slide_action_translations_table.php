
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slide_action_translations', function (Blueprint $table) {

            // Основные данные перевода.
            $table->id(); // Уникальный идентификатор перевода действия.

            $table->foreignId('slider_slide_action_id')
                ->constrained('slider_slide_actions')
                ->cascadeOnDelete(); // Кнопка или действие, которому принадлежит перевод.

            $table->string('locale', 12); // Код языка: ru, kk, en и другие.

            // Переводимые текстовые поля.
            $table->string('label', 255); // Основной текст кнопки.

            $table->string('title', 255)
                ->nullable(); // Дополнительная подсказка при наведении на кнопку.

            $table->string('aria_label', 255)
                ->nullable(); // Доступное название кнопки для скринридеров.

            // Системные поля.
            $table->timestamps(); // Даты создания и последнего изменения перевода.

            // Уникальность перевода.
            $table->unique(
                ['slider_slide_action_id', 'locale'],
                'slider_action_translation_locale_unique'
            ); // Один перевод каждого языка для конкретной кнопки.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slide_action_translations');
    }
};
