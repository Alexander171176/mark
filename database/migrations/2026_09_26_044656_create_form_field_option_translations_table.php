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
        Schema::create('form_field_option_translations', function (Blueprint $table) {
            $table->id();

            // Вариант поля
            $table->foreignId('form_field_option_id')
                ->constrained('form_field_options')
                ->cascadeOnDelete();

            // Локаль: ru, kk, en и т.д.
            $table->string('locale', 10);

            // Отображаемое название варианта
            $table->string('label');

            // Дополнительное описание варианта.
            //
            // Может использоваться, например,
            // для карточек выбора с пояснением.
            $table->text('description')
                ->nullable();

            $table->timestamps();

            // Один перевод каждой локали
            // для одного варианта
            $table->unique(
                ['form_field_option_id', 'locale'],
                'ffot_option_locale_unique'
            );

            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_field_option_translations');
    }
};
