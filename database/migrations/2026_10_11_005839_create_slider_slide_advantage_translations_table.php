
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slider_slide_advantage_translations', function (Blueprint $table) {

            // Основные данные перевода.
            $table->id(); // Уникальный идентификатор перевода преимущества.

            $table->unsignedBigInteger('slider_slide_advantage_id')
                ->comment('Преимущество, которому принадлежит перевод.');

            // Внешний ключ с коротким именем.
            $table->foreign(
                'slider_slide_advantage_id',
                'slider_advantage_translations_fk'
            )
                ->references('id')
                ->on('slider_slide_advantages')
                ->cascadeOnDelete();

            // Язык перевода.
            $table->string('locale', 12); // ru, kk, en и другие.

            // Переводимые поля.
            $table->string('title', 255)
                ->nullable(); // Заголовок преимущества.

            $table->text('text')
                ->nullable(); // Описание преимущества.

            // Системные поля.
            $table->timestamps();

            // Уникальность перевода.
            $table->unique(
                ['slider_slide_advantage_id', 'locale'],
                'slider_advantage_translation_locale_unique'
            ); // Один перевод для каждого языка.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slider_slide_advantage_translations');
    }
};
